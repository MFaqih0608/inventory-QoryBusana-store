<?php
include "koneksi.php";
$pageTitle = "Tambah Transaksi";
include "layout_top.php";

$kontaks = mysqli_query($conn, "SELECT * FROM kontak ORDER BY nama ASC");
?>

<div class="row justify-content-center">
<div class="col-12 col-lg-9">

<form method="POST" action="simpan_hutang.php" id="formHutang">

<!-- Informasi Utama -->
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-info-circle me-2 text-muted"></i>Informasi Transaksi</div>
    <div class="card-body p-4">
        <div class="row g-3">

            <div class="col-12 col-md-6">
                <label class="form-label">Jenis Transaksi</label>
                <div class="d-flex gap-2">
                    <label style="flex:1; cursor:pointer;">
                        <input type="radio" name="jenis" value="hutang" style="display:none;" onchange="updateJenis()" checked>
                        <div class="jenis-btn" id="btn-hutang" style="padding:12px; border-radius:10px; border:2px solid #e94560;
                             background:rgba(233,69,96,.07); text-align:center; transition:all .2s;">
                            <i class="bi bi-arrow-up-circle" style="font-size:20px; color:#e94560; display:block; margin-bottom:4px;"></i>
                            <div style="font-weight:700; font-size:13px; color:#e94560;">Hutang</div>
                            <div style="font-size:11px; color:var(--text-muted);">Kamu berhutang</div>
                        </div>
                    </label>
                    <label style="flex:1; cursor:pointer;">
                        <input type="radio" name="jenis" value="piutang" style="display:none;" onchange="updateJenis()">
                        <div class="jenis-btn" id="btn-piutang" style="padding:12px; border-radius:10px; border:2px solid var(--border);
                             background:var(--surface-2); text-align:center; transition:all .2s;">
                            <i class="bi bi-arrow-down-circle" style="font-size:20px; color:var(--text-muted); display:block; margin-bottom:4px;"></i>
                            <div style="font-weight:700; font-size:13px; color:var(--text-muted);">Piutang</div>
                            <div style="font-size:11px; color:var(--text-muted);">Orang lain berhutang</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" name="tanggal_transaksi" class="form-control"
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="col-12">
                <label class="form-label">Nama Kontak</label>
                <div class="d-flex gap-2">
                    <select name="id_kontak" class="form-select" required>
                        <option value="" disabled selected>-- Pilih kontak --</option>
                        <?php while ($k = mysqli_fetch_assoc($kontaks)): ?>
                        <option value="<?php echo $k['id_kontak']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <a href="kontak.php" target="_blank"
                       style="padding:10px 14px; border:1px solid var(--border); border-radius:10px;
                              text-decoration:none; color:var(--text-muted); white-space:nowrap; font-size:13px;
                              display:flex; align-items:center; gap:6px; background:var(--surface-2);">
                        <i class="bi bi-person-plus"></i> Kontak Baru
                    </a>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Keterangan (opsional)</label>
                <textarea name="keterangan" class="form-control" rows="2"
                          placeholder="Misal: Pembelian stok Maret, titipan uang, dll."></textarea>
            </div>

        </div>
    </div>
</div>

<!-- Detail Barang -->
<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-bag me-2 text-muted"></i>Detail Barang</span>
        <button type="button" onclick="tambahBaris()"
                style="padding:6px 12px; background:#e94560; color:#fff; border:none; border-radius:8px;
                       font-size:12.5px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif;">
            <i class="bi bi-plus"></i> Tambah Baris
        </button>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-custom mb-0" id="tabelBarang">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th style="width:80px;">Qty</th>
                        <th style="width:90px;">Satuan</th>
                        <th style="width:150px;">Harga Satuan</th>
                        <th style="width:150px;">Subtotal</th>
                        <th style="width:40px;"></th>
                    </tr>
                </thead>
                <tbody id="barisBarang">
                    <!-- baris pertama -->
                    <tr class="baris-barang">
                        <td><input type="text" name="nama_barang[]" class="form-control form-control-sm" placeholder="Nama barang" required></td>
                        <td><input type="number" name="qty[]" class="form-control form-control-sm qty" value="1" min="1" onchange="hitungSubtotal(this)" required></td>
                        <td><input type="text" name="satuan[]" class="form-control form-control-sm" value="pcs"></td>
                        <td><input type="number" name="harga_satuan[]" class="form-control form-control-sm harga" value="0" min="0" onchange="hitungSubtotal(this)" required></td>
                        <td><input type="text" name="subtotal[]" class="form-control form-control-sm subtotal" value="0" readonly style="background:var(--surface-2);"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="d-flex justify-content-end mt-3">
            <div style="text-align:right;">
                <div style="font-size:13px; color:var(--text-muted);">Total Tagihan</div>
                <div style="font-size:22px; font-weight:700; color:#e94560;" id="totalTagihan">Rp 0</div>
                <input type="hidden" name="total_tagihan" id="inputTotal" value="0">
            </div>
        </div>
    </div>
