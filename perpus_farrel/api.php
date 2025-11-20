<?php
include 'config.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$action = $_GET['action'] ?? '';

switch($action){
    case 'get_buku':
        $result = $conn->query("SELECT * FROM buku");
        $data = [];
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'get_peminjaman':
        $result = $conn->query("SELECT peminjaman.*, buku.judul FROM peminjaman JOIN buku ON peminjaman.id_buku = buku.id");
        $data = [];
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }
        echo json_encode($data);
        break;

case 'tambah_buku':
    $judul = $_POST['judul'] ?? '';
    $penulis = $_POST['penulis'] ?? '';
    $tahun = $_POST['tahun'] ?? 0;
    $stok = $_POST['stok'] ?? 0;

    $sql = "INSERT INTO buku (judul, penulis, tahun, stok) VALUES ('$judul','$penulis',$tahun,$stok)";
    
    if($conn->query($sql)){
        echo json_encode(["status"=>1]);
    } else {
        echo json_encode(["status"=>0, "error"=>$conn->error]);
    }
break;


    case 'hapus_buku':
    $id = $_POST['id'] ?? 0;
    if($conn->query("DELETE FROM buku WHERE id=$id")){
        echo json_encode(["status"=>1]);
    } else {
        echo json_encode(["status"=>0, "error"=>$conn->error]);
    }
break;


case 'edit_buku':
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', tahun=$tahun, stok=$stok WHERE id=$id";
    echo $conn->query($sql) ? json_encode(["status"=>1]) : json_encode(["status"=>0, "error"=>$conn->error]);
break;


    case 'pinjam_buku':

    $id_buku = intval($_POST['id_buku'] ?? 0);
    $nama = $conn->real_escape_string($_POST['nama'] ?? '');
    $tanggal = date("Y-m-d");

    // Validasi input
    if ($id_buku <= 0) {
        echo json_encode(["status"=>0, "error"=>"ID buku tidak valid"]);
        break;
    }

    if ($nama == '') {
        echo json_encode(["status"=>0, "error"=>"Nama peminjam tidak boleh kosong"]);
        break;
    }

    // Cek stok buku
    $cek = $conn->query("SELECT stok FROM buku WHERE id = $id_buku");
    if ($cek->num_rows == 0) {
        echo json_encode(["status"=>0, "error"=>"Buku tidak ditemukan"]);
        break;
    }

    $data = $cek->fetch_assoc();
    if ($data['stok'] <= 0) {
        echo json_encode(["status"=>0, "error"=>"Stok buku habis"]);
        break;
    }

    // Proses peminjaman
    $sql = "INSERT INTO peminjaman (id_buku, nama_peminjam, tanggal_pinjam, status) 
            VALUES ($id_buku, '$nama', '$tanggal', 'Dipinjam')";

    if ($conn->query($sql)) {

        // Kurangi stok
        $conn->query("UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");

        echo json_encode(["status"=>1, "message"=>"Peminjaman berhasil"]);
    } else {
        echo json_encode(["status"=>0, "error"=>$conn->error]);
    }
    break;


    case 'kembalikan_buku':

    $id_peminjaman = intval($_POST['id'] ?? 0);
    $tanggal = date("Y-m-d");

    if ($id_peminjaman <= 0) {
        echo json_encode(["status"=>0, "error"=>"ID peminjaman tidak valid"]);
        break;
    }

    // Ambil ID buku dari tabel peminjaman
    $cek = $conn->query("SELECT id_buku FROM peminjaman WHERE id = $id_peminjaman");
    if ($cek->num_rows == 0) {
        echo json_encode(["status"=>0, "error"=>"Data peminjaman tidak ditemukan"]);
        break;
    }

    $data = $cek->fetch_assoc();
    $id_buku = $data['id_buku'];

    // Update status peminjaman → Dikembalikan
    $sql = "UPDATE peminjaman 
            SET status = 'Dikembalikan', tanggal_kembali = '$tanggal' 
            WHERE id = $id_peminjaman";

    if ($conn->query($sql)) {

        // Tambahkan stok buku kembali
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id = $id_buku");

        echo json_encode(["status"=>1, "message"=>"Buku berhasil dikembalikan"]);
    } else {
        echo json_encode(["status"=>0, "error"=>$conn->error]);
    }

    break;


    default:
        echo json_encode(["status"=>0,"message"=>"Action tidak dikenali"]);
}
?>
