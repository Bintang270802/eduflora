<?php
// File untuk membuat admin user baru dengan password hash
require_once '../config/database.php';

// Fungsi untuk membuat password hash
function createPasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Fungsi untuk menambah admin user baru
function createAdminUser($username, $password, $full_name, $email = null, $role = 'admin') {
    global $conn;
    
    $hashed_password = createPasswordHash($password);
    
    $stmt = mysqli_prepare($conn, "INSERT INTO admin_users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $full_name, $email, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin user '$username' berhasil dibuat!<br>";
        echo "Password: $password<br>";
        echo "Password Hash: $hashed_password<br><br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
    
    mysqli_stmt_close($stmt);
}

// Contoh penggunaan - uncomment untuk membuat user baru
/*
echo "<h2>Membuat Admin Users</h2>";

// Buat admin default
createAdminUser('admin', 'admin123', 'Administrator', 'admin@eduflora.com', 'super_admin');

// Buat admin tambahan
createAdminUser('eduflora_admin', 'password123', 'EduFlora Admin', 'eduflora@admin.com', 'admin');

echo "<h3>Selesai!</h3>";
*/

// Untuk testing - tampilkan hash dari password tertentu
if (isset($_GET['password'])) {
    $password = $_GET['password'];
    $hash = createPasswordHash($password);
    echo "<h3>Password Hash Generator</h3>";
    echo "Password: " . htmlspecialchars($password) . "<br>";
    echo "Hash: " . $hash . "<br>";
    echo "<br>Gunakan hash ini untuk INSERT ke database.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User - EduFlora</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"], input[type="email"], select {
            width: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
        }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .result { background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Create Admin User</h1>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        
        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
        
        <button type="submit" name="create_user">Create Admin User</button>
    </form>
    
    <?php
    if (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'] ?: null;
        $role = $_POST['role'];
        
        echo "<div class='result'>";
        createAdminUser($username, $password, $full_name, $email, $role);
        echo "</div>";
    }
    ?>
    
    <hr>
    <h3>Password Hash Generator</h3>
    <form method="GET">
        <div class="form-group">
            <label for="test_password">Test Password:</label>
            <input type="text" id="test_password" name="password" placeholder="Masukkan password untuk di-hash">
        </div>
        <button type="submit">Generate Hash</button>
    </form>
    
    <p><a href="login.php">← Kembali ke Login</a></p>
</body>
</html>