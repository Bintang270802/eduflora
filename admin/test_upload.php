<?php
session_start();

// Simple upload test without authentication for debugging
echo "<h2>Upload Test - EduFlora</h2>";

if ($_POST && isset($_FILES['test_image'])) {
    echo "<h3>Upload Attempt</h3>";
    
    // Debug information
    echo "<p><strong>File Info:</strong></p>";
    echo "<ul>";
    echo "<li>Name: " . $_FILES['test_image']['name'] . "</li>";
    echo "<li>Type: " . $_FILES['test_image']['type'] . "</li>";
    echo "<li>Size: " . $_FILES['test_image']['size'] . " bytes</li>";
    echo "<li>Error: " . $_FILES['test_image']['error'] . "</li>";
    echo "<li>Temp file: " . $_FILES['test_image']['tmp_name'] . "</li>";
    echo "</ul>";
    
    // Check if upload directory exists
    $upload_dir = '../assets/images/';
    echo "<p><strong>Directory Info:</strong></p>";
    echo "<ul>";
    echo "<li>Upload dir: " . realpath($upload_dir) . "</li>";
    echo "<li>Directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "</li>";
    echo "<li>Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "</li>";
    echo "<li>Directory permissions: " . (is_dir($upload_dir) ? substr(sprintf('%o', fileperms($upload_dir)), -4) : 'N/A') . "</li>";
    echo "</ul>";
    
    if ($_FILES['test_image']['error'] === 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['test_image']['name'], PATHINFO_EXTENSION));
        
        echo "<p><strong>Validation:</strong></p>";
        echo "<ul>";
        echo "<li>File extension: " . $file_extension . "</li>";
        echo "<li>Extension allowed: " . (in_array($file_extension, $allowed_types) ? 'Yes' : 'No') . "</li>";
        echo "<li>Size check: " . ($_FILES['test_image']['size'] <= 5 * 1024 * 1024 ? 'Pass' : 'Fail') . "</li>";
        echo "</ul>";
        
        if (in_array($file_extension, $allowed_types) && $_FILES['test_image']['size'] <= 5 * 1024 * 1024) {
            $new_name = 'test_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_name;
            
            echo "<p><strong>Upload Attempt:</strong></p>";
            echo "<ul>";
            echo "<li>Target path: " . $upload_path . "</li>";
            echo "<li>Temp file exists: " . (file_exists($_FILES['test_image']['tmp_name']) ? 'Yes' : 'No') . "</li>";
            echo "</ul>";
            
            if (move_uploaded_file($_FILES['test_image']['tmp_name'], $upload_path)) {
                echo "<p style='color: green; font-weight: bold;'>✓ UPLOAD SUCCESS!</p>";
                echo "<p>File saved as: " . $new_name . "</p>";
                echo "<p>Full path: " . realpath($upload_path) . "</p>";
                echo "<p>File size on disk: " . filesize($upload_path) . " bytes</p>";
                
                // Show the uploaded image
                echo "<h4>Uploaded Image:</h4>";
                echo "<img src='../assets/images/" . $new_name . "' style='max-width: 300px; border: 1px solid #ddd;' alt='Uploaded test image'>";
                
                // Clean up test file after 30 seconds
                echo "<p><small>Test file will be automatically deleted.</small></p>";
                echo "<script>setTimeout(() => { 
                    fetch('test_upload.php?cleanup=" . $new_name . "'); 
                }, 30000);</script>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>✗ UPLOAD FAILED!</p>";
                echo "<p>move_uploaded_file() returned false</p>";
                
                // Additional debugging
                $error = error_get_last();
                if ($error) {
                    echo "<p>Last error: " . $error['message'] . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>File validation failed!</p>";
        }
    } else {
        echo "<p style='color: red;'>Upload error code: " . $_FILES['test_image']['error'] . "</p>";
        
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        
        if (isset($upload_errors[$_FILES['test_image']['error']])) {
            echo "<p>Error meaning: " . $upload_errors[$_FILES['test_image']['error']] . "</p>";
        }
    }
}

// Handle cleanup
if (isset($_GET['cleanup'])) {
    $cleanup_file = '../assets/images/' . basename($_GET['cleanup']);
    if (file_exists($cleanup_file) && strpos(basename($_GET['cleanup']), 'test_') === 0) {
        unlink($cleanup_file);
        echo "Cleanup completed";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Test - EduFlora</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 800px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { padding: 10px; border: 2px dashed #ddd; width: 100%; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>EduFlora Upload Test</h1>
    
    <div class="info">
        <h3>PHP Configuration:</h3>
        <ul>
            <li>file_uploads: <?php echo ini_get('file_uploads') ? 'Enabled' : 'Disabled'; ?></li>
            <li>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
            <li>post_max_size: <?php echo ini_get('post_max_size'); ?></li>
            <li>max_execution_time: <?php echo ini_get('max_execution_time'); ?></li>
            <li>memory_limit: <?php echo ini_get('memory_limit'); ?></li>
        </ul>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="test_image">Select Image to Test Upload:</label>
            <input type="file" id="test_image" name="test_image" accept="image/*" required>
        </div>
        <button type="submit">Test Upload</button>
    </form>
    
    <div class="info">
        <h3>Test Instructions:</h3>
        <ol>
            <li>Select any image file (JPG, PNG, GIF, WebP)</li>
            <li>Click "Test Upload" to test the upload functionality</li>
            <li>Check the results above for any errors</li>
            <li>Test files are automatically cleaned up after 30 seconds</li>
        </ol>
    </div>
    
    <p><a href="flora.php">← Back to Flora Management</a></p>
    <p><a href="fauna.php">← Back to Fauna Management</a></p>
</body>
</html>