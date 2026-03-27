<?php
include "koneksi.php";

// Hapus kontak
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kontak WHERE id_kontak = $id");
    header("Location: kontak.php?msg=hapus");
    exit;
}

// Simpan kontak baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $telepon     = mysqli_real_escape_string($conn, trim($_POST['telepon'] ?? ''));
    $keterangan  = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));

    if (isset($_POST['id_kontak']) && $_POST['id_kontak'] > 0) {
        $id = (int)$_POST['id_kontak'];
        mysqli_query($conn,
            "UPDATE kontak SET nama='$nama', telepon='$telepon', keterangan='$keterangan'
             WHERE id_kontak=$id");
        header("Location: kontak.php?msg=update");
    } else {
        mysqli_query($conn,
            "INSERT INTO kontak (nama, telepon, keterangan) VALUES ('$nama', '$telepon', '$keterangan')");
        header("Location: kontak.php?msg=simpan");
    }
    exit;
}

$pageTitle = "Kelola Kontak";
include "layout_top.php";

$data = mysqli_query($conn, "SELECT k.*,
    (SELECT COUNT(*) FROM transaksi_hutang t WHERE t.id_kontak = k.id_kontak) as total_transaksi,
    (SELECT COALESCE(SUM(total_tagihan - total_bayar),0) FROM transaksi_hutang t WHERE t.id_kontak = k.id_kontak AND t.status != 'lunas') as total_sisa
    FROM kontak k ORDER BY k.nama ASC");
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="border-radius:12px;font-size:13.5px;border:none;
     <?php echo $_GET['msg']=='hapus'?'background:rgba(239,68,68,.1);color:#991b1b;':'background:rgba(16,185,129,.1);color:#065f46;';?>">
    <i class="bi bi-<?php echo $_GET['msg']=='hapus'?'trash':'check-circle';?>-fill"></i>
    <?php
    if ($_GET['msg']=='simpan') echo 'Kontak berhasil ditambahkan.';
    elseif ($_GET['msg']=='update') echo 'Kontak berhasil diperbarui.';
    elseif ($_GET['msg']=='hapus') echo 'Kontak berhasil dihapus.';
    ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Form tambah/edit -->
    <div class="col-12 col-md-4">
        <div class="card">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-person-plus me-2 text-muted"></i>Tambah Kontak
            </div>
            <div class="card-body p-4">
                <form method="POST">
                    <input type="hidden" name="id_kontak" value="0">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" placeholder="08xx (opsional)">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Supplier, pelanggan, dll.">
                    </div>
                    <button type="submit" class="btn-accent" style="width:100%;">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar kontak -->
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-people me-2 text-muted"></i>Daftar Kontak
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Telepon</th>
                            <th>Transaksi</th>
                            <th>Total Sisa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($k = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($k['nama']); ?></strong>
                            <?php if ($k['keterangan']): ?>
                            <div style="font-size:11.5px;color:var(--text-muted);"><?php echo htmlspecialchars($k['keterangan']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;"><?php echo htmlspecialchars($k['telepon'] ?: '-'); ?></td>
                        <td style="font-size:13px;"><?php echo $k['total_transaksi']; ?> transaksi</td>
                        <td style="font-size:13px; font-weight:600; color:<?php echo $k['total_sisa']>0?'#dc2626':'var(--text-muted)';?>">
                            Rp <?php echo number_format($k['total_sisa'], 0, ',', '.'); ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="hutang.php?cari=<?php echo urlencode($k['nama']); ?>"
                                   class="btn-icon" style="color:#3b82f6;" title="Lihat transaksi">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="kontak.php?hapus=<?php echo $k['id_kontak']; ?>"
                                   class="btn-icon del" title="Hapus"
                                   onclick="return confirm('Hapus kontak <?php echo addslashes($k['nama']); ?>? Semua transaksinya ikut terhapus!')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "layout_bottom.php"; ?>