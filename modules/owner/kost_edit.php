<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$owner_id = $_SESSION['user_id'];

// Verify ownership
$stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ? AND owner_id = ?");
$stmt->execute([$id, $owner_id]);
$kost = $stmt->fetch();

if (!$kost) {
    header("Location: kost_manage.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_photo_id'])) {
        $photo_id = (int)$_POST['delete_photo_id'];
        $stmt = $pdo->prepare("SELECT kf.* FROM kost_foto kf JOIN kost k ON kf.kost_id = k.id WHERE kf.id = ? AND k.owner_id = ?");
        $stmt->execute([$photo_id, $owner_id]);
        $photo = $stmt->fetch();
        
        if ($photo) {
            $file_path = "../../" . $photo['image_path'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $pdo->prepare("DELETE FROM kost_foto WHERE id = ?")->execute([$photo_id]);
            if ($photo['is_main']) {
                $pdo->prepare("UPDATE kost_foto SET is_main = 1 WHERE kost_id = ? LIMIT 1")->execute([$id]);
            }
            $success = "Foto berhasil dihapus!";
        }
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ?");
        $stmt->execute([$id]);
        $kost = $stmt->fetch();
    } elseif (isset($_POST['set_main_photo_id'])) {
        $photo_id = (int)$_POST['set_main_photo_id'];
        $stmt = $pdo->prepare("SELECT kf.* FROM kost_foto kf JOIN kost k ON kf.kost_id = k.id WHERE kf.id = ? AND k.owner_id = ?");
        $stmt->execute([$photo_id, $owner_id]);
        $photo = $stmt->fetch();
        
        if ($photo) {
            $pdo->prepare("UPDATE kost_foto SET is_main = 0 WHERE kost_id = ?")->execute([$id]);
            $pdo->prepare("UPDATE kost_foto SET is_main = 1 WHERE id = ?")->execute([$photo_id]);
            $success = "Foto utama berhasil diperbarui!";
        }
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ?");
        $stmt->execute([$id]);
        $kost = $stmt->fetch();
    } else {
        $name = $_POST['name'];
        $type = $_POST['type'];
        $description = $_POST['description'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $rules = $_POST['rules'];
        $facilities = $_POST['facilities'];
        $price_start = $_POST['price_start'];

        try {
            $stmt = $pdo->prepare("UPDATE kost SET name = ?, type = ?, description = ?, address = ?, city = ?, rules = ?, facilities = ?, price_start = ? WHERE id = ?");
            $stmt->execute([$name, $type, $description, $address, $city, $rules, $facilities, $price_start, $id]);
            
            // Handle Multiple Image Upload
            if (!empty($_FILES['kost_images']['name'][0])) {
                $target_dir = "../../uploads/kost/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM kost_foto WHERE kost_id = ? AND is_main = 1");
                $stmt_check->execute([$id]);
                $has_main = ($stmt_check->fetchColumn() > 0);

                foreach ($_FILES['kost_images']['name'] as $key => $name_img) {
                    if ($_FILES['kost_images']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['kost_images']['tmp_name'][$key];
                        $file_extension = pathinfo($name_img, PATHINFO_EXTENSION);
                        $file_name = "kost_" . $id . "_" . time() . "_" . $key . "." . $file_extension;
                        $target_file = $target_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $is_main = (!$has_main && $key === 0) ? 1 : 0;
                            $stmt = $pdo->prepare("INSERT INTO kost_foto (kost_id, image_path, is_main) VALUES (?, ?, ?)");
                            $stmt->execute([$id, "uploads/kost/" . $file_name, $is_main]);
                            $has_main = true;
                        }
                    }
                }
            }

            $success = "Data kost berhasil diperbarui! <a href='kost_manage.php'>Kembali ke daftar</a>";
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ?");
            $stmt->execute([$id]);
            $kost = $stmt->fetch();
        } catch (Exception $e) {
            $error = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

// Fetch current photos
$stmt_photos = $pdo->prepare("SELECT * FROM kost_foto WHERE kost_id = ? ORDER BY is_main DESC, id ASC");
$stmt_photos->execute([$id]);
$current_photos = $stmt_photos->fetchAll();

include '../../layouts/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm border-0">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action active"><i class="bi bi-house me-2"></i> Kelola Kost</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4">Edit Data Kost</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama Kost</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($kost['name']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipe Kost</label>
                                <select name="type" class="form-select" required>
                                    <option value="Putra" <?php echo $kost['type'] == 'Putra' ? 'selected' : ''; ?>>Putra</option>
                                    <option value="Putri" <?php echo $kost['type'] == 'Putri' ? 'selected' : ''; ?>>Putri</option>
                                    <option value="Campur" <?php echo $kost['type'] == 'Campur' ? 'selected' : ''; ?>>Campur</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($kost['description']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota</label>
                                <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($kost['city']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($kost['address']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fasilitas</label>
                            <input type="text" name="facilities" class="form-control" value="<?php echo htmlspecialchars($kost['facilities']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Mulai (Per Bulan)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_start" class="form-control" value="<?php echo htmlspecialchars($kost['price_start']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Peraturan Kost</label>
                            <textarea name="rules" class="form-control" rows="2"><?php echo htmlspecialchars($kost['rules']); ?></textarea>
                        </div>

                        <!-- Current Photos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Foto Kost Saat Ini</label>
                            <?php if (empty($current_photos)): ?>
                                <p class="text-muted small">Belum ada foto kost.</p>
                            <?php else: ?>
                                <div class="row g-2 mb-3">
                                    <?php foreach ($current_photos as $photo): ?>
                                        <div class="col-6 col-md-3">
                                            <div class="card h-100 border border-2 border-dark position-relative" style="box-shadow: 2px 2px 0 #000;">
                                                <img src="<?php echo $base_url . $photo['image_path']; ?>" class="card-img-top" style="height: 120px; object-fit: cover;">
                                                <div class="card-body p-2 d-flex flex-column gap-2 justify-content-between">
                                                    <?php if ($photo['is_main']): ?>
                                                        <span class="badge bg-primary w-100 py-1" style="border: 1px solid #000;">Foto Utama</span>
                                                    <?php else: ?>
                                                        <button type="submit" name="set_main_photo_id" value="<?php echo $photo['id']; ?>" class="btn btn-sm btn-outline-success w-100 py-1" style="font-size: .75rem;">Jadikan Utama</button>
                                                    <?php endif; ?>
                                                    <button type="submit" name="delete_photo_id" value="<?php echo $photo['id']; ?>" class="btn btn-sm btn-danger w-100 py-1" style="font-size: .75rem;" onclick="return confirm('Hapus foto ini?')">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Add New Photos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tambah Foto Baru (Bisa pilih 1 atau lebih)</label>
                            <input type="file" name="kost_images[]" id="kost_images" class="form-control" accept="image/*" multiple>
                            <div id="image_preview_container" class="row g-2 mt-2"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="kost_manage.php" class="btn btn-light me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">Update Kost</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('kost_images').addEventListener('change', function(event) {
    const container = document.getElementById('image_preview_container');
    container.innerHTML = '';
    const files = event.target.files;
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 position-relative';
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail w-100';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.style.border = '2px solid #000';
                img.style.boxShadow = '3px 3px 0 #000';
                
                const badge = document.createElement('span');
                badge.className = 'position-absolute top-0 start-0 m-2 badge bg-secondary';
                badge.innerText = 'Baru ' + (i + 1);
                badge.style.border = '1px solid #000';
                
                col.appendChild(img);
                col.appendChild(badge);
                container.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>

<?php include '../../layouts/footer.php'; ?>
