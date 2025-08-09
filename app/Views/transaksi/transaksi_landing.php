<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>
<div class="mb-3 text-warning text-center">
    <div class="mb-1">TOTAL</div>
    <input type="text" value="0" class="form-control super_total bg-warning fw-bold text-center text-dark border border-light border-3">
</div>

<div class="form-floating position-relative mb-2">
    <input type="text" class="form-control bg-dark text-light cari_customer" placeholder="Cari..." autofocus>
    <label class="text-secondary">Cari Customer</label>
    <div class="bg-dark text-light body_list_hasil position-absolute border border-secondary" style="width: 100%;z-index:3;">
    </div>
</div>

<div class="form-floating position-relative mb-2">
    <input type="text" class="form-control bg-dark text-light cari_barang" placeholder="Cari...">
    <label class="text-secondary">Cari Produk</label>
    <div class="bg-dark text-light body_list_barang position-absolute border border-secondary" style="width: 100%;z-index:3;">
    </div>
</div>
<div class="form-floating mb-2">
    <input type="text" class="form-control bg-dark text-light border border-warning harga" value="0" readonly>
    <label class="text-secondary">Harga</label>
</div>
<div class="form-floating mb-2">
    <input type="text" class="form-control bg-dark text-light qty angka cari_biaya" value="1">
    <label class="text-secondary">Qty</label>
</div>
<div class="form-floating mb-2">
    <input type="text" class="form-control bg-dark text-light diskon angka cari_biaya" value="0">
    <label class="text-secondary">Diskon</label>
</div>
<div class="form-floating mb-3">
    <input type="text" class="form-control bg-dark text-light border border-warning total" value="0" readonly>
    <label class="text-secondary">Total</label>
</div>
<div class="form-floating mb-3">
    <input type="text" class="form-control bg-secondary opacity-50 text-light fw-bold border border-warning biaya" value="0" readonly>
    <label class="text-light">Biaya</label>
</div>


<div class="d-flex gap-2">
    <div class="flex-grow-1">
        <button class="btn btn-outline-warning tambah_barang" style="width: 100%;"><i class="fa-solid fa-box-open"></i> TAMBAH BARANG</button>
    </div>
    <div><button class="btn btn-outline-info next" style="width: 115px;"><i class="fa-solid fa-arrow-up-from-bracket"></i> NEXT</button></div>
</div>


<table class="table table-borderless text-light table-sm mt-4" style="font-size: 12px;">
    <thead>
        <tr>
            <td>#</td>
            <td>Barang</td>
            <td>Harga</td>
            <td>Qty</td>
            <td>Del</td>
        </tr>
    </thead>
    <tbody class="list_items">

    </tbody>
</table>

