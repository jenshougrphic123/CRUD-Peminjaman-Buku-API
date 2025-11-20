<?php
include 'config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM buku WHERE id=$id");
header("Location: index.php");
?>
