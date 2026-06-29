<?php
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role, $full_name, $status]);
        $success = "User berhasil ditambahkan! <a href='users.php'>Lihat daftar</a>";

        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $log_stmt = $pdo->prepare("INSERT INTO system_logs (user_id, username, role, activity, ip_address) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->execute([
                $_SESSION['user_id'],
                $_SESSION['username'] ?? 'admin',
                $_SESSION['role'] ?? 'admin',
                "Menambah pengguna baru: $username sebagai $role",
                $ip
            ]);
        } catch (Exception $log_ex) {
            // Fail silently
        }
    } catch (Exception $e) {
        $error = "Gagal menambah user: " . $e->getMessage();
    }
}

include '../../layouts/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-neubrutal-yellow py-3 border-bottom border-3 border-dark">
                    <h5 class="mb-0 fw-black text-uppercase">Tambah Pengguna Baru</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="user">Tenant (Pencari Kost)</option>
                                    <option value="owner">Owner (Pemilik Kost)</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="full_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Verifikasi</label>
                                <select name="status" class="form-select" required>
                                    <option value="verified">Verified</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="users.php" class="btn btn-light me-md-2">BATAL</a>
                            <button type="submit" class="btn btn-primary px-5">SIMPAN USER</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/footer.php'; ?>
