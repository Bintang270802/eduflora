<?php
// Script untuk generate password hash
$passwords = [
    'admin123',
    'password123'
];

echo "<h2>Password Hash Generator</h2>";

foreach ($passwords as $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba;'>";
    echo "<strong>Password:</strong> $password<br>";
    echo "<strong>Hash:</strong> $hash<br>";
    echo "</div>";
}

// Test verification
echo "<hr><h3>Test Verification:</h3>";
$test_password = 'admin123';
$test_hash = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm';

if (password_verify($test_password, $test_hash)) {
    echo "<span style='color: green;'>✓ Password verification berhasil untuk '$test_password'</span>";
} else {
    echo "<span style='color: red;'>✗ Password verification gagal untuk '$test_password'</span>";
}
?>