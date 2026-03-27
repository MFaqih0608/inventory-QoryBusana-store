<?php
include "koneksi.php";

// Hapus barang — HARUS sebelum include layout (sebelum HTML keluar)
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang = $id");
    header("Location: barang.php?msg=hapus");
    exit;
}

$pageTitle = "Data Barang";
include "layout_top.php";

// Ambil keyword pencarian
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, trim($_GET['cari'])) : '';

// Query dengan atau tanpa pencarian
if ($cari !== '') {
    $data = mysqli_query($conn, "SELECT * FROM barang
                                  WHERE nama_barang LIKE '%$cari%'
                                  OR kategori LIKE '%$cari%'
                                  ORDER BY id_barang DESC");
} else {
    $data = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang DESC");
}

$total_hasil = mysqli_num_rows($data);
?>

<!-- Notifikasi -->
<?php if (isset($_GET['msg'])): ?>
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="border-radius:12px; font-size:13.5px; border:none;
     <?php echo $_GET['msg']=='simpan'
        ? 'background:rgba(16,185,129,.1); color:#065f46;'
        : ($_GET['msg']=='update'
            ? 'background:rgba(59,130,246,.1); color:#1e40af;'
            : 'background:rgba(239,68,68,.1); color:#991b1b;'); ?>">
    <i class="bi <?php echo $_GET['msg']=='hapus' ? 'bi-trash' : 'bi-check-circle'; ?>-fill"></i>
    <?php
        if ($_GET['msg'] == 'simpan')     echo 'Barang berhasil ditambahkan.';
        elseif ($_GET['msg'] == 'update') echo 'Barang berhasil diperbarui.';
        elseif ($_GET['msg'] == 'hapus')  echo 'Barang berhasil dihapus.';
    ?>
</div>
<?php endif; ?>

<div class="card">
    <!-- Card Header: judul + search + tombol tambah -->
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <span style="font-size:14px; font-weight:600;">
                <i class="bi bi-box-seam me-2 text-muted"></i>Daftar Semua Barang
            </span>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Form Search -->
                <form method="GET" action="barang.php" class="d-flex gap-2">
                    <div style="position:relative;">
                        <i class="bi bi-search"
                           style="position:absolute; left:10px; top:50%; transform:translateY(-50%);
                                  color:var(--text-muted); font-size:13px; pointer-events:none;"></i>
                        <input type="text" name="cari"
                               value="<?php echo htmlspecialchars($cari); ?>"
                               placeholder="Cari nama atau kategori..."
                               style="padding:7px 14px 7px 32px; border:1px solid var(--border);
                                      border-radius:9px; font-size:13px; font-family:'Plus Jakarta Sans',sans-serif;
                                      width:220px; outline:none; color:var(--text-main); background:var(--surface-2);"
                               onfocus="this.style.borderColor='#e94560'; this.style.boxShadow='0 0 0 3px rgba(233,69,96,.1)'"
                               onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none'">
                    </div>
                    <button type="submit"
                            style="padding:7px 14px; background:#e94560; color:#fff; border:none;
                                   border-radius:9px; font-size:13px; font-weight:600;
                                   font-family:'Plus Jakarta Sans',sans-serif; cursor:pointer;">
                        Cari
                    </button>
                    <?php if ($cari !== ''): ?>
                    <a href="barang.php"
                       style="padding:7px 12px; border:1px solid var(--border); border-radius:9px;
                              font-size:13px; color:var(--text-muted); text-decoration:none; background:var(--surface);">
                        <i class="bi bi-x"></i> Reset
                    </a>
                    <?php endif; ?>
                </form>

                <!-- Tombol Tambah -->
                <a href="tambah_barang.php" class="btn-accent"
                   style="text-decoration:none; font-size:12.5px; padding:7px 14px;
                          border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-plus-lg"></i> Tambah Barang
                </a>
            </div>

        </div>

        <!-- Info hasil pencarian -->
        <?php if ($cari !== ''): ?>
        <div style="margin-top:10px; font-size:12.5px; color:var(--text-muted);">
            Menampilkan <strong style="color:var(--text-main);"><?php echo $total_hasil; ?> hasil</strong>
            untuk pencarian "<strong style="color:var(--text-main);"><?php echo htmlspecialchars($cari); ?></strong>"
        </div>
        <?php endif; ?>
    </div>

    <!-- Tabel -->
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_hasil > 0):
                    $no = 1;
                    while ($d = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:13px;"><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($d['nama_barang']); ?></strong></td>
                    <td>
                        <span style="background:var(--surface-2); padding:3px 10px; border-radius:20px;
                                     font-size:12px; font-weight:500; border:1px solid var(--border);">
                            <?php echo htmlspecialchars($d['kategori']); ?>
                        </span>
                    </td>
                    <td>Rp <?php echo number_format($d['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($d['stok'] == 0): ?>
                            <span class="badge-stok empty">Habis</span>
                        <?php elseif ($d['stok'] <= 5): ?>
                            <span class="badge-stok low"><?php echo $d['stok']; ?> pcs</span>
                        <?php else: ?>
                            <span class="badge-stok ok"><?php echo $d['stok']; ?> pcs</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="edit_barang.php?id=<?php echo $d['id_barang']; ?>"
                               class="btn-icon edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="barang.php?hapus=<?php echo $d['id_barang']; ?>"
                               class="btn-icon del" title="Hapus"
                               onclick="return confirm('Yakin ingin menghapus <?php echo addslashes($d['nama_barang']); ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                    <?php endwhile;
                else: ?>
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
                        <?php if ($cari !== ''): ?>
                            <i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:8px; opacity:.4;"></i>
                            Tidak ada barang yang cocok dengan "<strong><?php echo htmlspecialchars($cari); ?></strong>"
                        <?php else: ?>
                            <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px; opacity:.4;"></i>
                            Belum ada data barang
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "layout_bottom.php"; ?>