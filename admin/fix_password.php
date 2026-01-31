<?php
// Script untuk memperbaiki password eduflora_admin
require_once '../config/database.php';

echo "<h2>Fix Password untuk eduflora_admin</h2>";

// Generate hash yang benar untuk password123
$password = 'password123';
$new_hash = password_hash($password, PASSWORD_DEFAULT);

echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba;'>";
echo "<strong>Password:</strong> $password<br>";
echo "<strong>New Hash:</strong> $new_hash<br>";
echo "</div>";

// Test hash yang lama
$old_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "<h3>Test Hash Lama:</h3>";
if (password_verify($password, $old_hash)) {
    echo "<span style='color: green;'>✓ Hash lama BENAR</span>";
} else {
    echo "<span style='color: red;'>✗ Hash lama SALAH - perlu diperbaiki</span>";
}

echo "<h3>Test Hash Baru:</h3>";
if (password_verify($password, $new_hash)) {
    echo "<span style='color: green;'>✓ Hash baru BENAR</span>";
} else {
    echo "<span style='color: red;'>✗ Hash baru SALAH</span>";
}

// Update password di database
if (isset($_POST['update_password'])) {
    $stmt = mysqli_prepare($conn, "UPDATE admin_users SET password = ? WHERE username = 'eduflora_admin'");
    mysqli_stmt_bind_param($stmt, "s", $new_hash);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
        echo "<strong>✓ Password berhasil diupdate!</strong><br>";
        echo "Sekarang coba login dengan:<br>";
        echo "Username: eduflora_admin<br>";
        echo "Password: password123";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
        echo "✗ Error: " . mysqli_error($conn);
        echo "</div>";
    }
    mysqli_stmt_close($stmt);
}

// Atau buat semua user pakai password admin123
if (isset($_POST['make_same_password'])) {
    $admin123_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = mysqli_prepare($conn, "UPDATE admin_users SET password = ? WHERE username = 'eduflora_admin'");
    mysqli_stmt_bind_param($stmt, "s", $admin123_hash);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
        echo "<strong>✓ Password eduflora_admin diubah ke admin123!</strong><br>";
        echo "Sekarang kedua user menggunakan password yang sama:<br>";
        echo "admin / admin123<br>";
        echo "eduflora_admin / admin123";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 20px 0;'>";
        echo "✗ Error: " . mysqli_error($conn);
        echo "</div>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Password - EduFlora</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
        .danger { background: #dc3545; }
        .danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <form method="POST">
        <h3>Pilih Solusi:</h3>
        
        <button type="submit" name="update_password">
            Perbaiki Password eduflora_admin (tetap password123)
        </button>
        
        <button type="submit" name="make_same_password" class="danger">
            Ubah eduflora_admin jadi pakai admin123 juga
        </button>
    </form>
    
    <hr>
    <p><a href="login.php">← Kembali ke Login</a></p>
</body>
</html>