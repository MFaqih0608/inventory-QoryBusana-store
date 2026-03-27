<?php
include "koneksi.php";
$pageTitle = "Edit Barang";

$id = (int) $_GET['id'];
$barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = $id"));

if (!$barang) {
    header("Location: barang.php");
    exit;
}

include "layout_top.php";
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square me-2 text-muted"></i>Edit Barang
            </div>
            <div class="card-body p-4">
                <form method="POST" action="update_barang.php">
                    <input type="hidden" name="id" value="<?php echo $barang['id_barang']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control"
                               value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <?php
                            $kategori_list = ['Atasan','Bawahan','Dress','Outerwear','Aksesoris','Lainnya'];
                            foreach ($kategori_list as $kat):
                                $sel = $barang['kategori'] == $kat ? 'selected' : '';
                            ?>
                            <option <?php echo $sel; ?>><?php echo $kat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control"
                                   value="<?php echo $barang['harga']; ?>" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control"
                                   value="<?php echo $barang['stok']; ?>" min="0" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <button type="submit" class="btn-accent" style="flex:1;">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
