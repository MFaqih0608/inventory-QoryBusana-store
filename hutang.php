<?php
include "koneksi.php";
$pageTitle = "Hutang & Piutang";
include "layout_top.php";

// Ringkasan
$total_hutang  = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(total_tagihan - total_bayar),0) FROM transaksi_hutang WHERE jenis='hutang' AND status != 'lunas'"))[0];
$total_piutang = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(total_tagihan - total_bayar),0) FROM transaksi_hutang WHERE jenis='piutang' AND status != 'lunas'"))[0];

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$cari   = isset($_GET['cari'])   ? mysqli_real_escape_string($conn, trim($_GET['cari'])) : '';

$where = "WHERE 1=1";
if ($filter === 'hutang')  $where .= " AND t.jenis = 'hutang'";
if ($filter === 'piutang') $where .= " AND t.jenis = 'piutang'";
if ($filter === 'lunas')   $where .= " AND t.status = 'lunas'";
if ($filter === 'belum')   $where .= " AND t.status != 'lunas'";
if ($cari !== '')           $where .= " AND (k.nama LIKE '%$cari%' OR t.keterangan LIKE '%$cari%')";

$data = mysqli_query($conn,
    "SELECT t.*, k.nama, k.telepon,
            (t.total_tagihan - t.total_bayar) AS sisa
     FROM transaksi_hutang t
     JOIN kontak k ON t.id_kontak = k.id_kontak
     $where
     ORDER BY t.tanggal_transaksi DESC");
?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.1); color:#ef4444;">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="stat-value" style="font-size:18px; color:#ef4444;">
                Rp <?php echo number_format($total_hutang, 0, ',', '.'); ?>
            </div>
            <div class="stat-label">Total Hutang Belum Lunas</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1); color:#10b981;">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="stat-value" style="font-size:18px; color:#10b981;">
                Rp <?php echo number_format($total_piutang, 0, ',', '.'); ?>
            </div>
            <div class="stat-label">Total Piutang Belum Lunas</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1); color:#3b82f6;">
                <i class="bi bi-calculator"></i>
            </div>
            <?php $selisih = $total_piutang - $total_hutang; ?>
            <div class="stat-value" style="font-size:18px; color:<?php echo $selisih >= 0 ? '#10b981' : '#ef4444'; ?>">
                Rp <?php echo number_format(abs($selisih), 0, ',', '.'); ?>
            </div>
            <div class="stat-label"><?php echo $selisih >= 0 ? 'Kamu lebih banyak punya tagihan' : 'Kamu lebih banyak berhutang'; ?></div>
        </div>
    </div>
</div>

