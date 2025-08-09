<?php

namespace App\Controllers;

class Hutang extends BaseController
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

        $data = db('hutang')
            ->select('*')
            ->selectSum('biaya', 'total_biaya')
            ->groupBy('customer_id')
            ->get()
            ->getResultArray();

        return view(menu()['controller'] . '/' . menu()['controller'] . "_" . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }

    public function detail()
    {
        $customer_id = clear($this->request->getVar('customer_id'));

        $data = db('hutang')->where('customer_id', $customer_id)->orderBy('tgl', "ASC")->get()->getResultArray();

        sukses_js("Ok", $data);
    }
    public function wa()
    {
        $customer_id = clear($this->request->getVar('customer_id'));
        $customer = db('customer')->where('id', $customer_id)->get()->getRowArray();

        if (!$customer) {
            gagal_js("Customer not found");
        }

        $data = db('hutang')->where('customer_id', $customer_id)->orderBy('tgl', "ASC")->get()->getResultArray();

        sukses_js("Ok", $data, $customer);
    }
    public function kasir()
    {
        $customer_id = clear($this->request->getVar('customer_id'));

        $data = db('hutang')->where('customer_id', $customer_id)->orderBy('tgl', "ASC")->get()->getResultArray();

        $super_total = ['total' => 0, "diskon" => 0, "biaya" => 0];
        foreach ($data as $i) {
            $super_total['total'] += (int)$i['total'];
            $super_total['diskon'] += (int)$i['diskon'];
            $super_total['biaya'] += (int)$i['biaya'];
        }

        sukses_js("Ok", $super_total);
    }

    public function bayar()
    {
        $customer_id = clear($this->request->getVar('customer_id'));
        $uang = angka_to_int(clear($this->request->getVar('uang')));
        $biaya = angka_to_int(clear($this->request->getVar('biaya')));
        $data = db('hutang')->where('customer_id', $customer_id)->orderBy('tgl', "ASC")->get()->getResultArray();

        if ($uang < $biaya) {
            gagal_js("Uang kurang");
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $no_nota = next_invoice();

        $tgl = time();

        foreach ($data as $i) {
            $id = $i['id'];
            unset($i['id']);
            $i['tgl'] = $tgl;
            $i['uang'] = $uang;
            $i['no_nota'] = $no_nota;
            // tambah uang
            if (!$db->table('nota')->insert($i)) {
                gagal_js("Insert nota gagal");
            }
            unset($i['no_nota']);
            unset($i['uang']);
            // no_nota hilang
            if (!$db->table('transaksi')->insert($i)) {
                gagal_js("Insert transaksi gagal");
            }


            if (!db('hutang')->where('id', $id)->delete()) {
                gagal_js($i['barang'] . " gagal dihapus");
            }
        }


        $db->transComplete();

        return $db->transStatus()
            ? sukses_js("Sukses", str_replace("/", "-", $no_nota))
            : gagal_js("Gagal");
    }
}
