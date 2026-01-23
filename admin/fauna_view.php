<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/database.php';

// Ambil ID fauna dari URL
$fauna_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($fauna_id <= 0) {
    header('Location: fauna.php');
    exit();
}

// Ambil data fauna
$query = "SELECT * FROM fauna WHERE id = $fauna_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: fauna.php');
    exit();
}

$fauna = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Fauna - EduFlora Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-leaf"></i>
                    <span>EduFlora Admin</span>
                </div>
            </div>
            <nav>
                <ul class="sidebar-nav">
                    <li><a href="flora.php"><i class="fas fa-seedling"></i> Kelola Flora</a></li>
                    <li><a href="fauna.php" class="active"><i class="fas fa-paw"></i> Kelola Fauna</a></li>
                    <li><a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Website</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-left">
                    <h1 class="admin-title">
                        <i class="fas fa-eye"></i>
                        Detail Fauna: <?php echo htmlspecialchars($fauna['nama']); ?>
                    </h1>
                    <div class="breadcrumb">
                        <a href="fauna.php">Kelola Fauna</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Detail Fauna</span>
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
                <!-- Detail Container -->
                <div class="detail-container">
                    <div class="detail-header">
                        <div class="detail-image">
                            <img src="../<?php echo $fauna['image']; ?>" alt="<?php echo $fauna['nama']; ?>" 
                                 onerror="this.src='../assets/images/default-fauna.svg'">
                            <div class="image-overlay">
                                <button class="image-zoom" onclick="openImageModal()">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="detail-info">
                            <div class="detail-title">
                                <h2><?php echo htmlspecialchars($fauna['nama']); ?></h2>
                                <div class="detail-subtitle">
                                    <em><?php echo htmlspecialchars($fauna['nama_ilmiah']); ?></em>
                                </div>
                            </div>
                            <div class="detail-badges">
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $fauna['status_konservasi'])); ?>">
                                    <i class="fas fa-shield-alt"></i>
                                    <?php echo $fauna['status_konservasi']; ?>
                                </span>
                                <span class="habitat-badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $fauna['habitat']; ?>
                                </span>
                                <span class="location-badge">
                                    <i class="fas fa-globe-asia"></i>
                                    <?php echo $fauna['asal_daerah']; ?>
                                </span>
                            </div>
                            <div class="detail-actions">
                                <a href="fauna_edit.php?id=<?php echo $fauna['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                    Edit Fauna
                                </a>
                                <button onclick="confirmDelete()" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Hapus Fauna
                                </button>
                                <a href="fauna.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Content -->
                    <div class="detail-content">
                        <!-- Informasi Dasar -->
                        <div class="detail-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Dasar
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-paw"></i>
                                        Nama Fauna
                                    </div>
                                    <div class="info-value"><?php echo htmlspecialchars($fauna['nama']); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-microscope"></i>
                                        Nama Ilmiah
                                    </div>
                                    <div class="info-value"><em><?php echo htmlspecialchars($fauna['nama_ilmiah']); ?></em></div>
                                </div>
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <i class="fas fa-align-left"></i>
                                        Deskripsi
                                    </div>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($fauna['deskripsi'])); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Habitat dan Lokasi -->
                        <div class="detail-section">
                            <h3 class="section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Habitat dan Lokasi
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-tree"></i>
                                        Habitat
                                    </div>
                                    <div class="info-value">
                                        <span class="habitat-tag"><?php echo htmlspecialchars($fauna['habitat']); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-globe-asia"></i>
                                        Asal Daerah
                                    </div>
                                    <div class="info-value"><?php echo htmlspecialchars($fauna['asal_daerah']); ?></div>
                                </div>
                                <?php if ($fauna['habitat_detail']): ?>
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <i class="fas fa-map"></i>
                                        Detail Habitat
                                    </div>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($fauna['habitat_detail'])); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Karakteristik Fauna -->
                        <div class="detail-section">
                            <h3 class="section-title">
                                <i class="fas fa-eye"></i>
                                Karakteristik Fauna
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-shield-alt"></i>
                                        Status Konservasi
                                    </div>
                                    <div class="info-value">
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $fauna['status_konservasi'])); ?>">
                                            <?php echo $fauna['status_konservasi']; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if ($fauna['makanan']): ?>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-utensils"></i>
                                        Makanan
                                    </div>
                                    <div class="info-value"><?php echo htmlspecialchars($fauna['makanan']); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($fauna['ciri_fisik']): ?>
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <i class="fas fa-search"></i>
                                        Ciri Fisik
                                    </div>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($fauna['ciri_fisik'])); ?></div>
                                </div>
                                <?php endif; ?>
                                <?php if ($fauna['perilaku']): ?>
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <i class="fas fa-running"></i>
                                        Perilaku
                                    </div>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($fauna['perilaku'])); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Informasi Sistem -->
                        <div class="detail-section">
                            <h3 class="section-title">
                                <i class="fas fa-database"></i>
                                Informasi Sistem
                            </h3>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-hashtag"></i>
                                        ID Fauna
                                    </div>
                                    <div class="info-value"><?php echo $fauna['id']; ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-plus"></i>
                                        Tanggal Dibuat
                                    </div>
                                    <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($fauna['created_at'])); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar-edit"></i>
                                        Terakhir Diupdate
                                    </div>
                                    <div class="info-value"><?php echo date('d/m/Y H:i', strtotime($fauna['updated_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus fauna <strong><?php echo htmlspecialchars($fauna['nama']); ?></strong>?</p>
                <p class="warning-text">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <a href="fauna.php?delete=<?php echo $fauna['id']; ?>" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content image-modal">
            <span class="close">&times;</span>
            <img id="fullImage" src="" alt="<?php echo htmlspecialchars($fauna['nama']); ?>">
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Delete confirmation
        function confirmDelete() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Image modal
        function openImageModal() {
            const modal = document.getElementById('imageModal');
            const fullImage = document.getElementById('fullImage');
            
            fullImage.src = '../<?php echo $fauna['image']; ?>';
            modal.style.display = 'block';
        }

        // Close modal functionality
        document.querySelectorAll('.close').forEach(closeBtn => {
            closeBtn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('active');
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.getElementById('adminSidebar').classList.remove('active');
            }
        });
    </script>

    <style>
        /* Detail page specific styles */
        .detail-container {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .detail-header {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            padding: 2rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-image {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .detail-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .detail-image:hover .image-overlay {
            opacity: 1;
        }

        .image-zoom {
            background: var(--white);
            color: var(--gray-700);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .image-zoom:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: scale(1.1);
        }

        .detail-info {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .detail-title h2 {
            font-size: 2rem;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .detail-subtitle {
            font-size: 1.25rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .detail-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .status-badge, .habitat-badge, .location-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .habitat-badge {
            background: rgba(46, 139, 87, 0.1);
            color: var(--primary-color);
            border: 1px solid rgba(46, 139, 87, 0.2);
        }

        .location-badge {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .detail-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .detail-content {
            padding: 0;
        }

        .detail-section {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .detail-section:last-child {
            border-bottom: none;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            background: var(--gray-50);
            padding: 1.25rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .info-value {
            color: var(--gray-900);
            line-height: 1.6;
        }

        .habitat-tag {
            background: rgba(46, 139, 87, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid rgba(46, 139, 87, 0.2);
        }

        .image-modal {
            max-width: 90vw;
            max-height: 90vh;
            padding: 0;
            background: transparent;
            box-shadow: none;
        }

        .image-modal img {
            width: 100%;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
            border-radius: var(--border-radius);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .detail-header {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .detail-image {
                max-width: 100%;
            }
            
            .detail-title h2 {
                font-size: 1.5rem;
            }
            
            .detail-actions {
                flex-direction: column;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .detail-section {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .detail-header {
                padding: 1rem;
            }
            
            .detail-section {
                padding: 1rem;
            }
            
            .detail-badges {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</body>
</html>