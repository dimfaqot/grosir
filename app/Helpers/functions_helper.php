<?php

function db($tabel, $db = null)
{
    if ($db == null || $db == 'batea') {
        $db = \Config\Database::connect();
    } else {
        $db = \Config\Database::connect(strtolower(str_replace(" ", "_", $db)));
    }
    $db = $db->table($tabel);

    return $db;
}

function clear($text)
{
    $text = trim($text);
    $text = htmlspecialchars($text);
    return $text;
}

function upper_first($text)
{
    $text = clear($text);
    $exp = explode(" ", $text);

    $val = [];
    foreach ($exp as $i) {
        $lower = strtolower($i);
        $val[] = ucfirst($lower);
    }

    return implode(" ", $val);
}

function sukses($url, $pesan)
{
    session()->setFlashdata('sukses', $pesan);
    header("Location: " . $url);
    die;
}

function gagal($url, $pesan)
{
    session()->setFlashdata('gagal', $pesan);
    header("Location: " . $url);
    die;
}

function gagal_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $res = [
        'status' => '400',
        'message' =>  $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($res);
    die;
}

function sukses_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $data = [
        'status' => '200',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($data);
    die;
}

function options($kategori)
{
    $q = db('options')->where("kategori", upper_first($kategori))->orderBy("value", "ASC")->get()->getResultArray();

    return $q;
}

function url()
{
    $url = service('uri');
    $res = $url->getPath();
    $res = str_replace("index.php/", "", $res);
    $res = explode("/", $res);
    $res = ($res[0] == "" ? $res[1] : $res[0]);
    return $res;
}

function user()
{
    $res = false;
    if (session('id')) {
        $res = db('user')->where('id', session('id'))->get()->getRowArray();
    }
    return $res;
}

function menus()
{

    $items = db('menu')
        ->where('role', (user() ? user()['role'] : "Public"))
        ->orderBy('urutan', 'ASC')
        ->orderBy('menu', 'ASC')
        ->get()
        ->getResultArray();

    $data = [];

    foreach ($items as $item) {
        $data[$item['grup']][] = $item;
    }

    // Jika perlu format seperti sebelumnya:
    $result = [];
    foreach ($data as $grup => $list) {
        $menus = [];

        foreach ($list as $i) {
            $menus[] = $i['controller'];
        }

        $result[] = ['grup' => $grup, 'data' => $list, "menus" => $menus];
    }

    return $result;
}

function menu($controller = null)
{
    $controller = ($controller == "" ? url() : $controller);
    $controller = ($controller == null ? url() : $controller);

    $q = db('menu')->where('role', (user() ? user()['role'] : "Public"))->where('controller', $controller)->get()->getRowArray();

    if (!$q) {
        gagal(base_url("home"), "Access denied");
    } else {
        return $q;
    }
}

function settings($nama)
{
    return db('settings')->where('nama', strtolower($nama))->get()->getRowArray();
}

function angka($uang)
{
    return number_format($uang, 0, ",", ".");
}

function angka_to_int($uang)
{
    $uang = str_replace("Rp. ", "", $uang);
    $uang = str_replace(".", "", $uang);
    return $uang;
}

function barang($jenis = null)
{
    $db = db('barang');
    if ($jenis !== null) {
        $db->whereIn("jenis", $jenis);
    }
    return $db->orderBy("barang", "ASC")->get()->getResultArray();
}

function random_string($length = 14)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function next_invoice($order = null)
{

    $db = db('nota');

    $year  = date('Y');
    $month = date('m');
    $prefix = "$year/$month/";

    // Cari no_nota terakhir berdasarkan bulan ini
    $lastNota = $db->select('no_nota')
        ->orderBy('tgl', 'DESC')
        ->get()
        ->getRowArray();


    $nextNumber = 1;
    if ($lastNota) {
        $parts = explode('/', $lastNota['no_nota']);
        $lastNumber = end($parts);
        $nextNumber = (int)$lastNumber + 1;
    }

    $nota = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    if ($order == "hutang") {
        $nota = $prefix . random_string(6);
    }

    return $nota;
}

function users($roles = null)
{
    $db = db('user');

    $db;
    if ($roles !== null) {
        $db->whereIn('role', $roles);
    }
    $q = $db->orderBy('nama', 'ASC')->get()->getResultArray();
    return $q;
}
function toko()
{
    return db('toko')->orderBy('toko', 'ASC')->get()->getResultArray();
}
