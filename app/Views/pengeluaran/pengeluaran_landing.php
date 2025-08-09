<?= $this->extend('templates/logged') ?>

<?= $this->section('content') ?>

<div class="input-group input-group-sm mb-2">
    <input type="text" class="form-control bg-dark text-light border-secondary cari_card" placeholder="Cari..." aria-label="Recipient's username" aria-describedby="button-addon2">
    <button class="btn btn-outline-light form_input" data-order="Add" type="button"><i class="fa-solid fa-circle-plus"></i> <?= menu()['menu']; ?></button>
</div>
<?php foreach ($data as $k => $i): ?>
    <div class="card text-bg-dark mb-3" data-menu="<?= $i['barang']; ?>">
        <div class="card-header"><?= ($k + 1) . ". " . $i['barang']; ?></div>
        <div class="card-body d-flex justify-content-between ps-4">
            <div class="text-secondary"><small><?= ($i['jenis'] == "Kulakan" ? date("d/m/Y", $i['tgl']) . " [" . angka($i['harga']) . "] [" . angka($i['qty']) . "]" : date("d/m/Y", $i['tgl']) . " [" . angka($i['harga']) . "]"); ?></small></div>
            <div>
                <button class="btn btn-sm btn-light me-2 form_input" data-order="Edit" data-id="<?= $i['id']; ?>">Edit</button>
                <button class="btn btn-sm btn-danger delete" data-id="<?= $i['id']; ?>" data-message="Yakin hapus data ini?" data-tabel="<?= menu()['tabel']; ?>" data-is_reload="reload">Delete</button>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?= view("templates/search_barang", ["order" => ['Makanan', 'Minuman', 'Snack', "Lainnya"]]); ?>
