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
        
        // Update main image if uploaded
        if (!empty($_FILES['main_image']['name'])) {
            $target_dir = "../../uploads/kost/";
            $file_extension = pathinfo($_FILES["main_image"]["name"], PATHINFO_EXTENSION);
            $file_name = "kost_" . $id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
                // Delete old main image record
                $stmt = $pdo->prepare("DELETE FROM kost_foto WHERE kost_id = ? AND is_main = 1");
                $stmt->execute([$id]);
                
                // Insert new main image
                $stmt = $pdo->prepare("INSERT INTO kost_foto (kost_id, image_path, is_main) VALUES (?, ?, 1)");
                $stmt->execute([$id, "uploads/kost/" . $file_name]);
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

                        <div class="mb-4">
                            <label class="form-label">Foto Utama Baru (Kosongkan jika tidak ingin ganti)</label>
                            <input type="file" name="main_image" class="form-control" accept="image/*">
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

<?php include '../../layouts/footer.php'; ?>
