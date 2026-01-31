<?php
// Komponen untuk menampilkan informasi session admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $admin_username = $_SESSION['admin_username'] ?? 'Unknown';
    $admin_full_name = $_SESSION['admin_full_name'] ?? 'Unknown User';
    $admin_role = $_SESSION['admin_role'] ?? 'admin';
    $admin_id = $_SESSION['admin_id'] ?? 0;
    
    echo "<div class='admin-session-info' style='background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #007cba;'>";
    echo "<div style='display: flex; justify-content: space-between; align-items: center;'>";
    echo "<div>";
    echo "<strong>Logged in as:</strong> $admin_full_name ($admin_username)<br>";
    echo "<small>Role: " . ucfirst($admin_role) . " | ID: $admin_id</small>";
    echo "</div>";
    echo "<div>";
    echo "<a href='logout.php' style='background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;'>";
    echo "<i class='fas fa-sign-out-alt'></i> Logout";
    echo "</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
?>