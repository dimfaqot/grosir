<?php

namespace App\Controllers;

class Transaksi extends BaseController
{
    function __construct()
    {
        if (!session('id')) {
            session()->setFlashdata('gagal', "Ligin first");
            header("Location: " . base_url());
            die;
        }
    }
    public function index(): string
    {

        $data = db(menu()['tabel'])->orderBy("tgl", "DESC")->get()->getResultArray();
        return view(menu()['controller'] . '/' . menu()['controller'] . "_" . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }

    public function bayar()
    {
        $super_total = json_decode(json_encode($this->request->getVar('super_total')), true);
        $customer = json_decode(json_encode($this->request->getVar('customer')), true);
        $datas = json_decode(json_encode($this->request->getVar('datas')), true);
        $uang = angka_to_int(clear($this->request->getVar('uang')));
        $hutang = clear($this->request->getVar('hutang'));

        if ($uang < $super_total['biaya']) {
            gagal_js("Uang kurang");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $no_nota = next_invoice();
        $no_nota_hutang = next_invoice('hutang');
        $tgl = time();

        foreach ($datas as $i) {
            $barang = db('barang')->where('id', $i['id'])->get()->getRowArray();
            if (!$barang) {
                gagal_js("Id " . $i['barang'] . " not found");
            }

            // Update stok
            if ($barang['tipe'] !== "Mix") {
                if (!db('barang')->where('id', $barang['id'])->update(['qty' => $barang['qty'] - (int)$i['qty']])) {
                    gagal_js("Update stok gagal");
                }
            }

            // Data transaksi
            $transaksiData = [
                "tgl" => $tgl,
                "jenis" => $i['jenis'],
                "barang" => $i['barang'],
                "tipe" => $i['tipe'],
                "barang_id" => $i['id'],
                "harga" => $i['harga'],
                "qty" => $i['qty'],
                "total" => $i['total'],
                "diskon" => $i['diskon'],
                "biaya" => $i['biaya'],
                "petugas" => user()['nama'],
                "customer_id" => $customer['id'],
                "customer" => $customer['customer']
            ];

            // Insert ke tabel sesuai kondisi hutang
            $table = $hutang ? 'hutang' : 'nota';

            if ($hutang == "") {
                if (!$db->table('transaksi')->insert($transaksiData)) {
                    gagal_js("Insert transaksi gagal");
                }
                $transaksiData['uang'] = $uang;
            }

            $transaksiData['no_nota'] = $hutang ? $no_nota_hutang : $no_nota;

            if (!$db->table($table)->insert($transaksiData)) {
                gagal_js("Insert $table gagal");
            }
        }

        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses", str_replace("/", "-", $no_nota))
            : gagal_js("Gagal");
    }



    public function cari_user()
    {
        $text = clear($this->request->getVar("text"));
        $roles = json_decode(json_encode($this->request->getVar("roles")), true);
        $data = db('user')->whereIn('role', $roles)->like("nama", $text, "both")->orderBy('nama', 'ASC')->limit(7)->get()->getResultArray();

        sukses_js("Ok", $data);
    }
    public function cari_customer()
    {
        $text = clear($this->request->getVar("text"));
        $data = db('customer')->like("customer", $text, "both")->orderBy('customer', 'ASC')->limit(7)->get()->getResultArray();

        sukses_js("Ok", $data);
    }
    public function cari_barang()
    {
        $text = clear($this->request->getVar("text"));
        $jenis = json_decode(json_encode($this->request->getVar("jenis")), true);
        $data = db('barang')->whereIn('jenis', $jenis)->like("barang", $text, "both")->orderBy('barang', 'ASC')->limit(7)->get()->getResultArray();

        sukses_js("Ok", $data);
    }

    public function list()
    {
        $tahun = clear($this->request->getVar('tahun'));
        $bulan = clear($this->request->getVar('bulan'));
        $jenis = clear($this->request->getVar('jenis'));

        // Query total biaya
        $total = db(strtolower($jenis))
            ->selectSum('biaya')
            ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
            ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
            ->get()
            ->getRowArray();


        // Query data detail
        $data = db(strtolower($jenis))
            ->select('*')
            ->where("MONTH(FROM_UNIXTIME(tgl))", $bulan)
            ->where("YEAR(FROM_UNIXTIME(tgl))", $tahun)
            ->orderBy('tgl', 'DESC')
            ->get()
            ->getResultArray();


        sukses_js("Ok", $data, $total['biaya']);
    }
}
