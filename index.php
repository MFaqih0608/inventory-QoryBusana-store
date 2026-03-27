<?php
include "koneksi.php";
include "auth.php";
$pageTitle = "Dashboard";
include "layout_top.php";

$bln = (int)date('m');
$thn = (int)date('Y');
$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];

// STOK
$total_jenis  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM barang"))[0];
$total_stok   = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(stok),0) FROM barang"))[0];
$stok_habis   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM barang WHERE stok=0"))[0];
$nilai_stok   = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(harga*stok),0) FROM barang"))[0];

// KEUANGAN BULAN INI
$pemasukan   = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(jumlah),0) FROM keuangan
     WHERE jenis='pemasukan' AND MONTH(tanggal)=$bln AND YEAR(tanggal)=$thn"))[0];
$pengeluaran = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(jumlah),0) FROM keuangan
     WHERE jenis='pengeluaran' AND MONTH(tanggal)=$bln AND YEAR(tanggal)=$thn"))[0];
$saldo_bulan = $pemasukan - $pengeluaran;

// HUTANG PIUTANG
$total_hutang  = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(total_tagihan-total_bayar),0) FROM transaksi_hutang
     WHERE jenis='hutang' AND status!='lunas'"))[0];
$total_piutang = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(total_tagihan-total_bayar),0) FROM transaksi_hutang
     WHERE jenis='piutang' AND status!='lunas'"))[0];

// DATA TABEL
$stok_alert   = mysqli_query($conn, "SELECT * FROM barang WHERE stok<=5 ORDER BY stok ASC LIMIT 5");
$trx_terbaru  = mysqli_query($conn,
    "SELECT * FROM keuangan ORDER BY tanggal DESC, id_keuangan DESC LIMIT 6");