<script>
    let form_input = (order, id) => {
        let toko = <?= json_encode(toko()); ?>;
        let data = {};
        if (order == "Edit") {
            let val = <?= json_encode($data); ?>;
            val.forEach(e => {
                if (e.id == id) {
                    data = e;
                    return;
                }
            });
        }
        let html = `
                <div class="form-floating mb-3">
                        <input type="text" name="pj" ${(order=="Edit"?'value="'+data.pj+'"':"")} class="form-control bg-dark text-light" data-order="${order}" data-id="${id}" placeholder="Pj" required>
                        <label class="text-secondary">Pj</label>
                    </div>
                <div class="form-floating mb-3">
                        <input type="text" name="barang" ${(order=="Edit"?'value="'+data.barang+'"':"")} class="form-control bg-dark border-warning text-light text-light barang" data-order="${order}" data-id="${id}" placeholder="Barang" readonly required>
                        <label class="text-secondary">Barang</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="harga" ${(order=="Edit"?'value="'+angka(data.harga)+'"':"")} class="form-control bg-dark text-light angka harga cari_biaya" placeholder="Harga" required>
                        <label class="text-secondary">Harga</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="qty" ${(order=="Edit"?'value="'+angka(data.qty)+'"':"")} class="form-control bg-dark text-light angka qty cari_biaya" placeholder="Qty" required>
                        <label class="text-secondary">Qty</label>
                    </div>
                    <div class="form-floating mb-3">
                    <input type="text" name="diskon" ${(order=="Edit"?'value="'+angka(data.diskon)+'"':"")} class="form-control bg-dark text-light angka diskon cari_biaya" placeholder="Diskon" required>
                    <label class="text-secondary">Diskon</label>
                    </div>
                    <div class="form-floating mb-3">`;
        html += `<select class="form-select bg-dark text-light border-secondary rounded" name="toko_id" required>`;
        if (order == "Add") {
            html += `<option selected value="" required>Pilih Toko</option>`;
        }
        toko.forEach(e => {
            html += `<option ${(e.toko==data.toko?"selected":"")} value="${e.id}">${e.toko}</option>`;

        })
        html += `</select>
                        <label class="text-secondary">Toko</label>
                    </div>`;
        html += `<div class="form-floating mb-3">
                       <input type="text" name="total" ${(order=="Edit"?'value="'+angka(data.total)+'"':"")} class="form-control bg-dark border border-warning text-light total" placeholder="Total" required readonly>
                       <label class="text-secondary">Total</label>
                   </div>
                    <div class="form-floating mb-3">
                    <input type="text" name="biaya" ${(order=="Edit"?'value="'+angka(data.biaya)+'"':"")} class="form-control bg-dark border border-warning text-light biaya" placeholder="Biaya" required readonly>
                    <label class="text-secondary">Biaya</label>
                </div>`;
        if (order == "Edit") {
            html += `<input type="hidden" name="barang_id" value="${data.barang_id}">`;
            html += `<input type="hidden" name="id" value="${data.id}">`;
        }
        html += `<div class="d-grid">
                        <button type="submit" class="btn btn-outline-info">Simpan</button>
                    </div>`

        return html;
    }

    $(document).on('click', '.form_input', function(e) {
        e.preventDefault();
        loading();
        let order = $(this).data("order");
        let id = $(this).data("id");

        let html = build_html(order, "offcanvas");

        html += `<div class="container">
                        <form method="post" action="<?= base_url(menu()['controller'] . "/"); ?>${order.toLowerCase()}">`;
        html += form_input(order, id);
        html += `</form>
                    </div>`;

        $(".body_canvas").html(html);
        loading("close");
        canvas.show();
    });


    $(document).on('click', '.barang', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let order = $(this).data('order');

        let html = `<div class="container">
                        <div class="form-floating position-relative">
                            <input type="text" class="form-control bg-dark text-light cari_barang" data-id="${id}" data-order="${order}" placeholder="Cari...">
                            <label class="text-secondary">Cari Produk</label>
                            <div class="bg-dark text-light body_list_barang"></div>
                        </div>
                    </div>`;
        $(".body_modal").html(html);
        modal.show();

        $('#main_modal').on('shown.bs.modal', () => {
            $('.cari_barang').trigger('focus').select();
        });

    });

    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let text = $(this).val().toLowerCase();
        let id = $(this).data("id");
        let order = $(this).data("order");
        let body_class_list = $('.body_list_barang');
        let barangs = <?= json_encode(barang()); ?>;

        if (text.length > 0) {

            let matchedProducts = barangs.filter(product =>
                product.barang.toLowerCase().includes(text)
            );

            if (matchedProducts.length > 0) {
                let html = '';
                matchedProducts.forEach(e => {
                    html += `
                        <div class="list_barang" data-barang_id="${e.id}" data-order="${order}" data-id="${id}" data-beli="${e.beli}">
                            <div class="d-flex justify-content-between">
                                <span class="nama_barang_${e.id}">${e.barang}</span>
                                <span class="text-muted">${angka(e.harga)} [${angka(e.qty)}]</span>
                            </div>
                        </div>`;
                });

                body_class_list.html(html).show();
            } else {
                body_class_list.html('<div class="list_barang text-muted">No data found</div>').show();
            }
        } else {
            body_class_list.hide();
        }
    });


    $(document).on('click', '.list_barang', function(e) {
        const id = $(this).data("id");
        const order = $(this).data("order");
        const barang_id = $(this).data("barang_id");
        const beli = $(this).data("beli");
        const nama_barang = $(".nama_barang_" + barang_id).text();

        $(".barang").val(nama_barang);

        const input = $('input[name="barang_id"]');
        if (input.length) {
            // Jika sudah ada, ganti nilainya
            input.val(barang_id);
        } else {
            // Jika belum ada, tambahkan setelah elemen .barang
            $('.barang').after('<input name="barang_id" value="' + barang_id + '" type="hidden">');
        }

        // let harga = $(".harga").val();
        $(".harga").val(angka(beli));
        // let qty = $(".qty").val();
        $(".qty").val((1));
        // let diskon = $(".diskon").val();
        $(".diskon").val(0);
        // let total = $(".total").val();
        $(".total").val(0);
        // let biaya = $(".biaya").val();
        $(".biaya").val(0);


        $('.body_list_barang').html("");
        $('.body_list_barang').hide();
        modal.hide();
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

        $(".total").val(angka(harga * qty));

        $(".biaya").val(angka(((harga * qty) - diskon)));

    }

    $(document).on('keyup', '.cari_biaya', function(e) {
        e.preventDefault();
        biaya();
    });
</script>
<?= $this->endSection() ?>