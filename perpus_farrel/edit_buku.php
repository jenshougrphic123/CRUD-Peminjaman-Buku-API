<?php
include 'config.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM buku WHERE id=$id")->fetch_assoc();

if($_POST){
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    $conn->query("UPDATE buku SET judul='$judul', penulis='$penulis', tahun=$tahun, stok=$stok WHERE id=$id");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Buku</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Edit Buku</h2>
<form method="POST">
    <input type="text" name="judul" value="<?= $result['judul'] ?>" required>
    <input type="text" name="penulis" value="<?= $result['penulis'] ?>" required>
    <input type="number" name="tahun" value="<?= $result['tahun'] ?>" required>
    <input type="number" name="stok" value="<?= $result['stok'] ?>" required>
    <button type="submit">Simpan</button>
</form>
</body>
</html>
