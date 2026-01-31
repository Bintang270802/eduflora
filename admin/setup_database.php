<?php
// File untuk setup database admin users
require_once '../config/database.php';

echo "<h2>Setup Database Admin Users</h2>";

// Baca file SQL
$sql_file = '../database/admin_users_table.sql';
if (!file_exists($sql_file)) {
    die("File SQL tidak ditemukan: $sql_file");
}

$sql_content = file_get_contents($sql_file);

// Pisahkan query berdasarkan semicolon
$queries = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;

foreach ($queries as $query) {
    if (empty($query) || strpos($query, '--') === 0) {
        continue; // Skip empty queries and comments
    }
    
    echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba;'>";
    echo "<strong>Executing:</strong> " . substr($query, 0, 100) . "...<br>";
    
    if (mysqli_query($conn, $query)) {
        echo "<span style='color: green;'>✓ Berhasil</span>";
        $success_count++;
    } else {
        echo "<span style='color: red;'>✗ Error: " . mysqli_error($conn) . "</span>";
        $error_count++;
    }
    echo "</div>";
}

echo "<hr>";
echo "<h3>Hasil Setup:</h3>";
echo "<p>✓ Berhasil: $success_count query</p>";
echo "<p>✗ Error: $error_count query</p>";

if ($error_count == 0) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0;'>";
    echo "<strong>Setup berhasil!</strong><br>";
    echo "Tabel admin_users telah dibuat dan user default telah ditambahkan.<br><br>";
    echo "<strong>Login credentials:</strong><br>";
    echo "Username: admin | Password: admin123<br>";
    echo "Username: eduflora_admin | Password: password123<br>";
    echo "</div>";
    
    echo "<p><a href='login.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>← Kembali ke Login</a></p>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px 0;'>";
    echo "<strong>Ada error dalam setup!</strong><br>";
    echo "Silakan periksa error di atas dan perbaiki sebelum melanjutkan.";
    echo "</div>";
}

// Tampilkan struktur tabel yang ada
echo "<hr>";
echo "<h3>Tabel yang ada di database:</h3>";
$tables_result = mysqli_query($conn, "SHOW TABLES");
while ($table = mysqli_fetch_array($tables_result)) {
    echo "- " . $table[0] . "<br>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - EduFlora</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h2, h3 { color: #333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
</body>
</html>