<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_inventory";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>
