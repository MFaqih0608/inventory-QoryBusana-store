<?php
include "koneksi.php";

$id_kontak         = (int)$_POST['id_kontak'];
$jenis             = in_array($_POST['jenis'], ['hutang','piutang']) ? $_POST['jenis'] : 'hutang';
$total_tagihan     = (int)$_POST['total_tagihan'];
$bayar_awal        = (int)$_POST['bayar_awal'];
$tanggal           = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
$keterangan        = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
$catatan_bayar     = mysqli_real_escape_string($conn, trim($_POST['catatan_bayar'] ?? ''));

// Tentukan status awal
if ($bayar_awal <= 0)               $status = 'belum_bayar';
elseif ($bayar_awal >= $total_tagihan) $status = 'lunas';
else                                    $status = 'sebagian';

// Simpan transaksi utama
mysqli_query($conn,
    "INSERT INTO transaksi_hutang (id_kontak, jenis, total_tagihan, total_bayar, tanggal_transaksi, keterangan, status)
     VALUES ($id_kontak, '$jenis', $total_tagihan, $bayar_awal, '$tanggal', '$keterangan', '$status')");
$id_transaksi = mysqli_insert_id($conn);

// Simpan detail barang
$nama_barang  = $_POST['nama_barang'];
$qty          = $_POST['qty'];
$satuan       = $_POST['satuan'];
$harga_satuan = $_POST['harga_satuan'];

for ($i = 0; $i < count($nama_barang); $i++) {
    $nb  = mysqli_real_escape_string($conn, trim($nama_barang[$i]));
    $q   = (int)$qty[$i];
    $sat = mysqli_real_escape_string($conn, trim($satuan[$i]));
    $hs  = (int)$harga_satuan[$i];
    $sub = $q * $hs;
    if ($nb === '') continue;
    mysqli_query($conn,
        "INSERT INTO detail_hutang (id_transaksi, nama_barang, qty, satuan, harga_satuan, subtotal)
         VALUES ($id_transaksi, '$nb', $q, '$sat', $hs, $sub)");
}

// Simpan pembayaran awal jika ada
if ($bayar_awal > 0) {
    mysqli_query($conn,
        "INSERT INTO pembayaran_hutang (id_transaksi, jumlah_bayar, tanggal_bayar, catatan)
         VALUES ($id_transaksi, $bayar_awal, '$tanggal', '$catatan_bayar')");
}

header("Location: hutang.php?msg=simpan");
exit;
?>