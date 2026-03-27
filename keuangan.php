<?php
include "koneksi.php";
include "auth.php";

// Hapus transaksi
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keuangan WHERE id_keuangan = $id");
    header("Location: keuangan.php?msg=hapus");
    exit;
}

// Simpan transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis      = in_array($_POST['jenis'], ['pemasukan','pengeluaran']) ? $_POST['jenis'] : 'pemasukan';
    $kategori   = mysqli_real_escape_string($conn, trim($_POST['kategori']));
    $jumlah     = (int)str_replace(['.', ','], '', $_POST['jumlah']);
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);

    mysqli_query($conn,
        "INSERT INTO keuangan (jenis, kategori, jumlah, keterangan, tanggal)
         VALUES ('$jenis', '$kategori', $jumlah, '$keterangan', '$tanggal')");
    header("Location: keuangan.php?msg=simpan");
    exit;
}

$pageTitle = "Keuangan";
include "layout_top.php";

// Filter bulan & tahun
$bln = isset($_GET['bln']) ? (int)$_GET['bln'] : (int)date('m');
$thn = isset($_GET['thn']) ? (int)$_GET['thn'] : (int)date('Y');
$filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';

$where = "WHERE MONTH(tanggal)=$bln AND YEAR(tanggal)=$thn";
if ($filter_jenis === 'pemasukan')   $where .= " AND jenis='pemasukan'";
if ($filter_jenis === 'pengeluaran') $where .= " AND jenis='pengeluaran'";

// Ringkasan bulan ini
$total_masuk  = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(jumlah),0) FROM keuangan WHERE jenis='pemasukan' AND MONTH(tanggal)=$bln AND YEAR(tanggal)=$thn"))[0];
$total_keluar = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COALESCE(SUM(jumlah),0) FROM keuangan WHERE jenis='pengeluaran' AND MONTH(tanggal)=$bln AND YEAR(tanggal)=$thn"))[0];
$saldo = $total_masuk - $total_keluar;

$data = mysqli_query($conn, "SELECT * FROM keuangan $where ORDER BY tanggal DESC, id_keuangan DESC");

$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert d-flex align-items-center gap-2 mb-4"
     style="border-radius:12px;font-size:13.5px;border:none;
     <?php echo $_GET['msg']=='hapus'?'background:rgba(239,68,68,.1);color:#991b1b;':'background:rgba(16,185,129,.1);color:#065f46;';?>">
    <i class="bi bi-<?php echo $_GET['msg']=='hapus'?'trash':'check-circle';?>-fill"></i>
    <?php echo $_GET['msg']=='simpan'?'Transaksi berhasil disimpan.':'Transaksi berhasil dihapus.'; ?>
