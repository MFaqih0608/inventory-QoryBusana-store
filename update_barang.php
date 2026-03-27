<?php
include "koneksi.php";

$id       = (int) $_POST['id'];
$nama     = mysqli_real_escape_string($conn, trim($_POST['nama_barang']));
$kategori = mysqli_real_escape_string($conn, trim($_POST['kategori']));
$harga    = (int) $_POST['harga'];
$stok     = (int) $_POST['stok'];

mysqli_query($conn, "UPDATE barang
                     SET nama_barang='$nama', kategori='$kategori', harga=$harga, stok=$stok
                     WHERE id_barang=$id");

header("Location: barang.php?msg=update");
exit;
?>
