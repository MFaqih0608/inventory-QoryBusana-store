<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "koneksi.php";

$nama     = mysqli_real_escape_string($conn, trim($_POST['nama_barang']));
$kategori = mysqli_real_escape_string($conn, trim($_POST['kategori']));
$harga    = (int) $_POST['harga'];
$stok     = (int) $_POST['stok'];

mysqli_query($conn, "INSERT INTO barang (nama_barang, kategori, harga, stok)
                     VALUES ('$nama', '$kategori', $harga, $stok)");

header("Location: barang.php?msg=simpan");
exit;
?>
