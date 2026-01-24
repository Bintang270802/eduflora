<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/database.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM flora WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Data flora berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus data flora!";
    }
    mysqli_stmt_close($stmt);
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE nama LIKE '%$search%' OR nama_ilmiah LIKE '%$search%'" : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM flora $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get flora data
$query = "SELECT * FROM flora $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Flora - EduFlora Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-fix.css">
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
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="header-left">
                    <h1 class="admin-title">
                        <i class="fas fa-seedling"></i>
                        Kelola Flora
                    </h1>
                    <div class="breadcrumb">
                        <span>Kelola Flora</span>
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
                <!-- Alert Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Search and Actions -->
                <div class="content-header">
                    <div class="search-section">
                        <form method="GET" class="search-form">
                            <div class="search-input-group">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" placeholder="Cari flora..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="action-buttons">
                        <a href="flora_add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Flora
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($total_records); ?></div>
                            <div class="stat-label">Total Flora</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e67e22;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">
                                <?php 
                                $endangered = mysqli_query($conn, "SELECT COUNT(*) as count FROM flora WHERE status_konservasi IN ('Terancam', 'Langka', 'Kritis')");
                                echo mysqli_fetch_assoc($endangered)['count'];
                                ?>
                            </div>
                            <div class="stat-label">Terancam Punah</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #27ae60;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">
                                <?php 
                                $safe = mysqli_query($conn, "SELECT COUNT(*) as count FROM flora WHERE status_konservasi = 'Aman'");
                                echo mysqli_fetch_assoc($safe)['count'];
                                ?>
                            </div>
                            <div class="stat-label">Status Aman</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #9b59b6;">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">
                                <?php 
                                $recent = mysqli_query($conn, "SELECT COUNT(*) as count FROM flora WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
                                echo mysqli_fetch_assoc($recent)['count'];
                                ?>
                            </div>
                            <div class="stat-label">Ditambah Bulan Ini</div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="data-table">
                    <div class="table-header">
                        <h2 class="table-title">
                            <i class="fas fa-list"></i>
                            Daftar Flora
                        </h2>
                        <div class="table-info">
                            Menampilkan <?php echo min($limit, $total_records); ?> dari <?php echo $total_records; ?> data
                        </div>
                    </div>
                    
                    <div class="table-content">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Gambar</th>
                                        <th>Nama</th>
                                        <th>Nama Ilmiah</th>
                                        <th>Habitat</th>
                                        <th>Asal Daerah</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($flora = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $flora['id']; ?></td>
                                            <td>
                                                <div class="table-image">
                                                    <img src="../<?php echo $flora['image']; ?>" 
                                                         alt="<?php echo $flora['nama']; ?>"
                                                         onerror="this.src='../assets/images/default-flora.svg'">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-name">
                                                    <strong><?php echo htmlspecialchars($flora['nama']); ?></strong>
                                                </div>
                                            </td>
                                            <td>
                                                <em><?php echo htmlspecialchars($flora['nama_ilmiah']); ?></em>
                                            </td>
                                            <td>
                                                <span class="habitat-tag">
                                                    <?php echo $flora['habitat']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $flora['asal_daerah']; ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $flora['status_konservasi'])); ?>">
                                                    <?php echo $flora['status_konservasi']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($flora['created_at'])); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="flora_view.php?id=<?php echo $flora['id']; ?>" 
                                                       class="btn-view" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="flora_edit.php?id=<?php echo $flora['id']; ?>" 
                                                       class="btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?php echo $flora['id']; ?>, '<?php echo addslashes($flora['nama']); ?>')" 
                                                            class="btn-delete" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-data">
                                <i class="fas fa-seedling"></i>
                                <h3>Tidak ada data flora</h3>
                                <p>Belum ada data flora yang tersedia. Mulai dengan menambahkan data flora baru.</p>
                                <a href="flora_add.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Tambah Flora Pertama
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="table-pagination">
                            <div class="pagination-info">
                                Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                            </div>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-btn">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($total_pages, $page + 2);
                                
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-number <?php echo $i === $page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-btn">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
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
                <p>Apakah Anda yakin ingin menghapus flora <strong id="deleteItemName"></strong>?</p>
                <p class="warning-text">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <a id="confirmDeleteBtn" href="#" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !toggle.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Delete confirmation
        function confirmDelete(id, name) {
            document.getElementById('deleteItemName').textContent = name;
            document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            // Close sidebar on desktop
            if (window.innerWidth > 768) {
                document.getElementById('adminSidebar').classList.remove('active');
            }
        });
    </script>
</body>
</html>