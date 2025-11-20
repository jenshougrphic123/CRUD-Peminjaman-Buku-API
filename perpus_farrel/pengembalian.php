<?php
include 'config.php';
$id = $_GET['id'];
$tanggal = date("Y-m-d");
$conn->query("UPDATE peminjaman SET status='Dikembalikan', tanggal_kembali='$tanggal' WHERE id=$id");
header("Location: index.php");
?>
