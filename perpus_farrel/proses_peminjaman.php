<?php
include 'config.php';

// Pastikan request menggunakan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form, pastikan aman
    $id_buku = isset($_POST['id_buku']) ? intval($_POST['id_buku']) : 0;
    $nama_peminjam = isset($_POST['nama']) ? $conn->real_escape_string($_POST['nama']) : '';
    $tanggal_pinjam = date("Y-m-d");

    // Validasi input
    if ($id_buku <= 0) {
        echo "ID buku tidak valid!";
        exit;
    }

    if (empty($nama_peminjam)) {
        echo "Nama peminjam tidak boleh kosong!";
        exit;
    }

    // Cek apakah buku ada
    $cek = $conn->query("SELECT stok FROM buku WHERE id = $id_buku");
    if ($cek->num_rows === 0) {
        echo "Buku tidak ditemukan!";
        exit;
    }

    $data = $cek->fetch_assoc();

    // Cek stok tersedia
    if ($data['stok'] <= 0) {
        echo "Stok buku habis! Tidak bisa dipinjam.";
        exit;
    }

    // Masukkan data peminjaman
    $sql = "INSERT INTO peminjaman (id_buku, nama_peminjam, tanggal_pinjam, status) 
            VALUES ($id_buku, '$nama_peminjam', '$tanggal_pinjam', 'Dipinjam')";

    if ($conn->query($sql)) {

        // Kurangi stok buku
        $updateStok = "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku";
        $conn->query($updateStok);

        // Redirect setelah sukses
        header("Location: index.php?pesan=peminjaman_berhasil");
        exit;

    } else {
        echo "Gagal meminjam buku: " . $conn->error;
    }

} else {
    echo "Request tidak valid!";
}
?>
