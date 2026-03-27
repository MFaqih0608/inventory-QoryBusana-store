<?php
include "koneksi.php";
include "auth.php";

// Proses tambah pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    $id_tr    = (int)$_POST['id_transaksi'];
    $jml      = (int)$_POST['jumlah_bayar'];
    $tgl      = mysqli_real_escape_string($conn, $_POST['tanggal_bayar']);
    $catatan  = mysqli_real_escape_string($conn, trim($_POST['catatan'] ?? ''));

    mysqli_query($conn,
        "INSERT INTO pembayaran_hutang (id_transaksi, jumlah_bayar, tanggal_bayar, catatan)
         VALUES ($id_tr, $jml, '$tgl', '$catatan')");

    // Update total_bayar dan status di transaksi utama
    $total_bayar_baru = mysqli_fetch_row(mysqli_query($conn,
        "SELECT COALESCE(SUM(jumlah_bayar),0) FROM pembayaran_hutang WHERE id_transaksi = $id_tr"))[0];
    $total_tagihan = mysqli_fetch_row(mysqli_query($conn,
        "SELECT total_tagihan FROM transaksi_hutang WHERE id_transaksi = $id_tr"))[0];

    if ($total_bayar_baru <= 0)                  $status = 'belum_bayar';
    elseif ($total_bayar_baru >= $total_tagihan) $status = 'lunas';
    else                                          $status = 'sebagian';

    mysqli_query($conn,
        "UPDATE transaksi_hutang SET total_bayar=$total_bayar_baru, status='$status'
         WHERE id_transaksi = $id_tr");

    header("Location: detail_hutang.php?id=$id_tr&msg=bayar");
    exit;
}

$id = (int)$_GET['id'];
$tr = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT t.*, k.nama, k.telepon, k.keterangan as ket_kontak
     FROM transaksi_hutang t
     JOIN kontak k ON t.id_kontak = k.id_kontak
     WHERE t.id_transaksi = $id"));

if (!$tr) { header("Location: hutang.php"); exit; }

$details   = mysqli_query($conn, "SELECT * FROM detail_hutang WHERE id_transaksi = $id");
$payments  = mysqli_query($conn, "SELECT * FROM pembayaran_hutang WHERE id_transaksi = $id ORDER BY tanggal_bayar DESC");
$sisa      = $tr['total_tagihan'] - $tr['total_bayar'];

$pageTitle = "Detail Transaksi";
include "layout_top.php";
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'bayar'): ?>
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="border-radius:12px;font-size:13.5px;border:none;background:rgba(16,185,129,.1);color:#065f46;">
    <i class="bi bi-check-circle-fill"></i> Pembayaran berhasil dicatat.
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- Kiri: info + barang + bayar -->
    <div class="col-12 col-lg-8">

        <!-- Info utama -->
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:600;">
                    <i class="bi bi-receipt me-2 text-muted"></i>Detail Transaksi #<?php echo $id; ?>
                </span>
                <span>
                    <?php if ($tr['jenis'] === 'hutang'): ?>
                        <span style="background:rgba(239,68,68,.1);color:#dc2626;padding:4px 12px;border-radius:20px;font-size:12.5px;font-weight:600;">
                            <i class="bi bi-arrow-up"></i> Hutang
                        </span>
                    <?php else: ?>
                        <span style="background:rgba(16,185,129,.1);color:#059669;padding:4px 12px;border-radius:20px;font-size:12.5px;font-weight:600;">
                            <i class="bi bi-arrow-down"></i> Piutang
                        </span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">Nama</div>
                        <div style="font-weight:600;"><?php echo htmlspecialchars($tr['nama']); ?></div>
                        <?php if ($tr['telepon']): ?>
                        <div style="font-size:12px;color:var(--text-muted);"><?php echo htmlspecialchars($tr['telepon']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-6">
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">Tanggal</div>
                        <div style="font-weight:600;"><?php echo date('d F Y', strtotime($tr['tanggal_transaksi'])); ?></div>
                    </div>
                    <?php if ($tr['keterangan']): ?>
                    <div class="col-12">
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">Keterangan</div>
                        <div><?php echo htmlspecialchars($tr['keterangan']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Detail barang -->
        <div class="card mb-3">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-bag me-2 text-muted"></i>Daftar Barang
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($b = mysqli_fetch_assoc($details)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['nama_barang']); ?></strong></td>
                            <td><?php echo $b['qty']; ?></td>
                            <td><?php echo htmlspecialchars($b['satuan']); ?></td>
                            <td>Rp <?php echo number_format($b['harga_satuan'], 0, ',', '.'); ?></td>
                            <td><strong>Rp <?php echo number_format($b['subtotal'], 0, ',', '.'); ?></strong></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr style="background:var(--surface-2);">
                            <td colspan="4" style="text-align:right;font-weight:700;">Total Tagihan</td>
                            <td style="font-weight:700;font-size:15px;">Rp <?php echo number_format($tr['total_tagihan'], 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form tambah pembayaran -->
        <?php if ($tr['status'] !== 'lunas'): ?>
        <div class="card mb-3">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-cash-stack me-2 text-muted"></i>Catat Pembayaran
            </div>
            <div class="card-body p-4">
                <form method="POST" action="">
                    <input type="hidden" name="bayar" value="1">
                    <input type="hidden" name="id_transaksi" value="<?php echo $id; ?>">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Jumlah Bayar (Rp)</label>
                            <input type="number" name="jumlah_bayar" class="form-control"
                                   placeholder="<?php echo $sisa; ?>" min="1" max="<?php echo $sisa; ?>" required>
                            <div style="font-size:11.5px;color:var(--text-muted);margin-top:4px;">
                                Sisa: Rp <?php echo number_format($sisa, 0, ',', '.'); ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" class="form-control"
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-accent">
                                <i class="bi bi-check-lg me-1"></i> Catat Pembayaran
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Kanan: ringkasan + riwayat bayar -->
    <div class="col-12 col-lg-4">

        <!-- Ringkasan keuangan -->
        <div class="card mb-3">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-graph-up me-2 text-muted"></i>Ringkasan
            </div>
            <div class="card-body p-4">
                <?php
                $persen = $tr['total_tagihan'] > 0
                    ? round(($tr['total_bayar'] / $tr['total_tagihan']) * 100)
                    : 0;
                ?>
                <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                    <span style="color:var(--text-muted);">Progress bayar</span>
                    <span style="font-weight:700;"><?php echo $persen; ?>%</span>
                </div>
                <div style="height:8px;background:var(--surface-2);border-radius:8px;overflow:hidden;margin-bottom:16px;border:1px solid var(--border);">
                    <div style="height:100%;width:<?php echo $persen; ?>%;background:<?php echo $persen>=100?'#10b981':'#e94560';?>;border-radius:8px;transition:width .5s;"></div>
                </div>

                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;">
                        <span style="color:var(--text-muted);">Total tagihan</span>
                        <span style="font-weight:600;">Rp <?php echo number_format($tr['total_tagihan'], 0, ',', '.'); ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:13.5px;">
                        <span style="color:var(--text-muted);">Sudah dibayar</span>
                        <span style="font-weight:600;color:#059669;">Rp <?php echo number_format($tr['total_bayar'], 0, ',', '.'); ?></span>
                    </div>
                    <div style="height:1px;background:var(--border);"></div>
                    <div style="display:flex;justify-content:space-between;font-size:15px;">
                        <span style="font-weight:700;">Sisa</span>
                        <span style="font-weight:700;color:<?php echo $sisa>0?'#dc2626':'#059669';?>">
                            Rp <?php echo number_format($sisa, 0, ',', '.'); ?>
                        </span>
                    </div>
                </div>

                <?php
                $status_map = [
                    'belum_bayar' => ['label'=>'Belum Bayar','bg'=>'rgba(239,68,68,.1)','color'=>'#dc2626'],
                    'sebagian'    => ['label'=>'Bayar Sebagian','bg'=>'rgba(245,158,11,.1)','color'=>'#d97706'],
                    'lunas'       => ['label'=>'Lunas','bg'=>'rgba(16,185,129,.1)','color'=>'#059669'],
                ];
                $s = $status_map[$tr['status']];
                ?>
                <div style="margin-top:16px;padding:10px;border-radius:10px;background:<?php echo $s['bg'];?>;
                            text-align:center;font-weight:700;color:<?php echo $s['color'];?>;">
                    <?php echo $s['label']; ?>
                </div>
            </div>
        </div>

        <!-- Riwayat pembayaran -->
        <div class="card">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-clock-history me-2 text-muted"></i>Riwayat Pembayaran
            </div>
            <div class="card-body p-3">
                <?php if (mysqli_num_rows($payments) > 0):
                    while ($p = mysqli_fetch_assoc($payments)): ?>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;
                            padding:10px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:600;font-size:13.5px;color:#059669;">
                            + Rp <?php echo number_format($p['jumlah_bayar'], 0, ',', '.'); ?>
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            <?php echo date('d M Y', strtotime($p['tanggal_bayar'])); ?>
                        </div>
                        <?php if ($p['catatan']): ?>
                        <div style="font-size:12px;color:var(--text-muted);"><?php echo htmlspecialchars($p['catatan']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:20px 0;color:var(--text-muted);font-size:13px;">
                    Belum ada pembayaran
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3">
            <a href="hutang.php" style="color:var(--text-muted);font-size:13px;text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar
            </a>
        </div>
    </div>

</div>

<?php include "layout_bottom.php"; ?>