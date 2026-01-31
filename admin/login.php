<?php
session_start();
require_once '../config/database.php';

// Redirect jika sudah login
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: flora.php');
    exit();
}

$error_message = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Query untuk mencari user di database
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, full_name, role, is_active FROM admin_users WHERE username = ? AND is_active = 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_full_name'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];
                
                // Update last login
                $update_stmt = mysqli_prepare($conn, "UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                mysqli_stmt_bind_param($update_stmt, "i", $user['id']);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);
                
                header('Location: flora.php');
                exit();
            } else {
                $error_message = 'Username atau password salah!';
            }
        } else {
            $error_message = 'Username atau password salah!';
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $error_message = 'Harap isi semua field!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EduFlora</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-background">
            <div class="bg-animation">
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
                <div class="floating-shape"></div>
            </div>
        </div>
        
        <div class="login-content">
            <div class="login-form-container">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-leaf"></i>
                        <span>EduFlora</span>
                    </div>
                    <h1>Admin Panel</h1>
                    <p>Masuk untuk mengelola sistem informasi flora dan fauna</p>
                </div>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>
                            Username
                        </label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Masukkan username admin" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required 
                                   placeholder="Masukkan password">
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk ke Dashboard
                    </button>
                </form>
                
                <div class="login-footer">
                    <div class="demo-credentials">
                        <h4><i class="fas fa-info-circle"></i> Demo Credentials</h4>
                        <p><strong>Username:</strong> admin</p>
                        <p><strong>Password:</strong> admin123</p>
                    </div>
                    
                    <div class="back-to-site">
                        <a href="../index.php">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke Website
                        </a>
                    </div>
                    
                    <div class="admin-tools">
                        <a href="create_admin.php" style="font-size: 12px; color: #666;">
                            <i class="fas fa-user-plus"></i>
                            Create New Admin
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="login-features">
                <h3>Fitur Admin Panel</h3>
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-seedling"></i>
                        <div>
                            <h4>Kelola Flora</h4>
                            <p>Tambah, edit, dan hapus data flora</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-paw"></i>
                        <div>
                            <h4>Kelola Fauna</h4>
                            <p>Tambah, edit, dan hapus data fauna</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-bar"></i>
                        <div>
                            <h4>Dashboard Analytics</h4>
                            <p>Lihat statistik dan laporan</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-images"></i>
                        <div>
                            <h4>Manajemen Media</h4>
                            <p>Upload dan kelola gambar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.classList.remove('fa-eye');
                toggleBtn.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleBtn.classList.remove('fa-eye-slash');
                toggleBtn.classList.add('fa-eye');
            }
        }
        
        // Form validation
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Harap isi semua field!');
                return;
            }
            
            // Add loading state
            const submitBtn = document.querySelector('.login-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitBtn.disabled = true;
        });
        
        // Auto-focus on username field
        document.getElementById('username').focus();
        
        // Add enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.login-form').submit();
            }
        });
    </script>
</body>
</html>