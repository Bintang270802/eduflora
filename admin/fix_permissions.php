<?php
// Script to fix directory permissions for EduFlora upload functionality

echo "<!DOCTYPE html>";
echo "<html lang='id'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Fix Permissions - EduFlora</title>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔧 EduFlora Permission Fix</h1>";

$directories_to_fix = [
    '../assets/' => 'Assets directory',
    '../assets/images/' => 'Images upload directory',
    '../assets/css/' => 'CSS directory',
    '../assets/js/' => 'JavaScript directory'
];

echo "<h2>Directory Permission Check & Fix</h2>";

foreach ($directories_to_fix as $dir => $description) {
    echo "<h3>$description ($dir)</h3>";
    
    // Check if directory exists
    if (!is_dir($dir)) {
        echo "<p class='info'>Directory doesn't exist. Creating...</p>";
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✓ Directory created successfully</p>";
        } else {
            echo "<p class='error'>✗ Failed to create directory</p>";
            continue;
        }
    } else {
        echo "<p class='success'>✓ Directory exists</p>";
    }
    
    // Check current permissions
    $current_perms = substr(sprintf('%o', fileperms($dir)), -4);
    echo "<p>Current permissions: $current_perms</p>";
    
    // Check if writable
    if (is_writable($dir)) {
        echo "<p class='success'>✓ Directory is writable</p>";
    } else {
        echo "<p class='error'>✗ Directory is not writable. Attempting to fix...</p>";
        
        // Try to fix permissions
        if (chmod($dir, 0755)) {
            echo "<p class='success'>✓ Permissions fixed (set to 0755)</p>";
        } else {
            echo "<p class='error'>✗ Failed to fix permissions. You may need to manually set permissions.</p>";
            echo "<p class='info'>Manual fix: Run 'chmod 755 $dir' on your server</p>";
        }
    }
    
    // Show real path
    echo "<p>Real path: " . realpath($dir) . "</p>";
    echo "<hr>";
}

echo "<h2>File Permission Check</h2>";

$important_files = [
    '../config/database.php' => 'Database configuration',
    '../index.php' => 'Main index file',
    'flora.php' => 'Flora management',
    'fauna.php' => 'Fauna management'
];

foreach ($important_files as $file => $description) {
    echo "<h3>$description ($file)</h3>";
    
    if (file_exists($file)) {
        $readable = is_readable($file);
        $writable = is_writable($file);
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        
        echo "<p>Permissions: $perms</p>";
        echo "<p>Readable: " . ($readable ? '✓ Yes' : '✗ No') . "</p>";
        echo "<p>Writable: " . ($writable ? '✓ Yes' : '✗ No') . "</p>";
        
        if (!$readable) {
            echo "<p class='error'>File is not readable!</p>";
        }
    } else {
        echo "<p class='error'>✗ File not found</p>";
    }
    echo "<hr>";
}

echo "<h2>Create .htaccess for Images Directory</h2>";

$htaccess_content = "# Allow image files
<FilesMatch \"\\.(jpg|jpeg|png|gif|svg|webp)$\">
    Order allow,deny
    Allow from all
</FilesMatch>

# Deny access to PHP files in images directory
<FilesMatch \"\\.php$\">
    Order deny,allow
    Deny from all
</FilesMatch>

# Enable MIME type detection
AddType image/svg+xml .svg
AddType image/webp .webp
";

$htaccess_path = '../assets/images/.htaccess';

if (file_exists($htaccess_path)) {
    echo "<p class='success'>✓ .htaccess already exists in images directory</p>";
} else {
    if (file_put_contents($htaccess_path, $htaccess_content)) {
        echo "<p class='success'>✓ .htaccess created successfully in images directory</p>";
    } else {
        echo "<p class='error'>✗ Failed to create .htaccess file</p>";
    }
}

echo "<h2>Test Image Upload Directory</h2>";

$test_file_content = "Test file created at " . date('Y-m-d H:i:s');
$test_file_path = '../assets/images/test_write.txt';

if (file_put_contents($test_file_path, $test_file_content)) {
    echo "<p class='success'>✓ Successfully created test file in upload directory</p>";
    echo "<p>Test file path: " . realpath($test_file_path) . "</p>";
    
    // Clean up test file
    if (unlink($test_file_path)) {
        echo "<p class='success'>✓ Test file cleaned up successfully</p>";
    } else {
        echo "<p class='error'>✗ Failed to clean up test file</p>";
    }
} else {
    echo "<p class='error'>✗ Failed to create test file in upload directory</p>";
    echo "<p class='info'>This indicates a permission problem with the upload directory</p>";
}

echo "<h2>PHP Configuration Summary</h2>";
echo "<ul>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . "</li>";
echo "<li>memory_limit: " . ini_get('memory_limit') . "</li>";
echo "</ul>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If any directories show permission errors, manually set them to 755 or 777</li>";
echo "<li>Ensure your web server (Apache/Nginx) has write access to the assets/images/ directory</li>";
echo "<li>Test the upload functionality using the diagnosis tool</li>";
echo "<li>Check server error logs if problems persist</li>";
echo "</ol>";

echo "<h2>Navigation</h2>";
echo "<p><a href='diagnose_upload.php'>→ Run Upload Diagnosis</a></p>";
echo "<p><a href='test_upload.php'>→ Simple Upload Test</a></p>";
echo "<p><a href='flora.php'>← Back to Flora Management</a></p>";
echo "<p><a href='fauna.php'>← Back to Fauna Management</a></p>";

echo "</body>";
echo "</html>";
?>