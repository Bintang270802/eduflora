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
    $delete_query = "DELETE FROM fauna WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Data fauna berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus data fauna!";
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
$count_query = "SELECT COUNT(*) as total FROM fauna $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $limit);

// Get fauna data
$query = "SELECT * FROM fauna $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fauna - EduFlora Admin</title>
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
                <h1 class="admin-title">
                    <i class="fas fa-paw"></i>
                    Kelola Fauna
                </h1>
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
                                <input type="text" name="search" placeholder="Cari fauna..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="action-buttons">
                        <a href="fauna_add.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Tambah Fauna
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e67e22;">
                            <i class="fas fa-paw"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo number_format($total_records); ?></div>
                            <div class="stat-label">Total Fauna</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e74c3c;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">
                                <?php 
                                $endangered = mysqli_query($conn, "SELECT COUNT(*) as count FROM fauna WHERE status_konservasi IN ('Terancam', 'Langka', 'Kritis')");
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
                                $safe = mysqli_query($conn, "SELECT COUNT(*) as count FROM fauna WHERE status_konservasi = 'Aman'");
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
                                $recent = mysqli_query($conn, "SELECT COUNT(*) as count FROM fauna WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
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
                            Daftar Fauna
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
                                        <th>Makanan</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($fauna = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $fauna['id']; ?></td>
                                            <td>
                                                <div class="table-image">
                                                    <img src="../<?php echo $fauna['image']; ?>" 
                                                         alt="<?php echo $fauna['nama']; ?>"
                                                         onerror="this.src='../assets/images/default-fauna.svg'">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-name">
                                                    <strong><?php echo htmlspecialchars($fauna['nama']); ?></strong>
                                                </div>
                                            </td>
                                            <td>
                                                <em><?php echo htmlspecialchars($fauna['nama_ilmiah']); ?></em>
                                            </td>
                                            <td>
                                                <span class="habitat-tag" style="background: rgba(230, 126, 34, 0.1); color: #e67e22;">
                                                    <?php echo $fauna['habitat']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $fauna['asal_daerah']; ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $fauna['status_konservasi'])); ?>">
                                                    <?php echo $fauna['status_konservasi']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="food-tag">
                                                    <?php echo $fauna['makanan'] ? substr($fauna['makanan'], 0, 20) . '...' : '-'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($fauna['created_at'])); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="fauna_view.php?id=<?php echo $fauna['id']; ?>" 
                                                       class="btn-view" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="fauna_edit.php?id=<?php echo $fauna['id']; ?>" 
                                                       class="btn-edit" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?php echo $fauna['id']; ?>, '<?php echo addslashes($fauna['nama']); ?>')" 
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
                                <i class="fas fa-paw"></i>
                                <h3>Tidak ada data fauna</h3>
                                <p>Belum ada data fauna yang tersedia. Mulai dengan menambahkan data fauna baru.</p>
                                <a href="fauna_add.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Tambah Fauna Pertama
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
                <p>Apakah Anda yakin ingin menghapus fauna <strong id="deleteItemName"></strong>?</p>
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

    <script>
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

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Add loading state to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.classList.contains('btn-delete')) {
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';
                }
            });
        });

        // Table row hover effect
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(230, 126, 34, 0.05)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });

        // Add animation to stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.style.animation = 'fadeInUp 0.6s ease-out forwards';
        });
    </script>

    <style>
        .food-tag {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>
</html>