</div>
<?php endif; ?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="stat-value" style="font-size:17px;color:#059669;">
                Rp <?php echo number_format($total_masuk,0,',','.'); ?>
            </div>
            <div class="stat-label">Pemasukan <?php echo $nama_bulan[$bln]; ?></div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,.1);color:#ef4444;">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="stat-value" style="font-size:17px;color:#dc2626;">
                Rp <?php echo number_format($total_keluar,0,',','.'); ?>
            </div>
            <div class="stat-label">Pengeluaran <?php echo $nama_bulan[$bln]; ?></div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="stat-value" style="font-size:17px;color:<?php echo $saldo>=0?'#059669':'#dc2626';?>;">
                <?php echo $saldo<0?'-':''; ?>Rp <?php echo number_format(abs($saldo),0,',','.'); ?>
            </div>
            <div class="stat-label">Saldo Bersih <?php echo $nama_bulan[$bln]; ?></div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Form tambah -->
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header" style="font-weight:600;">
                <i class="bi bi-plus-circle me-2 text-muted"></i>Catat Transaksi
            </div>
            <div class="card-body p-4">
                <form method="POST" action="keuangan.php">

                    <!-- Toggle Pemasukan / Pengeluaran -->
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <div class="d-flex gap-2">
                            <label style="flex:1;cursor:pointer;">
                                <input type="radio" name="jenis" value="pemasukan" checked style="display:none;" onchange="setJenis('pemasukan')">
                                <div id="btn-masuk" style="padding:10px;border-radius:10px;border:2px solid #10b981;
                                     background:rgba(16,185,129,.07);text-align:center;transition:all .2s;">
                                    <i class="bi bi-arrow-down-circle" style="color:#10b981;font-size:18px;display:block;margin-bottom:3px;"></i>
                                    <div style="font-size:12.5px;font-weight:700;color:#059669;">Pemasukan</div>
                                </div>
                            </label>
                            <label style="flex:1;cursor:pointer;">
                                <input type="radio" name="jenis" value="pengeluaran" style="display:none;" onchange="setJenis('pengeluaran')">
                                <div id="btn-keluar" style="padding:10px;border-radius:10px;border:2px solid var(--border);
                                     background:var(--surface-2);text-align:center;transition:all .2s;">
                                    <i class="bi bi-arrow-up-circle" style="color:var(--text-muted);font-size:18px;display:block;margin-bottom:3px;"></i>
                                    <div style="font-size:12.5px;font-weight:700;color:var(--text-muted);">Pengeluaran</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" id="selectKategori" class="form-select" required>
                            <optgroup label="Pemasukan" id="grp-masuk">
                                <option>Penjualan</option>
                                <option>Modal Tambahan</option>
                                <option>Lain-lain</option>
                            </optgroup>
                            <optgroup label="Pengeluaran" id="grp-keluar" style="display:none;">
                                <option>Belanja Stok</option>
                                <option>Operasional</option>
                                <option>Gaji Karyawan</option>
                                <option>Listrik & Air</option>
                                <option>Lain-lain</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" class="form-control"
                               placeholder="0" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Keterangan (opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2"
                                  placeholder="Misal: penjualan 5 kemeja..."></textarea>
                    </div>

                    <button type="submit" class="btn-accent" style="width:100%;">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar transaksi -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span style="font-weight:600;font-size:14px;">
                        <i class="bi bi-list-ul me-2 text-muted"></i>Riwayat Transaksi
                    </span>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <!-- Filter bulan -->
                        <form method="GET" class="d-flex gap-2 align-items-center">
                            <select name="bln" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                <?php for ($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $m==$bln?'selected':''; ?>>
                                    <?php echo $nama_bulan[$m]; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                            <select name="thn" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                <?php for ($y=date('Y'); $y>=date('Y')-3; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $y==$thn?'selected':''; ?>><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                            <!-- Filter jenis -->
                            <select name="jenis" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                                <option value="semua" <?php echo $filter_jenis=='semua'?'selected':''; ?>>Semua</option>
                                <option value="pemasukan" <?php echo $filter_jenis=='pemasukan'?'selected':''; ?>>Pemasukan</option>
                                <option value="pengeluaran" <?php echo $filter_jenis=='pengeluaran'?'selected':''; ?>>Pengeluaran</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($data) > 0):
                        while ($d = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td style="font-size:12.5px;white-space:nowrap;">
                            <?php echo date('d M Y', strtotime($d['tanggal'])); ?>
                        </td>
                        <td>
                            <?php if ($d['jenis']==='pemasukan'): ?>
                            <span style="background:rgba(16,185,129,.1);color:#059669;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                                <i class="bi bi-arrow-down" style="font-size:10px;"></i> Masuk
                            </span>
                            <?php else: ?>
                            <span style="background:rgba(239,68,68,.1);color:#dc2626;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                                <i class="bi bi-arrow-up" style="font-size:10px;"></i> Keluar
                            </span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;">
                            <span style="background:var(--surface-2);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;border:1px solid var(--border);">
                                <?php echo htmlspecialchars($d['kategori']); ?>
                            </span>
                        </td>
                        <td style="font-weight:700;white-space:nowrap;color:<?php echo $d['jenis']==='pemasukan'?'#059669':'#dc2626';?>;">
                            <?php echo $d['jenis']==='pemasukan'?'+':'-'; ?>
                            Rp <?php echo number_format($d['jumlah'],0,',','.'); ?>
                        </td>
                        <td style="font-size:12.5px;color:var(--text-muted);max-width:160px;">
                            <?php echo htmlspecialchars($d['keterangan'] ?: '-'); ?>
                        </td>
                        <td>
                            <a href="keuangan.php?hapus=<?php echo $d['id_keuangan']; ?>"
                               class="btn-icon del" title="Hapus"
                               onclick="return confirm('Hapus transaksi ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5" style="color:var(--text-muted);">
                            <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4;"></i>
                            Belum ada transaksi di <?php echo $nama_bulan[$bln].' '.$thn; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function setJenis(jenis) {
    const isMasuk = jenis === 'pemasukan';
    const bm = document.getElementById('btn-masuk');
    const bk = document.getElementById('btn-keluar');
    const gm = document.getElementById('grp-masuk');
    const gk = document.getElementById('grp-keluar');
    const sel = document.getElementById('selectKategori');

    if (isMasuk) {
        bm.style.border = '2px solid #10b981';
        bm.style.background = 'rgba(16,185,129,.07)';
        bm.querySelector('i').style.color = '#10b981';
        bm.querySelector('div').style.color = '#059669';
        bk.style.border = '2px solid var(--border)';
        bk.style.background = 'var(--surface-2)';
        bk.querySelector('i').style.color = 'var(--text-muted)';
        bk.querySelector('div').style.color = 'var(--text-muted)';
        gm.style.display = '';
        gk.style.display = 'none';
        sel.selectedIndex = 0;
    } else {
        bk.style.border = '2px solid #ef4444';
        bk.style.background = 'rgba(239,68,68,.07)';
        bk.querySelector('i').style.color = '#ef4444';
        bk.querySelector('div').style.color = '#dc2626';
        bm.style.border = '2px solid var(--border)';
        bm.style.background = 'var(--surface-2)';
        bm.querySelector('i').style.color = 'var(--text-muted)';
        bm.querySelector('div').style.color = 'var(--text-muted)';
        gm.style.display = 'none';
        gk.style.display = '';
        sel.selectedIndex = 0;
    }
}
</script>

<?php include "layout_bottom.php"; ?>