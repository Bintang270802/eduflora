<?php
function renderFooter() {
    echo '<footer class="footer">';
    echo '<div class="container">';
    echo '<div class="footer-content">';
    
    // Footer Logo Section
    echo '<div class="footer-section">';
    echo '<div class="footer-logo">';
    echo '<i class="fas fa-leaf"></i>';
    echo '<span>EduFlora</span>';
    echo '</div>';
    echo '<p>Platform edukasi flora dan fauna Indonesia terlengkap dan terpercaya.</p>';
    echo '<div class="social-links">';
    echo '<a href="#"><i class="fab fa-facebook"></i></a>';
    echo '<a href="#"><i class="fab fa-twitter"></i></a>';
    echo '<a href="#"><i class="fab fa-instagram"></i></a>';
    echo '<a href="#"><i class="fab fa-youtube"></i></a>';
    echo '</div>';
    echo '</div>';
    
    // Navigation Section
    echo '<div class="footer-section">';
    echo '<h4>Navigasi</h4>';
    echo '<ul>';
    echo '<li><a href="index.php">Beranda</a></li>';
    echo '<li><a href="flora.php">Flora</a></li>';
    echo '<li><a href="fauna.php">Fauna</a></li>';
    echo '<li><a href="index.php#about">Tentang</a></li>';
    echo '</ul>';
    echo '</div>';
    
    // Contact Section
    echo '<div class="footer-section">';
    echo '<h4>Kontak</h4>';
    echo '<div class="contact-info">';
    echo '<p><i class="fas fa-envelope"></i> info@eduflora.id</p>';
    echo '<p><i class="fas fa-phone"></i> +62 21 1234 5678</p>';
    echo '<p><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</p>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>';
    echo '<div class="footer-bottom">';
    echo '<p>&copy; 2026 EduFlora. Semua hak cipta dilindungi.</p>';
    echo '</div>';
    echo '</div>';
    echo '</footer>';
}

function renderScripts() {
    echo '<script src="assets/js/script.js"></script>';
    echo '</body>';
    echo '</html>';
}
?>