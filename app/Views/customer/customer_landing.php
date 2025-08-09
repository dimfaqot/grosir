    <?= $this->extend('templates/logged') ?>

    <?= $this->section('content') ?>

    <div class="input-group input-group-sm mb-2">
        <input type="text" class="form-control bg-dark text-light border-secondary cari_card" placeholder="Cari..." aria-label="Recipient's username" aria-describedby="button-addon2">
        <button class="btn btn-outline-light form_input" data-order="Add" type="button"><i class="fa-solid fa-circle-plus"></i> <?= menu()['menu']; ?></button>
    </div>



    <?php foreach ($data as $k => $i): ?>
        <div class="card text-bg-dark mb-3" data-menu="<?= $i['customer']; ?>">
            <div class="card-header"><?= ($k + 1) . ". " . $i['customer']; ?></div>
            <div class="card-body d-flex justify-content-between ps-4">
                <div class="text-secondary"><small><?= $i['alamat'] . " [" . $i['pj'] . "]"; ?></small></div>
                <div>
                    <button class="btn btn-sm btn-success mx-1 btn_wa" data-customer_id="<?= $i['id']; ?>"><i class="fa-brands fa-whatsapp"></i></button>
                    <button class="btn btn-sm btn-light me-2 form_input" data-order="Edit" data-id="<?= $i['id']; ?>">Edit</button>
                    <button class="btn btn-sm btn-danger delete" data-id="<?= $i['id']; ?>" data-message="Yakin hapus data ini?" data-tabel="<?= menu()['tabel']; ?>" data-is_reload="reload">Delete</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        let form_input = (order, id) => {

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
            let html = `<div class="form-floating mb-3">
                        <input type="text" name="customer" ${(order=="Edit"?'value="'+data.customer+'"':"")} class="form-control bg-dark text-light" placeholder="Customer" required>
                        <label class="text-secondary">Customer</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="alamat" ${(order=="Edit"?'value="'+data.alamat+'"':"")} class="form-control bg-dark text-light" placeholder="Alamat" required>
                        <label class="text-secondary">Alamat</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" name="pj" ${(order=="Edit"?'value="'+data.pj+'"':"")} class="form-control bg-dark text-light" placeholder="Pj" required>
                        <label class="text-secondary">Pj</label>
                    </div>
                     <div class="form-floating mb-3">
                        <input type="text" name="wa" ${(order=="Edit"?'value="'+data.wa+'"':"")} class="form-control bg-dark text-light" placeholder="W.a" required>
                        <label class="text-secondary">W.a</label>
                    </div>`;
            if (order == "Edit") {
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
        $(document).on('click', '.btn_wa', function(e) {
            e.preventDefault();
            let user_id = $(this).data("user_id");
            let total_biaya = $(this).data("total_biaya");

            post("hutang/wa", {
                user_id
            }).then(res => {
                if (res.status == "200") {
                    let no_hp = "62";
                    no_hp += res.data2.wa.substring(1);

                    let text = "_Assalamualaikum Wr. Wb._%0a";
                    text += "Yth. *" + res.data2.nama + '*%0a%0a';
                    text += 'Tagihan Anda di Hayu Batea:%0a%0a';
                    text += '*No. -- Tgl -- Barang -- Harga -- Qty -- Total -- Diskon -- Biaya*%0a'

                    let x = 1;
                    res.data.forEach((e, i) => {
                        text += (x++) + '. ' + time_php_to_js(e.tgl) + ' - ' + e.barang + ' - ' + angka(e.harga) + ' - ' + angka(e.qty) + ' - ' + angka(e.total) + ' - ' + angka(e.diskon) + ' - ' + angka(e.biaya) + '%0a';

                    })
                    text += '%0a';
                    text += "*TOTAL: " + angka(total_biaya) + "*%0a%0a";
                    text += "*_Mohon segera dibayar njihhh..._*%0a";
                    text += "_Wassalamualaikum Wr. Wb._%0a%0a";
                    text += 'Petugas%0a%0a';
                    text += '<?= user()['nama']; ?>';
                    text += "%0a%0a";
                    text += "_(*)Pesan ini dikirim oleh sistem, jadi mohon maklum dan ampun tersinggung njih._";
                    text += "%0a%0a";
                    // text += "Info lebih lengkap klik: %0a%0a";
                    // text += jwt;
                    loading("close");

                    // let url = "https://api.whatsapp.com/send/?phone=" + no_hp + "&text=" + text;
                    let url = "whatsapp://send/?phone=" + no_hp + "&text=" + text;

                    location.href = url;
                }
            })


        });
    </script>
    <?= $this->endSection() ?>