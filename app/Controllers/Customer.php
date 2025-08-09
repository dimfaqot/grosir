<?php

namespace App\Controllers;

class Customer extends BaseController
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
        $data = db(menu()['tabel'])->orderBy("customer", "ASC")->get()->getResultArray();
        return view(menu()['controller'] . '/' . menu()['controller'] . "_" . 'landing', ['judul' => menu()['menu'], "data" => $data]);
    }
    public function add()
    {
        $input = [
            'customer'       => upper_first(clear($this->request->getVar('customer'))),
            'alamat'       => upper_first(clear($this->request->getVar('alamat'))),
            'pj'       => upper_first(clear($this->request->getVar('pj'))),
            'wa'       => upper_first(clear($this->request->getVar('wa')))
        ];

        // Simpan data  
        db(menu()['tabel'])->insert($input)
            ? sukses(base_url(menu()['controller']), 'Sukses')
            : gagal(base_url(menu()['controller']), 'Gagal');
    }

    public function edit()
    {
        $id = clear($this->request->getVar('id'));

        $q = db(menu()['tabel'])->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal(base_url(menu()['controller']), "Id not found");
        }

        $q = [
            'customer'       => upper_first(clear($this->request->getVar('customer'))),
            'alamat'       => upper_first(clear($this->request->getVar('alamat'))),
            'pj'       => upper_first(clear($this->request->getVar('pj'))),
            'wa'       => upper_first(clear($this->request->getVar('wa')))
        ];


        // Simpan data
        db(menu()['tabel'])->where('id', $id)->update($q)
            ? sukses(base_url(menu()['controller']), 'Sukses')
            : gagal(base_url(menu()['controller']), 'Gagal');
    }
}