<script>
    let barangs = [];
    let datas = [];
    let barang_selected = {};
    let customer_selected = {};
    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let text = $(this).val().toLowerCase();
        let body_class_list = $('.body_list_barang');

        post("transaksi/cari_barang", {
            text,
            jenis: ["Makanan", "Minuman", "Snack", "Lainnya "]
        }, "No").then(res => {
            barangs = res.data;
            let barang_arr = res.data;

            if (barang_arr.length > 0) {
                let html = '';
                barang_arr.forEach(e => {
                    html += `
                            <div class="list_barang" data-id="${e.id}">
                                <div class="d-flex justify-content-between">
                                    <span>${e.barang}</span>
                                    <span class="text-muted">${angka(e.harga)} [${angka(e.qty)}]</span>
                                </div>
                            </div>`;
                });
                body_class_list.html(html).show();
            } else {
                body_class_list.html('<div class="list_hasil text-muted">No data found</div>').show();
            }
        })

    });

    const biaya = () => {
        let harga = $(".harga").val();
        harga = (harga == "" ? "0" : harga);
        harga = angka_to_int(harga);

        let qty = $(".qty").val();
        qty = (qty == "" ? "1" : qty);
        qty = angka_to_int(qty);

        let diskon = $(".diskon").val();
        diskon = (diskon == "" ? "0" : diskon);
        diskon = angka_to_int(diskon);


    }

    $(document).on('click', '.list_barang', function(e) {
        e.preventDefault();
        const id = $(this).data("id");

        let id_exist = [];
        datas.forEach(e => {
            if (e.id == id) {
                id_exist.push(e);
            }
        })

        if (id_exist.length > 0) {
            message("400", "Barang existed");
            return;
        }

        let val = {};
        barangs.forEach(e => {
            if (e.id == id) {
                val = e;
            }
        })

        if (parseInt(val.qty) < 1) {
            message("400", "Stok kosong");
            return;
        }

        $(".harga").val(angka(val.harga));
        $(".total").val(angka(val.harga * 1));
        $(".biaya").val(angka(val.harga * 1));
        $(".cari_barang").val(val.barang);

        $('.body_list_barang').html("");
        $('.body_list_barang').hide();
        barang_selected = val;
    });

    const blink = (cls, duration = 2000, interval = 300) => {
        let el = $("." + cls);
        let isOn = false;

        const blinkInterval = setInterval(() => {
            el.toggleClass("bg-dark bg-danger");
            isOn = !isOn;
        }, interval);

        // Hentikan blinking setelah `duration` ms
        setTimeout(() => {
            clearInterval(blinkInterval);
            el.removeClass("bg-danger").addClass("bg-dark"); // Reset ke awal
        }, duration);
    };

    const clear_input = () => {
        $(".cari_barang").val("");
        $(".harga").val("0");
        $(".qty").val("1");
        $(".diskon").val("0");
        $(".total").val("0");
        $(".biaya").val("0");
    }

    const super_total = () => {
        let total = 0;
        let diskon = 0;
        let biaya = 0;
        datas.forEach(e => {
            total += e.total;
            diskon += e.diskon;
            biaya += e.biaya;
        })

        let res = {
            total,
            diskon,
            biaya
        }
        return res;
    }

    const list_items = () => {
        let html = "";
        datas.forEach((e, i) => {
            html += `<tr>
                <td>${(i+1)}</td>
                <td>${e.barang}</td>
                <td>${angka(e.harga)}</td>
                <td>${angka(e.qty)}</td>
                <td><a href="" class="text-danger delete_item" data-barang_id="${e.id}" style="text-decoration:none"><i class="fa-solid fa-circle-xmark"></i></a></td>
            </tr>`;
        })

        return html;
    }

    $(document).on('click', '.delete_item', function(e) {
        e.preventDefault();
        let id = $(this).data("barang_id");

        let temp_datas = [];
        datas.forEach(e => {
            if (e.id != id) {
                temp_datas.push(e);
            }
        })

        datas = temp_datas;
        // let cb = cari_biaya();

        $(".list_items").html(list_items());
        $(".super_total").val(angka(super_total().biaya));
        $(".cari_barang").focus();
    });
    $(document).on('click', '.tambah_barang', function(e) {
        e.preventDefault();
        if ($(".cari_barang").val() == "") {
            message("400", "Barang kosong");
            return;
        }
        if (customer_selected.id == undefined) {
            message("400", "Customer kosong");
            return;
        }
        let cb = cari_biaya();
        if (parseInt(barang_selected.qty) < cb.qty) {
            blink("qty");
            message("400", "Stok kurang");
            return;
        }
        if (cb.diskon > (cb.harga * cb.qty)) {
            message("400", "Diskon over");
            blink('diskon');
            return;
        }
        barang_selected["harga"] = cb.harga;
        barang_selected["qty"] = cb.qty;
        barang_selected["total"] = (cb.harga * cb.qty);
        barang_selected["diskon"] = cb.diskon;
        barang_selected["biaya"] = (cb.harga * cb.qty) - cb.diskon;

        datas.push(barang_selected);

        $(".list_items").html(list_items());
        $(".super_total").val(angka(super_total().biaya));
        $(".cari_barang").focus();
        clear_input();
    });

    const cari_biaya = () => {
        let harga = $(".harga").val();
        harga = (harga == "" ? "0" : harga);
        harga = angka_to_int(harga);

        let qty = $(".qty").val();
        qty = (qty == "" ? "1" : qty);
        qty = angka_to_int(qty);

        let diskon = $(".diskon").val();
        diskon = (diskon == "" ? "0" : diskon);
        diskon = angka_to_int(diskon);
        let res = {
            harga,
            qty,
            diskon
        };

        return res;
    }

    $(document).on('keyup', '.cari_biaya', function(e) {
        e.preventDefault();
        let cb = cari_biaya();
        $(".total").val(angka(cb.harga * cb.qty));
        if (cb.diskon > (cb.harga * cb.qty)) {
            $(".biaya").val("- " + angka((cb.harga * cb.qty) - cb.diskon));
        } else {
            $(".biaya").val(angka((cb.harga * cb.qty) - cb.diskon));
        }
    });


    const next = (super_total) => {
        let html = ``;
        html += `<div class="border border-secondary rounded p-3">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" style="width: 100px;">SUB TOTAL</span>
                        <input type="text" class="form-control" value="${angka(super_total.total)}">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" style="width: 100px;">DISKON</span>
                        <input type="text" class="form-control"  value="${angka(super_total.diskon)}">
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text" style="width: 100px;">TOTAL</span>
                        <input type="text" class="form-control" value="${angka(super_total.biaya)}">
                    </div>
                   
                    <h6 class="text-center">UANG PEMBAYARAN</h6>
                    <input class="form-control form-control-lg text-light text-center border border-light border-3 bg-success uang_pembayaran angka" value="${angka(super_total.biaya)}" value="0" type="text">
                    
                    <div class="my-3 border border-light rounded p-3 d-flex justify-content-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input hutang" type="checkbox" role="switch">
                            <label class="form-check-label">HUTANG</label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-info btn_bayar" style="width:100%"><i class="fa-solid fa-arrow-right-to-bracket"></i> BAYAR</button>
                    </div>
                </div>`;

        return html;
    }

    $(document).on('click', '.next', function(e) {
        e.preventDefault();
        if (datas.length == 0) {
            message("400", "Barang kosong");
            return;
        }
        let html = build_html("TRANSAKSI", "offcanvas");
        html += next(super_total());

        $(".body_canvas").html(html);
        canvas.show();

        $('#main_canvas').on('shown.bs.offcanvas', function() {
            $('.uang_pembayaran').trigger('focus').select();
        });


    });
    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();
        let uang = $(".uang_pembayaran").val();
        let hutang = $('.hutang').prop('checked');
        uang = (uang == "" ? "0" : uang);
        uang = angka_to_int(uang);

        if (uang < super_total().biaya) {
            message("400", "Uang kurang");
            return;
        }

        post("transaksi/bayar", {
            uang,
            datas,
            hutang,
            super_total: super_total(),
            customer: customer_selected
        }, "no").then(res => {
            // loading("close");
            message(res.status, res.message);
            if (res.status == "200") {
                if (hutang === true) {
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    setTimeout(() => {
                        const no_nota = res.data; // pastikan backend mengembalikan string no_nota
                        const iframe_url = `<?= base_url(); ?>guest/nota/${no_nota}`;

                        let html = build_html("INVOICE", "modal", ["judul", "garis"]);
                        html += `<iframe id="nota_frame" src="${iframe_url}" style="border: none; width: 100%; height: 600px;"></iframe>`;
                        html += `
                <div class="d-grid mt-5">
                    <button class="btn btn-secondary selesai">Selesai</button>
                </div>
            `;

                        $(".body_modal_static").html(html);
                        modal_static.show();

                    }, 1200);
                }
            }
        })

    });

    $(document).on('click', '.selesai', function(e) {
        e.preventDefault();
        location.reload();
    });



    $(document).on('keyup', '.cari_customer', function(e) {
        e.preventDefault();
        let text = $(this).val().toLowerCase();
        let body_class_list = $('.body_list_hasil');

        if (text == "") {
            body_class_list.html('').hide();
            return;
        }

        post("transaksi/cari_customer", {
            text
        }, "No").then(res => {
            let users = res.data;

            if (users.length > 0) {
                let html = '';
                users.forEach(e => {
                    html += `
                            <div class="list_hasil" data-hasil_id="${e.id}" data-customer="${e.customer}">
                                <div class="d-flex justify-content-between">
                                    <span>${e.customer}</span>
                                    <span class="text-muted">${e.pj} [${e.wa}]</span>
                                </div>
                            </div>`;
                });
                body_class_list.html(html).show();
            } else {
                body_class_list.html('<div class="list_hasil text-muted">No data found</div>').show();
            }
        })
    });


    $(document).on('click', '.list_hasil', function(e) {
        e.preventDefault();
        let customer = $(this).data("customer");
        let customer_id = $(this).data("hasil_id");
        $(".cari_customer").val(customer);
        customer_selected['id'] = customer_id;
        customer_selected['customer'] = customer;
        $(".body_list_hasil").html("");
    });
</script>


<?= $this->endSection() ?>