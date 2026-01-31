<?php
// Generate hash untuk admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash untuk admin123</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #007cba; margin: 20px 0;'>";
echo "<strong>Password:</strong> $password<br>";
echo "<strong>Hash:</strong> $hash<br>";
echo "</div>";

echo "<h3>Copy hash ini ke database:</h3>";
echo "<textarea style='width: 100%; height: 60px; font-family: monospace;'>$hash</textarea>";

echo "<h3>SQL untuk update:</h3>";
echo "<textarea style='width: 100%; height: 80px; font-family: monospace;'>";
echo "UPDATE admin_users SET password = '$hash' WHERE username = 'admin';";
echo "</textarea>";

// Test hash
echo "<h3>Test Hash:</h3>";
if (password_verify($password, $hash)) {
    echo "<span style='color: green; font-weight: bold;'>✓ Hash BENAR - akan berfungsi untuk login</span>";
} else {
    echo "<span style='color: red; font-weight: bold;'>✗ Hash SALAH</span>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Hash - EduFlora</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <p><a href="login.php">← Kembali ke Login</a></p>
</body>
</html>