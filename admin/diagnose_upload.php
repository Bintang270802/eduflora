<?php
session_start();

// Comprehensive upload diagnosis for EduFlora
echo "<!DOCTYPE html>";
echo "<html lang='id'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Upload Diagnosis - EduFlora Admin</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; max-width: 1000px; }";
echo ".section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }";
echo ".success { background: #d4edda; border-color: #c3e6cb; color: #155724; }";
echo ".error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }";
echo ".warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }";
echo ".info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
echo "th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }";
echo "th { background-color: #f2f2f2; }";
echo ".test-image { max-width: 150px; max-height: 150px; border: 1px solid #ddd; margin: 5px; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 EduFlora Upload System Diagnosis</h1>";

// 1. PHP Configuration Check
echo "<div class='section info'>";
echo "<h2>1. PHP Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$php_checks = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir') ?: 'Default'
];

foreach ($php_checks as $setting => $value) {
    $status = 'info';
    if ($setting === 'file_uploads' && !$value) $status = 'error';
    echo "<tr class='$status'><td>$setting</td><td>$value</td><td>";
    if ($setting === 'file_uploads') echo $value ? '✓ Enabled' : '✗ Disabled';
    else echo '✓ OK';
    echo "</td></tr>";
}
echo "</table>";
echo "</div>";

// 2. Directory Structure Check
echo "<div class='section info'>";
echo "<h2>2. Directory Structure</h2>";

$directories = [
    '../assets/' => 'Assets directory',
    '../assets/images/' => 'Images directory',
    '../config/' => 'Config directory',
    '../admin/' => 'Admin directory'
];

echo "<table>";
echo "<tr><th>Directory</th><th>Path</th><th>Exists</th><th>Writable</th><th>Permissions</th></tr>";

foreach ($directories as $dir => $desc) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    $perms = $exists ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A';
    $realpath = $exists ? realpath($dir) : 'Not found';
    
    $status = $exists && $writable ? 'success' : 'error';
    echo "<tr class='$status'>";
    echo "<td>$desc</td>";
    echo "<td>$realpath</td>";
    echo "<td>" . ($exists ? '✓ Yes' : '✗ No') . "</td>";
    echo "<td>" . ($writable ? '✓ Yes' : '✗ No') . "</td>";
    echo "<td>$perms</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// 3. Image Files Check
echo "<div class='section info'>";
echo "<h2>3. Existing Image Files</h2>";