<!-- Card tabel -->
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <span style="font-weight:600; font-size:14px;">
                <i class="bi bi-journal-text me-2 text-muted"></i>Daftar Transaksi
            </span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Filter tab -->
                <div style="display:flex; gap:4px; background:var(--surface-2); border-radius:9px; padding:3px; border:1px solid var(--border);">
                    <?php
                    $tabs = ['semua'=>'Semua','hutang'=>'Hutang','piutang'=>'Piutang','belum'=>'Belum Lunas','lunas'=>'Lunas'];
                    foreach ($tabs as $key => $label):
                        $active = $filter === $key;
                    ?>
                    <a href="hutang.php?filter=<?php echo $key; ?>&cari=<?php echo urlencode($cari); ?>"
                       style="padding:5px 12px; border-radius:7px; font-size:12.5px; font-weight:<?php echo $active?'600':'500';?>;
                              text-decoration:none; transition:all .2s;
                              background:<?php echo $active?'#e94560':'transparent';?>;
                              color:<?php echo $active?'#fff':'var(--text-muted)';?>;">
                        <?php echo $label; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <!-- Search -->
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                    <div style="position:relative;">
                        <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px;pointer-events:none;"></i>
                        <input type="text" name="cari" value="<?php echo htmlspecialchars($cari); ?>"
                               placeholder="Cari nama..."
                               style="padding:7px 14px 7px 32px;border:1px solid var(--border);border-radius:9px;
                                      font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;width:180px;
                                      outline:none;color:var(--text-main);background:var(--surface-2);"
                               onfocus="this.style.borderColor='#e94560'"
                               onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <button type="submit" style="padding:7px 12px;background:#e94560;color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;">Cari</button>
                </form>
                <!-- Tombol tambah -->
                <a href="tambah_hutang.php" class="btn-accent"
                   style="text-decoration:none;font-size:12.5px;padding:7px 14px;border-radius:8px;display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Barang</th>
                    <th>Total</th>
                    <th>Sudah Bayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($data) > 0):
                while ($d = mysqli_fetch_assoc($data)):
                    // Ambil ringkasan barang
                    $barang_list = mysqli_query($conn,
                        "SELECT nama_barang, qty FROM detail_hutang WHERE id_transaksi = {$d['id_transaksi']} LIMIT 2");
                    $barang_str = '';
                    $items = [];
                    while ($b = mysqli_fetch_assoc($barang_list)) $items[] = $b['nama_barang'].' ('.$b['qty'].')';
                    $total_item = mysqli_fetch_row(mysqli_query($conn,
                        "SELECT COUNT(*) FROM detail_hutang WHERE id_transaksi = {$d['id_transaksi']}"))[0];
                    $barang_str = implode(', ', $items);
                    if ($total_item > 2) $barang_str .= ' +'.($total_item-2).' lainnya';
            ?>
            <tr>
                <td style="font-size:12.5px; white-space:nowrap;">
                    <?php echo date('d M Y', strtotime($d['tanggal_transaksi'])); ?>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($d['nama']); ?></strong>
                    <?php if ($d['telepon']): ?>
                    <div style="font-size:11.5px; color:var(--text-muted);"><?php echo htmlspecialchars($d['telepon']); ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($d['jenis'] === 'hutang'): ?>
                        <span style="background:rgba(239,68,68,.1);color:#dc2626;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            <i class="bi bi-arrow-up" style="font-size:10px;"></i> Hutang
                        </span>
                    <?php else: ?>
                        <span style="background:rgba(16,185,129,.1);color:#059669;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            <i class="bi bi-arrow-down" style="font-size:10px;"></i> Piutang
                        </span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12.5px; max-width:160px;">
                    <?php echo htmlspecialchars($barang_str ?: '-'); ?>
                </td>
                <td style="white-space:nowrap;">Rp <?php echo number_format($d['total_tagihan'], 0, ',', '.'); ?></td>
                <td style="white-space:nowrap; color:#059669;">Rp <?php echo number_format($d['total_bayar'], 0, ',', '.'); ?></td>
                <td style="white-space:nowrap; font-weight:600; color:<?php echo $d['sisa']>0?'#dc2626':'#059669';?>">
                    Rp <?php echo number_format($d['sisa'], 0, ',', '.'); ?>
                </td>
                <td>
                    <?php
                    $status_map = [
                        'belum_bayar' => ['label'=>'Belum','bg'=>'rgba(239,68,68,.1)','color'=>'#dc2626'],
                        'sebagian'    => ['label'=>'Sebagian','bg'=>'rgba(245,158,11,.1)','color'=>'#d97706'],
                        'lunas'       => ['label'=>'Lunas','bg'=>'rgba(16,185,129,.1)','color'=>'#059669'],
                    ];
                    $s = $status_map[$d['status']];
                    ?>
                    <span style="background:<?php echo $s['bg'];?>;color:<?php echo $s['color'];?>;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                        <?php echo $s['label']; ?>
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="detail_hutang.php?id=<?php echo $d['id_transaksi']; ?>" class="btn-icon" title="Detail" style="color:#3b82f6;">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="tambah_hutang.php?edit=<?php echo $d['id_transaksi']; ?>" class="btn-icon edit" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="hutang.php?hapus=<?php echo $d['id_transaksi']; ?>"
                           class="btn-icon del" title="Hapus"
                           onclick="return confirm('Hapus transaksi ini beserta semua datanya?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="9" class="text-center py-5" style="color:var(--text-muted);">
                    <i class="bi bi-journal-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4;"></i>
                    Belum ada transaksi
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Proses hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM transaksi_hutang WHERE id_transaksi = $id");
    header("Location: hutang.php?msg=hapus");
    exit;
}
include "layout_bottom.php";
?>