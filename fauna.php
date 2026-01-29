<?php
include 'config/database.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$habitat_filter = isset($_GET['habitat']) ? mysqli_real_escape_string($conn, $_GET['habitat']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Build query
$where_conditions = [];
if ($search) {
    $where_conditions[] = "(nama LIKE '%$search%' OR nama_ilmiah LIKE '%$search%' OR deskripsi LIKE '%$search%')";
}
if ($habitat_filter) {
    $where_conditions[] = "habitat = '$habitat_filter'";
}
if ($status_filter) {
    $where_conditions[] = "status_konservasi = '$status_filter'";
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM fauna $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get fauna data
$query = "SELECT * FROM fauna $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get filter options
$habitat_options = mysqli_query($conn, "SELECT DISTINCT habitat FROM fauna ORDER BY habitat");
$status_options = mysqli_query($conn, "SELECT DISTINCT status_konservasi FROM fauna ORDER BY status_konservasi");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fauna Indonesia - EduFlora</title>
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
                <li><a href="index.php" class="nav-link"><i class="fas fa-home"></i> Beranda</a></li>
                <li><a href="flora.php" class="nav-link"><i class="fas fa-seedling"></i> Flora</a></li>
                <li><a href="fauna.php" class="nav-link active"><i class="fas fa-paw"></i> Fauna</a></li>
                <li><a href="index.php#about" class="nav-link"><i class="fas fa-info-circle"></i> Tentang</a></li>
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
    <section class="page-hero" style="background: var(--gradient-secondary);">
        <div class="container">
            <div class="hero-content">
                <h1 class="page-title">
                    <i class="fas fa-paw"></i>
                    Fauna Indonesia
                </h1>
                <p class="page-description">
                    Temukan keunikan satwa Indonesia yang menakjubkan. 
                    Dari mamalia hingga reptil, setiap spesies memiliki cerita dan peran penting dalam ekosistem nusantara.
                </p>
                <div class="page-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($total_records); ?></span>
                        <span class="stat-label">Spesies Fauna</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="search-section">
        <div class="container">
            <form method="GET" class="search-form">
                <div class="search-grid">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari fauna berdasarkan nama atau deskripsi..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <select name="habitat">
                        <option value="">Semua Habitat</option>
                        <?php while($habitat = mysqli_fetch_assoc($habitat_options)): ?>
                            <option value="<?php echo $habitat['habitat']; ?>" 
                                    <?php echo $habitat_filter === $habitat['habitat'] ? 'selected' : ''; ?>>
                                <?php echo $habitat['habitat']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <select name="status">
                        <option value="">Semua Status</option>
                        <?php while($status = mysqli_fetch_assoc($status_options)): ?>
                            <option value="<?php echo $status['status_konservasi']; ?>" 
                                    <?php echo $status_filter === $status['status_konservasi'] ? 'selected' : ''; ?>>
                                <?php echo $status['status_konservasi']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        Cari
                    </button>
                    <?php if (!empty($search) || !empty($habitat_filter) || !empty($status_filter)): ?>
                    <a href="fauna.php" class="reset-btn-fauna">
                        <i class="fas fa-undo"></i>
                        Reset
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- Fauna Grid Section -->
    <section class="fauna-grid-section">
        <div class="container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="fauna-grid">
                    <?php while($fauna = mysqli_fetch_assoc($result)): ?>
                        <div class="fauna-card">
                            <div class="card-image">
                                <img src="<?php echo $fauna['image']; ?>" alt="<?php echo $fauna['nama']; ?>" 
                                     onerror="this.src='assets/images/default-fauna.svg'">
                                <div class="card-overlay">
                                    <button class="btn-detail" onclick="showDetail('fauna', <?php echo $fauna['id']; ?>)">
                                        <i class="fas fa-eye"></i> Lihat Detail
                                    </button>
                                </div>
                                <div class="card-status">
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $fauna['status_konservasi'])); ?>">
                                        <?php echo $fauna['status_konservasi']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="card-title"><?php echo $fauna['nama']; ?></h3>
                                <p class="card-scientific"><?php echo $fauna['nama_ilmiah']; ?></p>
                                <p class="card-description"><?php echo substr($fauna['deskripsi'], 0, 120); ?>...</p>
                                <div class="card-info">
                                    <div class="info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo $fauna['asal_daerah']; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-tree"></i>
                                        <span><?php echo $fauna['habitat']; ?></span>
                                    </div>
                                    <?php if ($fauna['makanan']): ?>
                                    <div class="info-item">
                                        <i class="fas fa-utensils"></i>
                                        <span><?php echo $fauna['makanan']; ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-tags">
                                    <span class="tag"><?php echo $fauna['habitat']; ?></span>
                                    <span class="tag"><?php echo $fauna['status_konservasi']; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&habitat=<?php echo urlencode($habitat_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                               class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&habitat=<?php echo urlencode($habitat_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                                   class="pagination-number <?php echo $i === $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&habitat=<?php echo urlencode($habitat_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                               class="pagination-btn">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Tidak ada fauna yang ditemukan</h3>
                    <p>Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                    <a href="fauna.php" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> Reset Pencarian
                    </a>
                </div>
            <?php endif; ?>
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
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="flora.php">Flora</a></li>
                        <li><a href="fauna.php">Fauna</a></li>
                        <li><a href="index.php#about">Tentang</a></li>
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
                <p>&copy; 2026 EduFlora. Semua hak cipta dilindungi.</p>
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