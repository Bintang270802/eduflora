<?php
function renderSearchSection($search = '', $habitat_filter = '', $status_filter = '', $habitat_options = [], $status_options = [], $type = 'flora') {
    echo '<section class="search-section">';
    echo '<div class="container">';
    echo '<form method="GET" class="search-form">';
    echo '<div class="search-grid">';
    
    // Search Input
    echo '<div class="search-input-group">';
    echo '<i class="fas fa-search"></i>';
    echo '<input type="text" name="search" placeholder="Cari ' . $type . ' berdasarkan nama atau deskripsi..." value="' . htmlspecialchars($search) . '">';
    echo '</div>';
    
    // Habitat Select
    echo '<select name="habitat">';
    echo '<option value="">Semua Habitat</option>';
    foreach ($habitat_options as $habitat) {
        $selected = ($habitat_filter === $habitat['habitat']) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($habitat['habitat']) . '"' . $selected . '>';
        echo htmlspecialchars($habitat['habitat']);
        echo '</option>';
    }
    echo '</select>';
    
    // Status Select
    echo '<select name="status">';
    echo '<option value="">Semua Status</option>';
    foreach ($status_options as $status) {
        $selected = ($status_filter === $status['status_konservasi']) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($status['status_konservasi']) . '"' . $selected . '>';
        echo htmlspecialchars($status['status_konservasi']);
        echo '</option>';
    }
    echo '</select>';
    
    // Search Button
    echo '<button type="submit" class="search-btn">';
    echo '<i class="fas fa-search"></i>';
    echo 'Cari';
    echo '</button>';
    
    // Reset Button
    echo '<a href="' . $type . '.php" class="reset-btn-' . $type . '">';
    echo '<i class="fas fa-undo"></i>';
    echo 'Reset';
    echo '</a>';
    
    echo '</div>';
    echo '</form>';
    echo '</div>';
    echo '</section>';
}

function renderPageHero($title, $description, $icon, $stats = []) {
    echo '<section class="page-hero">';
    echo '<div class="container">';
    echo '<div class="hero-content">';
    echo '<h1 class="page-title">';
    echo '<i class="' . $icon . '"></i>';
    echo $title;
    echo '</h1>';
    echo '<p class="page-description">' . $description . '</p>';
    
    if (!empty($stats)) {
        echo '<div class="page-stats">';
        foreach ($stats as $stat) {
            echo '<div class="stat-item">';
            echo '<span class="stat-number">' . number_format($stat['number']) . '</span>';
            echo '<span class="stat-label">' . $stat['label'] . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</section>';
}

function renderCard($item, $type = 'flora') {
    echo '<div class="' . $type . '-card">';
    echo '<div class="card-image">';
    echo '<img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['nama']) . '" onerror="this.src=\'assets/images/default-' . $type . '.svg\'">';
    echo '<div class="card-overlay">';
    echo '<button class="btn-detail" onclick="showDetail(\'' . $type . '\', ' . $item['id'] . ')">';
    echo '<i class="fas fa-eye"></i> Lihat Detail';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '<div class="card-content">';
    echo '<h3 class="card-title">' . htmlspecialchars($item['nama']) . '</h3>';
    echo '<p class="card-scientific">' . htmlspecialchars($item['nama_ilmiah']) . '</p>';
    echo '<p class="card-description">' . substr(htmlspecialchars($item['deskripsi']), 0, 100) . '...</p>';
    echo '<div class="card-tags">';
    echo '<span class="tag">' . htmlspecialchars($item['habitat']) . '</span>';
    echo '<span class="tag">' . htmlspecialchars($item['status_konservasi']) . '</span>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function renderModal() {
    echo '<div id="detailModal" class="modal">';
    echo '<div class="modal-content">';
    echo '<span class="close">&times;</span>';
    echo '<div id="modalBody"></div>';
    echo '</div>';
    echo '</div>';
}
?>