$image_dir = '../assets/images/';
if (is_dir($image_dir)) {
    $images = glob($image_dir . '*');
    echo "<table>";
    echo "<tr><th>File</th><th>Size</th><th>Type</th><th>Readable</th><th>Preview</th></tr>";
    
    foreach ($images as $image) {
        $filename = basename($image);
        $size = filesize($image);
        $readable = is_readable($image);
        $type = pathinfo($image, PATHINFO_EXTENSION);
        
        echo "<tr>";
        echo "<td>$filename</td>";
        echo "<td>" . number_format($size) . " bytes</td>";
        echo "<td>$type</td>";
        echo "<td>" . ($readable ? '✓ Yes' : '✗ No') . "</td>";
        echo "<td>";
        if ($readable && in_array(strtolower($type), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
            echo "<img src='../assets/images/$filename' class='test-image' alt='$filename' onerror='this.style.display=\"none\"'>";
        } else {
            echo "No preview";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>Images directory not found!</p>";
}
echo "</div>";

// 4. Database Connection Check
echo "<div class='section info'>";
echo "<h2>4. Database Connection</h2>";

try {
    include '../config/database.php';
    if (isset($conn) && $conn) {
        echo "<p class='success'>✓ Database connection successful</p>";
        
        // Check flora table
        $flora_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM flora");
        if ($flora_result) {
            $flora_count = mysqli_fetch_assoc($flora_result)['count'];
            echo "<p>Flora records: $flora_count</p>";
        }
        
        // Check fauna table
        $fauna_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM fauna");
        if ($fauna_result) {
            $fauna_count = mysqli_fetch_assoc($fauna_result)['count'];
            echo "<p>Fauna records: $fauna_count</p>";
        }
        
        // Check image paths in database
        echo "<h3>Image Paths in Database:</h3>";
        $image_check = mysqli_query($conn, "
            SELECT 'flora' as type, nama, image FROM flora 
            UNION ALL 
            SELECT 'fauna' as type, nama, image FROM fauna 
            ORDER BY type, nama
        ");
        
        if ($image_check) {
            echo "<table>";
            echo "<tr><th>Type</th><th>Name</th><th>Image Path</th><th>File Exists</th></tr>";
            while ($row = mysqli_fetch_assoc($image_check)) {
                $file_exists = file_exists('../' . $row['image']);
                $status = $file_exists ? 'success' : 'error';
                echo "<tr class='$status'>";
                echo "<td>" . ucfirst($row['type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                echo "<td>" . htmlspecialchars($row['image']) . "</td>";
                echo "<td>" . ($file_exists ? '✓ Yes' : '✗ No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p class='error'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Upload Test
if ($_POST && isset($_FILES['test_upload'])) {
    echo "<div class='section info'>";
    echo "<h2>5. Upload Test Results</h2>";
    
    $file = $_FILES['test_upload'];
    echo "<h3>File Information:</h3>";
    echo "<ul>";
    echo "<li>Original name: " . htmlspecialchars($file['name']) . "</li>";
    echo "<li>MIME type: " . htmlspecialchars($file['type']) . "</li>";
    echo "<li>Size: " . number_format($file['size']) . " bytes</li>";
    echo "<li>Error code: " . $file['error'] . "</li>";
    echo "<li>Temporary file: " . htmlspecialchars($file['tmp_name']) . "</li>";
    echo "</ul>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        echo "<h3>Validation:</h3>";
        echo "<ul>";
        echo "<li>File extension: $file_extension</li>";
        echo "<li>Extension allowed: " . (in_array($file_extension, $allowed_types) ? 'Yes' : 'No') . "</li>";
        echo "<li>Size within limit: " . ($file['size'] <= 5 * 1024 * 1024 ? 'Yes' : 'No') . "</li>";
        echo "<li>Temporary file exists: " . (file_exists($file['tmp_name']) ? 'Yes' : 'No') . "</li>";
        echo "</ul>";
        
        if (in_array($file_extension, $allowed_types) && $file['size'] <= 5 * 1024 * 1024) {
            $upload_dir = '../assets/images/';
            $new_filename = 'test_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            echo "<h3>Upload Attempt:</h3>";
            echo "<ul>";
            echo "<li>Target directory: " . realpath($upload_dir) . "</li>";
            echo "<li>Target filename: $new_filename</li>";
            echo "<li>Full upload path: $upload_path</li>";
            echo "</ul>";
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<div class='success'>";
                echo "<h3>✓ UPLOAD SUCCESSFUL!</h3>";
                echo "<p>File uploaded successfully to: $upload_path</p>";
                echo "<p>File size on disk: " . number_format(filesize($upload_path)) . " bytes</p>";
                echo "<h4>Uploaded Image:</h4>";
                echo "<img src='../assets/images/$new_filename' style='max-width: 300px; border: 1px solid #ddd;' alt='Test upload'>";
                echo "</div>";
                
                // Clean up test file
                echo "<script>setTimeout(() => { 
                    fetch('diagnose_upload.php?cleanup=$new_filename'); 
                }, 10000);</script>";
                echo "<p><small>Test file will be automatically deleted in 10 seconds.</small></p>";
            } else {
                echo "<div class='error'>";
                echo "<h3>✗ UPLOAD FAILED!</h3>";
                echo "<p>move_uploaded_file() returned false</p>";
                
                $last_error = error_get_last();
                if ($last_error) {
                    echo "<p>Last PHP error: " . htmlspecialchars($last_error['message']) . "</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='error'>";
            echo "<h3>✗ VALIDATION FAILED!</h3>";
            echo "<p>File did not pass validation checks.</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>";
        echo "<h3>✗ UPLOAD ERROR!</h3>";
        
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by PHP extension'
        ];
        
        $error_msg = isset($upload_errors[$file['error']]) ? $upload_errors[$file['error']] : 'Unknown error';
        echo "<p>Error: $error_msg (Code: {$file['error']})</p>";
        echo "</div>";
    }
    echo "</div>";
}

// Handle cleanup
if (isset($_GET['cleanup'])) {
    $cleanup_file = '../assets/images/' . basename($_GET['cleanup']);
    if (file_exists($cleanup_file) && strpos(basename($_GET['cleanup']), 'test_') === 0) {
        unlink($cleanup_file);
        echo "Test file cleaned up";
    }
    exit;
}

// Upload test form
echo "<div class='section warning'>";
echo "<h2>5. Upload Test</h2>";
echo "<p>Test the upload functionality by selecting an image file:</p>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='test_upload' accept='image/*' required>";
echo "<button type='submit' style='margin-left: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>Test Upload</button>";
echo "</form>";
echo "</div>";

// 6. Recommendations
echo "<div class='section info'>";
echo "<h2>6. Recommendations</h2>";
echo "<ul>";
echo "<li>Ensure the assets/images/ directory has write permissions (755 or 777)</li>";
echo "<li>Check that PHP file_uploads is enabled</li>";
echo "<li>Verify upload_max_filesize and post_max_size are adequate (at least 5MB)</li>";
echo "<li>Make sure the web server has permission to write to the upload directory</li>";
echo "<li>Test with different image formats (JPG, PNG, GIF, WebP)</li>";
echo "<li>Check server error logs if uploads continue to fail</li>";
echo "</ul>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Navigation</h2>";
echo "<p><a href='flora.php'>← Back to Flora Management</a></p>";
echo "<p><a href='fauna.php'>← Back to Fauna Management</a></p>";
echo "<p><a href='test_upload.php'>→ Simple Upload Test</a></p>";
echo "<p><a href='../test_images.html'>→ Image Resources Test</a></p>";
echo "</div>";

echo "</body>";
echo "</html>";
?>