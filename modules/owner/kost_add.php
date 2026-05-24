<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $owner_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $rules = $_POST['rules'];
    $facilities = $_POST['facilities'];
    $price_start = $_POST['price_start'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO kost (owner_id, name, type, description, address, city, rules, facilities, price_start) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$owner_id, $name, $type, $description, $address, $city, $rules, $facilities, $price_start]);
        $kost_id = $pdo->lastInsertId();

        // Handle Image Upload
        if (!empty($_FILES['main_image']['name'])) {
            $target_dir = "../../uploads/kost/";
            $file_extension = pathinfo($_FILES["main_image"]["name"], PATHINFO_EXTENSION);
            $file_name = "kost_" . $kost_id . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO kost_foto (kost_id, image_path, is_main) VALUES (?, ?, 1)");
                $stmt->execute([$kost_id, "uploads/kost/" . $file_name]);
            }
        }

        $pdo->commit();
        $success = "Kost berhasil ditambahkan! <a href='kost_manage.php'>Lihat daftar kost</a>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Gagal menambahkan kost: " . $e->getMessage();
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
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4">Tambah Kost Baru</h3>
                    
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
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Kost Mentari" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipe Kost</label>
                                <select name="type" class="form-select" required>
                                    <option value="Putra">Putra</option>
                                    <option value="Putri">Putri</option>
                                    <option value="Campur">Campur</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota</label>
                                <input type="text" name="city" class="form-control" placeholder="Contoh: Jakarta" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fasilitas (Pisahkan dengan koma)</label>
                            <input type="text" name="facilities" class="form-control" placeholder="Wifi, AC, Kamar Mandi Dalam">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga Mulai (Per Bulan)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_start" class="form-control" placeholder="Contoh: 500000" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Peraturan Kost</label>
                            <textarea name="rules" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Foto Utama Kost</label>
                            <input type="file" name="main_image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="kost_manage.php" class="btn btn-light me-md-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-5">Simpan Kost</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