$hutang_alert = mysqli_query($conn,
    "SELECT t.*, k.nama FROM transaksi_hutang t
     JOIN kontak k ON t.id_kontak=k.id_kontak
     WHERE t.status!='lunas' ORDER BY t.tanggal_transaksi ASC LIMIT 5");
?>

<!-- Label stok -->
<div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
            color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
    <i class="bi bi-box-seam"></i> Stok Barang
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(233,69,96,.1);color:var(--accent);"><i class="bi bi-box-seam"></i></div>
            <div class="stat-value"><?php echo $total_jenis; ?></div>
            <div class="stat-label">Jenis Barang</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-layers"></i></div>
            <div class="stat-value"><?php echo number_format($total_stok); ?></div>
            <div class="stat-label">Total Stok</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-value" style="color:#ef4444;"><?php echo $stok_habis; ?></div>
            <div class="stat-label">Stok Habis</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-value" style="font-size:15px;">Rp <?php echo number_format($nilai_stok,0,',','.'); ?></div>
            <div class="stat-label">Nilai Stok</div>
        </div>
    </div>
</div>

<!-- Label keuangan -->
<div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
            color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
    <i class="bi bi-wallet2"></i> Keuangan — <?php echo $nama_bulan[$bln].' '.$thn; ?>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card" style="border-left:3px solid #10b981;border-radius:0 14px 14px 0;">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-arrow-down-circle"></i></div>
            <div class="stat-value" style="font-size:16px;color:#059669;">Rp <?php echo number_format($pemasukan,0,',','.'); ?></div>
            <div class="stat-label">Pemasukan</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card" style="border-left:3px solid #ef4444;border-radius:0 14px 14px 0;">
            <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-arrow-up-circle"></i></div>
            <div class="stat-value" style="font-size:16px;color:#dc2626;">Rp <?php echo number_format($pengeluaran,0,',','.'); ?></div>
            <div class="stat-label">Pengeluaran</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card" style="border-left:3px solid #3b82f6;border-radius:0 14px 14px 0;">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value" style="font-size:16px;color:<?php echo $saldo_bulan>=0?'#059669':'#dc2626'; ?>;">
                <?php echo $saldo_bulan<0?'-':''; ?>Rp <?php echo number_format(abs($saldo_bulan),0,',','.'); ?>
            </div>
            <div class="stat-label">Saldo Bersih</div>
        </div>
    </div>
</div>

<!-- Label hutang piutang -->
<div style="font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;
            color:var(--text-muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;">
    <i class="bi bi-journal-text"></i> Hutang &amp; Piutang
</div>
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="stat-card" style="border-left:3px solid #ef4444;border-radius:0 14px 14px 0;">
            <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444;"><i class="bi bi-arrow-up-circle"></i></div>
            <div class="stat-value" style="font-size:16px;color:#dc2626;">Rp <?php echo number_format($total_hutang,0,',','.'); ?></div>
            <div class="stat-label">Hutang Belum Lunas</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card" style="border-left:3px solid #10b981;border-radius:0 14px 14px 0;">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-arrow-down-circle"></i></div>
            <div class="stat-value" style="font-size:16px;color:#059669;">Rp <?php echo number_format($total_piutang,0,',','.'); ?></div>
            <div class="stat-label">Piutang Belum Lunas</div>
        </div>
    </div>
</div>

<!-- Tabel ringkasan 3 kolom -->
<div class="row g-4">

    <!-- Stok menipis -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:600;font-size:13.5px;">
                    <i class="bi bi-exclamation-triangle me-2" style="color:#f59e0b;"></i>Stok Perlu Diisi
                </span>
                <a href="barang.php" style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:600;">Semua →</a>
            </div>
            <div class="card-body p-3">
                <?php if (mysqli_num_rows($stok_alert) > 0):
                    while ($s = mysqli_fetch_assoc($stok_alert)): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:13.5px;font-weight:600;"><?php echo htmlspecialchars($s['nama_barang']); ?></div>
                        <div style="font-size:11.5px;color:var(--text-muted);"><?php echo htmlspecialchars($s['kategori']); ?></div>
                    </div>
                    <?php if ($s['stok']==0): ?>
                        <span class="badge-stok empty">Habis</span>
                    <?php else: ?>
                        <span class="badge-stok low">Sisa <?php echo $s['stok']; ?></span>
                    <?php endif; ?>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:28px 0;color:var(--text-muted);font-size:13px;">
                    <i class="bi bi-check-circle" style="font-size:30px;display:block;margin-bottom:8px;color:#10b981;opacity:.6;"></i>
                    Semua stok aman
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Transaksi keuangan terbaru -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:600;font-size:13.5px;">
                    <i class="bi bi-clock-history me-2 text-muted"></i>Transaksi Terbaru
                </span>
                <a href="keuangan.php" style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:600;">Semua →</a>
            </div>
            <div class="card-body p-3">
                <?php if (mysqli_num_rows($trx_terbaru) > 0):
                    while ($t = mysqli_fetch_assoc($trx_terbaru)): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:14px;
                             background:<?php echo $t['jenis']==='pemasukan'?'rgba(16,185,129,.1)':'rgba(239,68,68,.1)';?>;
                             color:<?php echo $t['jenis']==='pemasukan'?'#059669':'#dc2626';?>;">
                            <i class="bi bi-arrow-<?php echo $t['jenis']==='pemasukan'?'down':'up';?>"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($t['kategori']); ?></div>
                            <div style="font-size:11.5px;color:var(--text-muted);"><?php echo date('d M', strtotime($t['tanggal'])); ?></div>
                        </div>
                    </div>
                    <div style="font-weight:700;font-size:13px;color:<?php echo $t['jenis']==='pemasukan'?'#059669':'#dc2626';?>;">
                        <?php echo $t['jenis']==='pemasukan'?'+':'-'; ?>Rp <?php echo number_format($t['jumlah'],0,',','.'); ?>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:28px 0;color:var(--text-muted);font-size:13px;">
                    <i class="bi bi-inbox" style="font-size:30px;display:block;margin-bottom:8px;opacity:.4;"></i>
                    Belum ada transaksi
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Hutang piutang belum lunas -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-weight:600;font-size:13.5px;">
                    <i class="bi bi-journal-text me-2 text-muted"></i>Belum Lunas
                </span>
                <a href="hutang.php" style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:600;">Semua →</a>
            </div>
            <div class="card-body p-3">
                <?php if (mysqli_num_rows($hutang_alert) > 0):
                    while ($h = mysqli_fetch_assoc($hutang_alert)):
                        $sisa = $h['total_tagihan'] - $h['total_bayar']; ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="display:flex;align-items:center;gap:5px;">
                            <span style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($h['nama']); ?></span>
                            <span style="padding:1px 7px;border-radius:10px;font-size:11px;font-weight:600;
                                  background:<?php echo $h['jenis']==='hutang'?'rgba(239,68,68,.1)':'rgba(16,185,129,.1)';?>;
                                  color:<?php echo $h['jenis']==='hutang'?'#dc2626':'#059669';?>;">
                                <?php echo $h['jenis']==='hutang'?'Hutang':'Piutang'; ?>
                            </span>
                        </div>
                        <div style="font-size:11.5px;color:var(--text-muted);"><?php echo date('d M Y', strtotime($h['tanggal_transaksi'])); ?></div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;font-size:13px;color:#dc2626;">Rp <?php echo number_format($sisa,0,',','.'); ?></div>
                        <div style="font-size:11px;color:var(--text-muted);">sisa</div>
                    </div>
                </div>
                <?php endwhile; else: ?>
                <div style="text-align:center;padding:28px 0;color:var(--text-muted);font-size:13px;">
                    <i class="bi bi-check-circle" style="font-size:30px;display:block;margin-bottom:8px;color:#10b981;opacity:.6;"></i>
                    Semua sudah lunas
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php include "layout_bottom.php"; ?>