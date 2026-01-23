<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/database.php';

$success_message = '';
$error_message = '';

// Ambil ID flora dari URL
$flora_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($flora_id <= 0) {
    header('Location: flora.php');
    exit();
}

// Ambil data flora
$query = "SELECT * FROM flora WHERE id = $flora_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: flora.php');
    exit();
}

$flora = mysqli_fetch_assoc($result);

// Handle form submission
if ($_POST) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nama_ilmiah = mysqli_real_escape_string($conn, $_POST['nama_ilmiah']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $habitat = mysqli_real_escape_string($conn, $_POST['habitat']);
    $habitat_detail = mysqli_real_escape_string($conn, $_POST['habitat_detail']);
    $asal_daerah = mysqli_real_escape_string($conn, $_POST['asal_daerah']);
    $status_konservasi = mysqli_real_escape_string($conn, $_POST['status_konservasi']);
    $manfaat = mysqli_real_escape_string($conn, $_POST['manfaat']);
    $ciri_khusus = mysqli_real_escape_string($conn, $_POST['ciri_khusus']);
    
    // Handle image upload
    $image_name = $flora['image']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $new_image_name = 'flora_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = '../assets/images/' . $new_image_name;
                
                // Create directory if it doesn't exist
                if (!is_dir('../assets/images/')) {
                    mkdir('../assets/images/', 0755, true);
                }
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if it exists and is not default
                    if ($flora['image'] && 
                        $flora['image'] != 'assets/images/default-flora.svg' && 
                        strpos($flora['image'], 'assets/images/') === 0 && 
                        file_exists('../' . $flora['image'])) {
                        unlink('../' . $flora['image']);
                    }
                    
                    // Update image name with relative path
                    $image_name = 'assets/images/' . $new_image_name;
                } else {
                    $error_message = "Gagal mengupload gambar! Periksa permission direktori. Upload path: " . $upload_path . " | Temp file: " . $_FILES['image']['tmp_name'] . " | Error: " . $_FILES['image']['error'];
                }
            } else {
                $error_message = "Ukuran gambar terlalu besar! Maksimal 5MB.";
            }
        } else {
            $error_message = "Format gambar tidak didukung! Gunakan JPG, PNG, GIF, atau WebP.";
        }
    }
    
    // Update data if no error
    if (empty($error_message)) {
        $update_query = "UPDATE flora SET 
            nama = '$nama',
            nama_ilmiah = '$nama_ilmiah',
            deskripsi = '$deskripsi',
            habitat = '$habitat',
            habitat_detail = '$habitat_detail',
            asal_daerah = '$asal_daerah',
            status_konservasi = '$status_konservasi',
            manfaat = '$manfaat',
            ciri_khusus = '$ciri_khusus',
            image = '$image_name',
            updated_at = NOW()
            WHERE id = $flora_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Data flora berhasil diperbarui!";
            // Refresh data
            $result = mysqli_query($conn, "SELECT * FROM flora WHERE id = $flora_id");
            $flora = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Gagal memperbarui data flora!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flora - EduFlora Admin</title>
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
                        <i class="fas fa-edit"></i>
                        Edit Flora: <?php echo htmlspecialchars($flora['nama']); ?>
                    </h1>
                    <div class="breadcrumb">
                        <a href="flora.php">Kelola Flora</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Edit Flora</span>
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
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success_message; ?>
                        <button class="alert-close">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $error_message; ?>
                        <button class="alert-close">&times;</button>
                    </div>
                <?php endif; ?>

                <!-- Form Container -->
                <div class="form-container">
                    <div class="form-header">
                        <h2>
                            <i class="fas fa-edit"></i>
                            Edit Data Flora
                        </h2>
                        <p>Perbarui informasi flora yang sudah ada dalam database</p>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="flora-form">
                        <!-- Informasi Dasar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Dasar
                            </h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nama">
                                        <i class="fas fa-seedling"></i>
                                        Nama Flora <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($flora['nama']); ?>" required>
                                    <div class="form-help">Nama umum flora dalam bahasa Indonesia</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_ilmiah">
                                        <i class="fas fa-microscope"></i>
                                        Nama Ilmiah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama_ilmiah" name="nama_ilmiah" value="<?php echo htmlspecialchars($flora['nama_ilmiah']); ?>" required>
                                    <div class="form-help">Nama ilmiah dalam bahasa Latin</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="deskripsi">
                                        <i class="fas fa-align-left"></i>
                                        Deskripsi <span class="required">*</span>
                                    </label>
                                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($flora['deskripsi']); ?></textarea>
                                    <div class="form-help">Deskripsi lengkap tentang flora (minimal 100 karakter)</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Habitat dan Lokasi -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Habitat dan Lokasi
                            </h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="habitat">
                                        <i class="fas fa-tree"></i>
                                        Habitat <span class="required">*</span>
                                    </label>
                                    <select id="habitat" name="habitat" required>
                                        <option value="">Pilih Habitat</option>
                                        <option value="Hutan Hujan Tropis" <?php echo $flora['habitat'] == 'Hutan Hujan Tropis' ? 'selected' : ''; ?>>Hutan Hujan Tropis</option>
                                        <option value="Hutan Mangrove" <?php echo $flora['habitat'] == 'Hutan Mangrove' ? 'selected' : ''; ?>>Hutan Mangrove</option>
                                        <option value="Savana" <?php echo $flora['habitat'] == 'Savana' ? 'selected' : ''; ?>>Savana</option>
                                        <option value="Pegunungan" <?php echo $flora['habitat'] == 'Pegunungan' ? 'selected' : ''; ?>>Pegunungan</option>
                                        <option value="Pantai" <?php echo $flora['habitat'] == 'Pantai' ? 'selected' : ''; ?>>Pantai</option>
                                        <option value="Rawa" <?php echo $flora['habitat'] == 'Rawa' ? 'selected' : ''; ?>>Rawa</option>
                                        <option value="Dataran Tinggi" <?php echo $flora['habitat'] == 'Dataran Tinggi' ? 'selected' : ''; ?>>Dataran Tinggi</option>
                                        <option value="Lainnya" <?php echo $flora['habitat'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                    <div class="form-help">Jenis habitat tempat flora hidup</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="asal_daerah">
                                        <i class="fas fa-globe-asia"></i>
                                        Asal Daerah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="asal_daerah" name="asal_daerah" value="<?php echo htmlspecialchars($flora['asal_daerah']); ?>" required>
                                    <div class="form-help">Daerah asal flora di Indonesia</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="habitat_detail">
                                        <i class="fas fa-map"></i>
                                        Detail Habitat
                                    </label>
                                    <textarea id="habitat_detail" name="habitat_detail" rows="3"><?php echo htmlspecialchars($flora['habitat_detail']); ?></textarea>
                                    <div class="form-help">Penjelasan detail tentang kondisi habitat</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status dan Karakteristik -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-shield-alt"></i>
                                Status dan Karakteristik
                            </h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="status_konservasi">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Status Konservasi <span class="required">*</span>
                                    </label>
                                    <select id="status_konservasi" name="status_konservasi" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Aman" <?php echo $flora['status_konservasi'] == 'Aman' ? 'selected' : ''; ?>>Aman (Least Concern)</option>
                                        <option value="Terancam" <?php echo $flora['status_konservasi'] == 'Terancam' ? 'selected' : ''; ?>>Terancam (Vulnerable)</option>
                                        <option value="Langka" <?php echo $flora['status_konservasi'] == 'Langka' ? 'selected' : ''; ?>>Langka (Endangered)</option>
                                        <option value="Kritis" <?php echo $flora['status_konservasi'] == 'Kritis' ? 'selected' : ''; ?>>Kritis (Critically Endangered)</option>
                                        <option value="Punah di Alam" <?php echo $flora['status_konservasi'] == 'Punah di Alam' ? 'selected' : ''; ?>>Punah di Alam (Extinct in Wild)</option>
                                    </select>
                                    <div class="form-help">Status konservasi berdasarkan IUCN</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="ciri_khusus">
                                        <i class="fas fa-eye"></i>
                                        Ciri Khusus
                                    </label>
                                    <textarea id="ciri_khusus" name="ciri_khusus" rows="3"><?php echo htmlspecialchars($flora['ciri_khusus']); ?></textarea>
                                    <div class="form-help">Ciri-ciri khusus yang membedakan flora ini</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="manfaat">
                                        <i class="fas fa-heart"></i>
                                        Manfaat
                                    </label>
                                    <textarea id="manfaat" name="manfaat" rows="3"><?php echo htmlspecialchars($flora['manfaat']); ?></textarea>
                                    <div class="form-help">Manfaat flora bagi manusia dan lingkungan</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload Gambar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-image"></i>
                                Gambar Flora
                            </h3>
                            <div class="form-grid">
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <?php if ($flora['image']): ?>
                                        <div class="current-image">
                                            <h4>Gambar Saat Ini:</h4>
                                            <img src="<?php echo $flora['image']; ?>" alt="<?php echo htmlspecialchars($flora['nama']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <label for="image">
                                        <i class="fas fa-upload"></i>
                                        Upload Gambar Baru
                                    </label>
                                    <div class="file-upload-area">
                                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                        <div class="file-upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Klik untuk memilih gambar baru atau drag & drop</p>
                                            <small>Format: JPG, PNG, GIF, WebP (Max: 5MB)</small>
                                        </div>
                                        <div id="imagePreview" class="image-preview" style="display: none;">
                                            <img id="previewImg" src="" alt="Preview">
                                            <button type="button" onclick="removeImage()" class="remove-image">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-help">Kosongkan jika tidak ingin mengubah gambar</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="flora.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="button" onclick="confirmDelete()" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Hapus Flora
                            </button>
                            <button type="button" onclick="resetForm()" class="btn btn-outline">
                                <i class="fas fa-undo"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Flora
                            </button>
                        </div>
                    </form>
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
                <p>Apakah Anda yakin ingin menghapus flora <strong><?php echo htmlspecialchars($flora['nama']); ?></strong>?</p>
                <p class="warning-text">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <a href="flora.php?delete=<?php echo $flora['id']; ?>" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize form functionality after DOM is loaded
            initializeForm();
        });

        function initializeForm() {
            // Character counter for textarea
            const deskripsiField = document.getElementById('deskripsi');
            if (deskripsiField) {
                deskripsiField.addEventListener('input', function() {
                    const length = this.value.length;
                    const help = this.nextElementSibling;
                    if (help) {
                        help.textContent = `Deskripsi lengkap tentang flora (${length}/100 karakter minimum)`;
                        
                        if (length >= 100) {
                            help.style.color = '#27ae60';
                        } else {
                            help.style.color = '#e74c3c';
                        }
                    }
                });
            }

            // Check for unsaved changes
            let formChanged = false;
            document.querySelectorAll('input, textarea, select').forEach(element => {
                element.addEventListener('change', () => formChanged = true);
            });

            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Mark form as saved when submitted
            const form = document.querySelector('.flora-form');
            if (form) {
                form.addEventListener('submit', () => formChanged = false);
            }
        }
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            // Check if elements exist
            if (!preview || !previewImg) {
                console.error('Preview elements not found');
                return;
            }
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    const uploadContent = input.parentElement.querySelector('.file-upload-content');
                    if (uploadContent) {
                        uploadContent.style.display = 'none';
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('image');
            const preview = document.getElementById('imagePreview');
            const uploadContent = document.querySelector('.file-upload-content');
            
            if (input) input.value = '';
            if (preview) preview.style.display = 'none';
            if (uploadContent) uploadContent.style.display = 'block';
        }

        // Delete confirmation
        function confirmDelete() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Form reset functionality
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form? Semua perubahan akan hilang.')) {
                // Store original form data
                const originalData = {
                    nama: '<?php echo addslashes($flora['nama']); ?>',
                    nama_ilmiah: '<?php echo addslashes($flora['nama_ilmiah']); ?>',
                    deskripsi: '<?php echo addslashes($flora['deskripsi']); ?>',
                    habitat: '<?php echo addslashes($flora['habitat']); ?>',
                    habitat_detail: '<?php echo addslashes($flora['habitat_detail']); ?>',
                    asal_daerah: '<?php echo addslashes($flora['asal_daerah']); ?>',
                    status_konservasi: '<?php echo addslashes($flora['status_konservasi']); ?>',
                    manfaat: '<?php echo addslashes($flora['manfaat']); ?>',
                    ciri_khusus: '<?php echo addslashes($flora['ciri_khusus']); ?>'
                };
                
                // Reset form to original values
                Object.keys(originalData).forEach(key => {
                    const element = document.getElementById(key);
                    if (element) {
                        element.value = originalData[key];
                    }
                });
                
                // Reset image upload
                removeImage();
                formChanged = false;
            }
        }

        // Form validation
        document.querySelector('.flora-form').addEventListener('submit', function(e) {
            const deskripsi = document.getElementById('deskripsi').value;
            
            if (deskripsi.length < 100) {
                e.preventDefault();
                alert('Deskripsi harus minimal 100 karakter!');
                document.getElementById('deskripsi').focus();
                return false;
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Character counter for textarea
        document.getElementById('deskripsi').addEventListener('input', function() {
            const length = this.value.length;
            const help = this.nextElementSibling;
            help.textContent = `Deskripsi lengkap tentang flora (${length}/100 karakter minimum)`;
            
            if (length >= 100) {
                help.style.color = '#27ae60';
            } else {
                help.style.color = '#e74c3c';
            }
        });

        // Check for unsaved changes
        let formChanged = false;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', () => formChanged = true);
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Mark form as saved when submitted
        document.querySelector('.flora-form').addEventListener('submit', () => formChanged = false);

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

        // Alert close functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('alert-close')) {
                const alert = e.target.closest('.alert');
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }
        });

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
</body>
</html>