<?php
include "koneksi.php";
$pageTitle = "Tambah Barang";
include "layout_top.php";
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2 text-muted"></i>Form Tambah Barang
            </div>
            <div class="card-body p-4">
                <form method="POST" action="simpan_barang.php">

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                               placeholder="Contoh : Pashmina" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="" disabled selected>Pilih kategori</option>
                            <option>Jilbab</option>
                            <option>Atasan</option>
                            <option>Gamis</option>
                            <option>BH</option>
                            <option>Celana Dalam</option>
                            <option>Kaos Kaki</option>
                            <option>Bed Cover</option>
                            <option>Handuk</option>
                            <option>Lainnya</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control"
                                   placeholder="150000" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control"
                                   placeholder="10" min="0" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn-accent" style="flex:1;">
                            <i class="bi bi-check-lg me-1"></i> Simpan Barang
                        </button>
                        <a href="barang.php" class="btn" style="border-radius:10px; border:1px solid var(--border); font-size:13.5px; font-weight:500;">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include "layout_bottom.php"; ?>
