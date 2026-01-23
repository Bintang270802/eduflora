<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/database.php';

// Get flora ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: flora.php');
    exit();
}

// Get flora data
$query = "SELECT * FROM flora WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$flora = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$flora) {
    header('Location: flora.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Flora - EduFlora Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-leaf"></i>
                    <span>EduFlora Admin</span>
                </div>
            </div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="flora.php" class="active"><i class="fas fa-seedling"></i> Kelola Flora</a></li>
                    <li><a href="fauna.php"><i class="fas fa-paw"></i> Kelola Fauna</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Website</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1 class="admin-title">
                        <i class="fas fa-eye"></i>
                        Detail Flora: <?php echo htmlspecialchars($flora['nama']); ?>
                    </h1>
                    <div class="breadcrumb">
                        <a href="flora.php">Kelola Flora</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Detail Flora</span>
                    </div>
                </div>
                <div class="admin-user">
                    <div class="user-info">
                        <div class="user-name">Admin</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="flora.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Daftar
                    </a>
                    <a href="flora_edit.php?id=<?php echo $flora['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Edit Flora
                    </a>
                    <a href="../get_detail.php?type=flora&id=<?php echo $flora['id']; ?>" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt"></i>
                        Lihat di Website
                    </a>
                    <button onclick="printDetail()" class="btn btn-outline">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>

                <!-- Detail Container -->
                <div class="detail-container">
                    <!-- Header Section -->
                    <div class="detail-header">
                        <div class="detail-image">
                            <img src="../<?php echo $flora['image']; ?>" alt="<?php echo $flora['nama']; ?>" 
                                 onerror="this.src='../assets/images/default-flora.svg'">
                            <div class="image-overlay">
                                <button onclick="viewFullImage()" class="btn btn-light">
                                    <i class="fas fa-expand"></i>
                                    Lihat Gambar Penuh
                                </button>
                            </div>
                        </div>
                        <div class="detail-info">
                            <h2 class="detail-title"><?php echo htmlspecialchars($flora['nama']); ?></h2>
                            <p class="detail-scientific"><?php echo htmlspecialchars($flora['nama_ilmiah']); ?></p>
                            
                            <div class="detail-badges">
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $flora['status_konservasi'])); ?>">
                                    <i class="fas fa-shield-alt"></i>
                                    <?php echo $flora['status_konservasi']; ?>
                                </span>
                                <span class="habitat-badge">
                                    <i class="fas fa-tree"></i>
                                    <?php echo $flora['habitat']; ?>
                                </span>
                                <span class="location-badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $flora['asal_daerah']; ?>
                                </span>
                            </div>

                            <div class="detail-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Ditambahkan: <?php echo date('d F Y, H:i', strtotime($flora['created_at'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-edit"></i>
                                    <span>Diperbarui: <?php echo date('d F Y, H:i', strtotime($flora['updated_at'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    <span>ID: <?php echo $flora['id']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Sections -->
                    <div class="detail-content">
                        <!-- Deskripsi -->
                        <div class="content-section">
                            <h3 class="section-title">
                                <i class="fas fa-align-left"></i>
                                Deskripsi
                            </h3>
                            <div class="section-content">
                                <p><?php echo nl2br(htmlspecialchars($flora['deskripsi'])); ?></p>
                            </div>
                        </div>

                        <!-- Habitat Detail -->
                        <?php if ($flora['habitat_detail']): ?>
                        <div class="content-section">
                            <h3 class="section-title">
                                <i class="fas fa-map"></i>
                                Detail Habitat
                            </h3>
                            <div class="section-content">
                                <p><?php echo nl2br(htmlspecialchars($flora['habitat_detail'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Manfaat -->
                        <?php if ($flora['manfaat']): ?>
                        <div class="content-section">
                            <h3 class="section-title">
                                <i class="fas fa-heart"></i>
                                Manfaat
                            </h3>
                            <div class="section-content">
                                <p><?php echo nl2br(htmlspecialchars($flora['manfaat'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Ciri Khusus -->
                        <?php if ($flora['ciri_khusus']): ?>
                        <div class="content-section">
                            <h3 class="section-title">
                                <i class="fas fa-eye"></i>
                                Ciri Khusus
                            </h3>
                            <div class="section-content">
                                <p><?php echo nl2br(htmlspecialchars($flora['ciri_khusus'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Informasi Tambahan -->
                        <div class="content-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Tambahan
                            </h3>
                            <div class="section-content">
                                <div class="info-grid">
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fas fa-tree"></i>
                                        </div>
                                        <div class="info-content">
                                            <h4>Habitat Utama</h4>
                                            <p><?php echo htmlspecialchars($flora['habitat']); ?></p>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <h4>Asal Daerah</h4>
                                            <p><?php echo htmlspecialchars($flora['asal_daerah']); ?></p>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <h4>Status Konservasi</h4>
                                            <p><?php echo htmlspecialchars($flora['status_konservasi']); ?></p>
                                        </div>
                                    </div>
                                    <div class="info-card">
                                        <div class="info-icon">
                                            <i class="fas fa-microscope"></i>
                                        </div>
                                        <div class="info-content">
                                            <h4>Nama Ilmiah</h4>
                                            <p><em><?php echo htmlspecialchars($flora['nama_ilmiah']); ?></em></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Full Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content image-modal-content">
            <span class="close">&times;</span>
            <img id="fullImage" src="" alt="">
            <div class="image-info">
                <h3><?php echo htmlspecialchars($flora['nama']); ?></h3>
                <p><?php echo htmlspecialchars($flora['nama_ilmiah']); ?></p>
            </div>
        </div>
    </div>

    <script>
        // View full image
        function viewFullImage() {
            const modal = document.getElementById('imageModal');
            const fullImage = document.getElementById('fullImage');
            
            fullImage.src = '../<?php echo $flora['image']; ?>';
            modal.style.display = 'block';
        }

        // Close modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('imageModal').style.display = 'none';
        });

        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Print functionality
        function printDetail() {
            window.print();
        }

        // Add smooth scrolling to sections
        document.querySelectorAll('.section-title').forEach(title => {
            title.addEventListener('click', function() {
                this.parentElement.classList.toggle('collapsed');
            });
        });

        // Add animation to info cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(20px)';
                    entry.target.style.transition = 'all 0.6s ease-out';
                    
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.info-card, .content-section').forEach(el => observer.observe(el));

        // Add hover effects to badges
        document.querySelectorAll('.detail-badges span').forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.05)';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>

    <style>
        /* Additional styles for detail view */
        .detail-container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .detail-header {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .detail-image {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-medium);
        }

        .detail-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: var(--transition);
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .detail-image:hover .image-overlay {
            opacity: 1;
        }

        .detail-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .detail-scientific {
            font-size: 1.3rem;
            font-style: italic;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .detail-badges {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .detail-badges span {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .habitat-badge {
            background: rgba(46, 139, 87, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(46, 139, 87, 0.2);
        }

        .location-badge {
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .detail-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--primary-color);
            min-width: 16px;
        }

        .detail-content {
            padding: 2rem;
        }

        .content-section {
            margin-bottom: 2rem;
            border: 1px solid #e0e0e0;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .section-title {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem 1.5rem;
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .section-title:hover {
            background: #267d4a;
        }

        .section-content {
            padding: 1.5rem;
            line-height: 1.8;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
        }

        .info-card:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .info-icon {
            background: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .info-content h4 {
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .info-content p {
            color: #666;
            margin: 0;
        }

        .image-modal-content {
            max-width: 90vw;
            max-height: 90vh;
            padding: 0;
            background: transparent;
            box-shadow: none;
        }

        .image-modal-content img {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
            border-radius: var(--border-radius);
        }

        .image-info {
            background: var(--white);
            padding: 1rem;
            text-align: center;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .image-info h3 {
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .image-info p {
            color: var(--primary-color);
            font-style: italic;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .detail-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .detail-image {
                max-width: 300px;
                margin: 0 auto;
            }

            .detail-title {
                font-size: 2rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .quick-actions .btn {
                flex: 1;
                min-width: 120px;
            }
        }

        /* Print styles */
        @media print {
            .admin-sidebar,
            .admin-header,
            .quick-actions {
                display: none !important;
            }

            .admin-main {
                margin-left: 0 !important;
            }

            .detail-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .section-title {
                background: #f8f9fa !important;
                color: var(--dark-color) !important;
            }
        }
    </style>
</body>
</html>