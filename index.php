<?php
session_start();
include 'config/database.php';

// Ambil data flora dan fauna untuk ditampilkan
$query_flora = "SELECT * FROM flora ORDER BY created_at DESC LIMIT 6";
$query_fauna = "SELECT * FROM fauna ORDER BY created_at DESC LIMIT 6";

$result_flora = mysqli_query($conn, $query_flora);
$result_fauna = mysqli_query($conn, $query_fauna);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlora - Sistem Informasi Edukasi Flora dan Fauna</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-leaf"></i>
                <span>EduFlora</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#home" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="#flora" class="nav-link"><i class="fas fa-seedling"></i> Flora</a></li>
                <li><a href="#fauna" class="nav-link"><i class="fas fa-paw"></i> Fauna</a></li>
                <li><a href="#about" class="nav-link"><i class="fas fa-info-circle"></i> Tentang</a></li>
                <li><a href="admin/login.php" class="nav-link admin-btn"><i class="fas fa-user-shield"></i> Admin</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="gradient-text">Jelajahi Keajaiban</span><br>
                    Flora & Fauna Indonesia
                </h1>
                <p class="hero-description">
                    Temukan kekayaan biodiversitas Indonesia melalui sistem informasi edukasi yang interaktif dan komprehensif. 
                    Pelajari berbagai spesies flora dan fauna dengan informasi lengkap dan gambar berkualitas tinggi.
                </p>
                <div class="hero-buttons">
                    <a href="#flora" class="btn btn-primary">
                        <i class="fas fa-seedling"></i> Eksplorasi Flora
                    </a>
                    <a href="#fauna" class="btn btn-secondary">
                        <i class="fas fa-paw"></i> Eksplorasi Fauna
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <div class="floating-card">
                    <i class="fas fa-leaf"></i>
                    <span>1000+ Spesies Flora</span>
                </div>
                <div class="floating-card">
                    <i class="fas fa-dove"></i>
                    <span>800+ Spesies Fauna</span>
                </div>
            </div>
        </div>
        <div class="hero-bg-animation">
            <div class="floating-element"><i class="fas fa-leaf"></i></div>
            <div class="floating-element"><i class="fas fa-butterfly"></i></div>
            <div class="floating-element"><i class="fas fa-seedling"></i></div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="stat-number" data-target="1000">0</div>
                    <div class="stat-label">Spesies Flora</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-paw"></i>
                    </div>
                    <div class="stat-number" data-target="800">0</div>
                    <div class="stat-label">Spesies Fauna</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-target="50000">0</div>
                    <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe-asia"></i>
                    </div>
                    <div class="stat-number" data-target="34">0</div>
                    <div class="stat-label">Provinsi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flora Section -->
    <section id="flora" class="flora-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-seedling"></i>
                    Koleksi Flora Indonesia
                </h2>
                <p class="section-description">
                    Jelajahi keanekaragaman tumbuhan Indonesia dari Sabang sampai Merauke. 
                    Setiap spesies dilengkapi dengan informasi detail, habitat, dan manfaatnya.
                </p>
            </div>
            <div class="flora-grid">
                <?php if(mysqli_num_rows($result_flora) > 0): ?>
                    <?php while($flora = mysqli_fetch_assoc($result_flora)): ?>
                        <div class="flora-card">
                            <div class="card-image">
                                <img src="<?php echo $flora['image']; ?>" alt="<?php echo $flora['nama']; ?>">
                                <div class="card-overlay">
                                    <button class="btn-detail" onclick="showDetail('flora', <?php echo $flora['id']; ?>)">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $flora['nama']; ?></h3>
                                <p class="card-scientific"><?php echo $flora['nama_ilmiah']; ?></p>
                                <p class="card-description"><?php echo substr($flora['deskripsi'], 0, 100); ?>...</p>
                                <div class="card-tags">
                                    <span class="tag"><?php echo $flora['habitat']; ?></span>
                                    <span class="tag"><?php echo $flora['status_konservasi']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-seedling"></i>
                        <p>Belum ada data flora tersedia</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="section-footer">
                <a href="flora.php" class="btn btn-outline">
                    <i class="fas fa-arrow-right"></i> Lihat Semua Flora
                </a>
            </div>
        </div>
    </section>

    <!-- Fauna Section -->
    <section id="fauna" class="fauna-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-paw"></i>
                    Koleksi Fauna Indonesia
                </h2>
                <p class="section-description">
                    Temukan keunikan satwa Indonesia yang menakjubkan. Dari mamalia hingga reptil, 
                    setiap spesies memiliki cerita dan peran penting dalam ekosistem.
                </p>
            </div>
            <div class="fauna-grid">
                <?php if(mysqli_num_rows($result_fauna) > 0): ?>
                    <?php while($fauna = mysqli_fetch_assoc($result_fauna)): ?>
                        <div class="fauna-card">
                            <div class="card-image">
                                <img src="<?php echo $fauna['image']; ?>" alt="<?php echo $fauna['nama']; ?>">
                                <div class="card-overlay">
                                    <button class="btn-detail" onclick="showDetail('fauna', <?php echo $fauna['id']; ?>)">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $fauna['nama']; ?></h3>
                                <p class="card-scientific"><?php echo $fauna['nama_ilmiah']; ?></p>
                                <p class="card-description"><?php echo substr($fauna['deskripsi'], 0, 100); ?>...</p>
                                <div class="card-tags">
                                    <span class="tag"><?php echo $fauna['habitat']; ?></span>
                                    <span class="tag"><?php echo $fauna['status_konservasi']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-paw"></i>
                        <p>Belum ada data fauna tersedia</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="section-footer">
                <a href="fauna.php" class="btn btn-outline">
                    <i class="fas fa-arrow-right"></i> Lihat Semua Fauna
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Tentang EduFlora
                    </h2>
                    <p class="about-description">
                        EduFlora adalah platform edukasi digital yang didedikasikan untuk melestarikan dan 
                        menyebarkan pengetahuan tentang keanekaragaman hayati Indonesia. Kami berkomitmen 
                        untuk menyediakan informasi yang akurat, lengkap, dan mudah diakses.
                    </p>
                    <div class="features-grid">
                        <div class="feature-item">
                            <i class="fas fa-database"></i>
                            <h4>Database Lengkap</h4>
                            <p>Ribuan spesies flora dan fauna dengan informasi detail</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt"></i>
                            <h4>Responsive Design</h4>
                            <p>Akses mudah dari berbagai perangkat</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-search"></i>
                            <h4>Pencarian Canggih</h4>
                            <p>Temukan spesies dengan mudah dan cepat</p>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-heart"></i>
                            <h4>Konservasi</h4>
                            <p>Mendukung upaya pelestarian alam Indonesia</p>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <div class="image-placeholder">
                        <i class="fas fa-globe-asia"></i>
                        <p>Indonesia Biodiversity</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-leaf"></i>
                        <span>EduFlora</span>
                    </div>
                    <p>Platform edukasi flora dan fauna Indonesia terlengkap dan terpercaya.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Navigasi</h4>
                    <ul>
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#flora">Flora</a></li>
                        <li><a href="#fauna">Fauna</a></li>
                        <li><a href="#about">Tentang</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Kontak</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> info@eduflora.id</p>
                        <p><i class="fas fa-phone"></i> +62 21 1234 5678</p>
                        <p><i class="fas fa-map-marker-alt"></i> Jakarta, Indonesia</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 EduFlora. Semua hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Detail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>