</div>

<!-- Bayar Awal (opsional) -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-cash me-2 text-muted"></i>Pembayaran Awal (opsional)</div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label">Jumlah Bayar Awal</label>
                <input type="number" name="bayar_awal" class="form-control" value="0" min="0"
                       placeholder="0 jika belum bayar sama sekali">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Catatan Pembayaran</label>
                <input type="text" name="catatan_bayar" class="form-control" placeholder="Misal: DP pertama">
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn-accent" style="flex:1;">
        <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
    </button>
    <a href="hutang.php" class="btn"
       style="border-radius:10px; border:1px solid var(--border); font-size:13.5px; font-weight:500; padding:9px 18px;">
        Batal
    </a>
</div>

</form>
</div>
</div>

<script>
function tambahBaris() {
    const tbody = document.getElementById('barisBarang');
    const tr = document.createElement('tr');
    tr.className = 'baris-barang';
    tr.innerHTML = `
        <td><input type="text" name="nama_barang[]" class="form-control form-control-sm" placeholder="Nama barang" required></td>
        <td><input type="number" name="qty[]" class="form-control form-control-sm qty" value="1" min="1" onchange="hitungSubtotal(this)" required></td>
        <td><input type="text" name="satuan[]" class="form-control form-control-sm" value="pcs"></td>
        <td><input type="number" name="harga_satuan[]" class="form-control form-control-sm harga" value="0" min="0" onchange="hitungSubtotal(this)" required></td>
        <td><input type="text" name="subtotal[]" class="form-control form-control-sm subtotal" value="0" readonly style="background:var(--surface-2);"></td>
        <td><button type="button" onclick="hapusBaris(this)" style="background:rgba(239,68,68,.1);border:none;border-radius:6px;color:#dc2626;padding:4px 8px;cursor:pointer;"><i class="bi bi-trash" style="font-size:12px;"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function hapusBaris(btn) {
    const rows = document.querySelectorAll('.baris-barang');
    if (rows.length <= 1) return;
    btn.closest('tr').remove();
    updateTotal();
}

function hitungSubtotal(el) {
    const row = el.closest('tr');
    const qty    = parseFloat(row.querySelector('.qty').value)   || 0;
    const harga  = parseFloat(row.querySelector('.harga').value) || 0;
    const sub    = qty * harga;
    row.querySelector('.subtotal').value = sub.toLocaleString('id-ID');
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.baris-barang').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty').value)   || 0;
        const harga = parseFloat(row.querySelector('.harga').value) || 0;
        total += qty * harga;
    });
    document.getElementById('totalTagihan').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('inputTotal').value = total;
}

function updateJenis() {
    const isHutang = document.querySelector('input[name="jenis"]:checked').value === 'hutang';
    const btnH = document.getElementById('btn-hutang');
    const btnP = document.getElementById('btn-piutang');
    if (isHutang) {
        btnH.style.border = '2px solid #e94560';
        btnH.style.background = 'rgba(233,69,96,.07)';
        btnH.querySelector('i').style.color = '#e94560';
        btnH.querySelector('div').style.color = '#e94560';
        btnP.style.border = '2px solid var(--border)';
        btnP.style.background = 'var(--surface-2)';
        btnP.querySelector('i').style.color = 'var(--text-muted)';
        btnP.querySelector('div').style.color = 'var(--text-muted)';
    } else {
        btnP.style.border = '2px solid #10b981';
        btnP.style.background = 'rgba(16,185,129,.07)';
        btnP.querySelector('i').style.color = '#10b981';
        btnP.querySelector('div').style.color = '#10b981';
        btnH.style.border = '2px solid var(--border)';
        btnH.style.background = 'var(--surface-2)';
        btnH.querySelector('i').style.color = 'var(--text-muted)';
        btnH.querySelector('div').style.color = 'var(--text-muted)';
    }
}
</script>

<?php include "layout_bottom.php"; ?>