<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Handle Avatar Upload
    $avatar_name = $user['avatar'];
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "../../uploads/avatars/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_extension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        $avatar_name = "avatar_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $avatar_name;

        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            // Success upload
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $phone, $avatar_name, $user_id]);
        $success = "Profil pemilik berhasil diperbarui!";
        
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

include '../../layouts/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="list-group">
                <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a href="kost_manage.php" class="list-group-item list-group-item-action"><i class="bi bi-house me-2"></i> Kelola Kost</a>
                <a href="bookings.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar-check me-2"></i> Pesanan Masuk</a>
                <a href="profile.php" class="list-group-item list-group-item-action active"><i class="bi bi-person me-2"></i> Profil</a>
                <a href="chat.php" class="list-group-item list-group-item-action"><i class="bi bi-chat-dots me-2"></i> Chat</a>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card p-4">
                <h2 class="mb-4 fw-black text-uppercase">Profil Pemilik Kost</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger border-3 rounded-0 shadow-sm mb-4"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success border-3 rounded-0 shadow-sm mb-4"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <?php
                                $avatar_url = 'https://via.placeholder.com/150?text=No+Avatar';
                                if ($user['avatar']) {
                                    if (filter_var($user['avatar'], FILTER_VALIDATE_URL)) {
                                        $avatar_url = $user['avatar'];
                                    } else {
                                        $avatar_url = $base_url . 'uploads/avatars/' . $user['avatar'];
                                    }
                                }
                                ?>
                                <img src="<?php echo $avatar_url; ?>" 
                                     class="border border-4 border-dark shadow-sm" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Ganti Foto Profil</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Username Pemilik</label>
                            <input type="text" class="form-control bg-light" value="<?php echo $user['username']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Akun</label>
                            <input type="text" class="form-control bg-light text-uppercase" value="<?php echo $user['role']; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Bisnis</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor WhatsApp Bisnis</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" name="update_profile" class="btn btn-primary px-5 py-3 w-100 w-md-auto">Update Profil Bisnis</button>
                    </div>
                </form>

                <div class="mt-5 p-4 border border-3 border-dark bg-light">
                    <h4 class="fw-black text-uppercase mb-4">Ganti Password Keamanan</h4>
                    <form action="change_password.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
                            </div>
                            <div class="col-md-5">
                                <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi Password Baru" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger w-100 py-3">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
