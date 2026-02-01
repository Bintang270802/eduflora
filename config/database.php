<?php
// Enhanced Database Configuration with Error Handling
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'eduflora_db';

// Connection timeout settings
$timeout = 10; // seconds

// Create connection with error handling
try {
    // Set connection timeout
    ini_set('mysql.connect_timeout', $timeout);
    ini_set('default_socket_timeout', $timeout);
    
    // Create connection
    $conn = mysqli_connect($host, $username, $password, $database);
    
    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to prevent encoding issues
    if (!mysqli_set_charset($conn, "utf8mb4")) {
        throw new Exception("Error setting charset: " . mysqli_error($conn));
    }
    
    // Set SQL mode for better compatibility
    mysqli_query($conn, "SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
    
    // Set timezone
    mysqli_query($conn, "SET time_zone = '+00:00'");
    
} catch (Exception $e) {
    // Log error (in production, use proper logging)
    error_log("Database connection error: " . $e->getMessage());
    
    // Display user-friendly error message
    die("
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Database Error - EduFlora</title>
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                background: #f8f9fa; 
                margin: 0; 
                padding: 20px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                min-height: 100vh; 
            }
            .error-container { 
                background: white; 
                padding: 2rem; 
                border-radius: 12px; 
                box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
                text-align: center; 
                max-width: 500px; 
                width: 100%; 
            }
            .error-icon { 
                font-size: 4rem; 
                color: #dc3545; 
                margin-bottom: 1rem; 
            }
            h1 { 
                color: #dc3545; 
                margin-bottom: 1rem; 
                font-size: 1.5rem; 
            }
            p { 
                color: #6c757d; 
                margin-bottom: 1.5rem; 
                line-height: 1.6; 
            }
            .btn { 
                background: #007bff; 
                color: white; 
                padding: 0.75rem 1.5rem; 
                border: none; 
                border-radius: 6px; 
                text-decoration: none; 
                display: inline-block; 
                transition: background 0.3s ease; 
            }
            .btn:hover { 
                background: #0056b3; 
                text-decoration: none; 
                color: white; 
            }
            @media (max-width: 768px) {
                .error-container { 
                    margin: 1rem; 
                    padding: 1.5rem; 
                }
                .error-icon { 
                    font-size: 3rem; 
                }
                h1 { 
                    font-size: 1.25rem; 
                }
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <div class='error-icon'>⚠️</div>
            <h1>Database Connection Error</h1>
            <p>Maaf, terjadi masalah koneksi ke database. Silakan coba lagi dalam beberapa saat atau hubungi administrator.</p>
            <a href='javascript:history.back()' class='btn'>Kembali</a>
        </div>
    </body>
    </html>
    ");
}

// Function to safely execute queries with error handling
function safe_query($conn, $query, $params = []) {
    try {
        if (empty($params)) {
            $result = mysqli_query($conn, $query);
            if (!$result) {
                throw new Exception("Query failed: " . mysqli_error($conn));
            }
            return $result;
        } else {
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . mysqli_error($conn));
            }
            
            if (!empty($params)) {
                $types = '';
                $values = [];
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                    $values[] = $param;
                }
                mysqli_stmt_bind_param($stmt, $types, ...$values);
            }
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
            }
            
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
    } catch (Exception $e) {
        error_log("Database query error: " . $e->getMessage());
        return false;
    }
}

// Function to safely escape strings (fallback for non-prepared statements)
function safe_escape($conn, $string) {
    return mysqli_real_escape_string($conn, trim($string));
}

// Function to check if table exists
function table_exists($conn, $table_name) {
    $query = "SHOW TABLES LIKE ?";
    $result = safe_query($conn, $query, [$table_name]);
    return $result && mysqli_num_rows($result) > 0;
}

// Function to get database info
function get_db_info($conn) {
    return [
        'server_info' => mysqli_get_server_info($conn),
        'client_info' => mysqli_get_client_info(),
        'host_info' => mysqli_get_host_info($conn),
        'protocol_version' => mysqli_get_proto_info($conn)
    ];
}
?>