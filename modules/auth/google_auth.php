<?php
require_once '../../config/database.php';
require_once '../../config/google.php';
session_start();

$error = '';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $role = $_GET['state'] ?? 'user'; // Mengambil role dari parameter state

    // Sanitasi role
    if (!in_array($role, ['user', 'owner'])) {
        $role = 'user';
    }

    // 1. Tukar Code Otorisasi dengan Access Token
    $token_url = 'https://oauth2.googleapis.com/token';
    $post_fields = [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $token_data = json_decode($response, true);
        $access_token = $token_data['access_token'] ?? '';

        if (!empty($access_token)) {
            // 2. Ambil Profil Pengguna dari UserInfo API Google
            $userinfo_url = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . urlencode($access_token);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userinfo_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $profile_response = curl_exec($ch);
            $profile_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($profile_http_code === 200) {
                $profile_data = json_decode($profile_response, true);
                
                $email  = $profile_data['email'] ?? '';
                $name   = $profile_data['name'] ?? '';
                $avatar = $profile_data['picture'] ?? '';

                if (!empty($email)) {
                    try {
                        // Cari apakah email sudah terdaftar di database
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch();

                        if ($user) {
                            // Jika user ada, login kan langsung
                            $_SESSION['user_id']  = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['role']     = $user['role'];

                            // Update avatar jika kosong
                            if (empty($user['avatar']) && !empty($avatar)) {
                                $up = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                                $up->execute([$avatar, $user['id']]);
                            }
                        } else {
                            // Jika user belum terdaftar, buat akun baru
                            
                            // Buat username unik dari email
                            $email_prefix = explode('@', $email)[0];
                            $username = preg_replace('/[^a-zA-Z0-9]/', '', $email_prefix);
                            if (empty($username)) {
                                $username = 'googleuser';
                            }
                            
                            // Cek ketersediaan username
                            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                            $stmt_check->execute([$username]);
                            if ($stmt_check->fetch()) {
                                $username .= rand(100, 999);
                            }

                            // Buat random password
                            $random_pass = bin2hex(random_bytes(16));
                            $hashed_password = password_hash($random_pass, PASSWORD_DEFAULT);

                            // Simpan user baru
                            $stmt_insert = $pdo->prepare("INSERT INTO users (username, password, email, role, full_name, avatar, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt_insert->execute([
                                $username,
                                $hashed_password,
                                $email,
                                $role,
                                $name,
                                $avatar,
                                ''
                            ]);

                            // Ambil data user yang baru dibuat
                            $new_user_id = $pdo->lastInsertId();
                            
                            // Login kan user baru
                            $_SESSION['user_id']  = $new_user_id;
                            $_SESSION['username'] = $username;
                            $_SESSION['role']     = $role;
                        }

                        // Redirect sesuai role
                        if ($_SESSION['role'] === 'admin') {
                            header("Location: ../admin/dashboard.php");
                        } elseif ($_SESSION['role'] === 'owner') {
                            header("Location: ../owner/dashboard.php");
                        } else {
                            header("Location: ../user/dashboard.php");
                        }
                        exit();

                    } catch (PDOException $e) {
                        $error = "Terjadi kesalahan database: " . $e->getMessage();
                    }
                } else {
                    $error = "Email tidak ditemukan dari profil Google Anda.";
                }
            } else {
                $error = "Gagal mengambil profil dari Google.";
            }
        } else {
            $error = "Token akses tidak valid.";
        }
    } else {
        $error = "Gagal otentikasi Google. Mohon periksa kembali Client ID & Client Secret di config/google.php.";
    }
} else {
    $error = "Otorisasi Google dibatalkan.";
}

// Jika ada error, kembalikan ke login
if (!empty($error)) {
    $_SESSION['google_auth_error'] = $error;
    header("Location: login.php");
    exit();
}
?>
