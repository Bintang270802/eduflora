<?php
function renderHeader($title = "EduFlora - Sistem Informasi Edukasi Flora dan Fauna", $page = "home") {
    echo '<!DOCTYPE html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . htmlspecialchars($title) . '</title>';
    echo '<link rel="stylesheet" href="assets/css/style.css">';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">';
    echo '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">';
    echo '</head>';
    echo '<body>';
}

function renderNavigation($active_page = "home") {
    echo '<nav class="navbar">';
    echo '<div class="nav-container">';
    echo '<div class="nav-logo">';
    echo '<i class="fas fa-leaf"></i>';
    echo '<span>EduFlora</span>';
    echo '</div>';
    echo '<ul class="nav-menu">';
    
    $nav_items = [
        'home' => ['url' => 'index.php', 'icon' => 'fas fa-home', 'text' => 'Beranda'],
        'flora' => ['url' => 'flora.php', 'icon' => 'fas fa-seedling', 'text' => 'Flora'],
        'fauna' => ['url' => 'fauna.php', 'icon' => 'fas fa-paw', 'text' => 'Fauna'],
        'about' => ['url' => 'index.php#about', 'icon' => 'fas fa-info-circle', 'text' => 'Tentang'],
        'admin' => ['url' => 'admin/login.php', 'icon' => 'fas fa-user-shield', 'text' => 'Admin']
    ];
    
    foreach ($nav_items as $key => $item) {
        $active_class = ($active_page === $key) ? ' active' : '';
        $admin_class = ($key === 'admin') ? ' admin-btn' : '';
        echo '<li><a href="' . $item['url'] . '" class="nav-link' . $active_class . $admin_class . '">';
        echo '<i class="' . $item['icon'] . '"></i> ' . $item['text'];
        echo '</a></li>';
    }
    
    echo '</ul>';
    echo '<div class="hamburger">';
    echo '<span></span>';
    echo '<span></span>';
    echo '<span></span>';
    echo '</div>';
    echo '</div>';
    echo '</nav>';
}
?>