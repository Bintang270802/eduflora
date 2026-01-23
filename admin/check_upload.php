<?php
// File untuk mengecek konfigurasi upload PHP
echo "<h2>PHP Upload Configuration Check</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . "</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . (ini_get('upload_tmp_dir') ?: 'Default') . "</td></tr>";
echo "</table>";

echo "<h3>Directory Permissions</h3>";
$upload_dir = '../assets/images/';
echo "<p>Upload directory: " . realpath($upload_dir) . "</p>";
echo "<p>Directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "</p>";

if ($_POST && isset($_FILES['test_upload'])) {
    echo "<h3>Upload Test Result</h3>";
    echo "<p>File error code: " . $_FILES['test_upload']['error'] . "</p>";
    echo "<p>File size: " . $_FILES['test_upload']['size'] . " bytes</p>";
    echo "<p>File type: " . $_FILES['test_upload']['type'] . "</p>";
    echo "<p>Temp file: " . $_FILES['test_upload']['tmp_name'] . "</p>";
    
    if ($_FILES['test_upload']['error'] === 0) {
        $test_path = $upload_dir . 'test_' . time() . '.txt';
        if (move_uploaded_file($_FILES['test_upload']['tmp_name'], $test_path)) {
            echo "<p style='color: green;'>Upload SUCCESS!</p>";
            unlink($test_path); // Clean up
        } else {
            echo "<p style='color: red;'>Upload FAILED!</p>";
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <h3>Test Upload</h3>
    <input type="file" name="test_upload" required>
    <button type="submit">Test Upload</button>
</form>