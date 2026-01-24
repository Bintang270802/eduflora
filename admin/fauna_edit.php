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

// Handle form submission
if ($_POST) {
    $nama = $_POST['nama'];
    $nama_ilmiah = $_POST['nama_ilmiah'];
    $deskripsi = $_POST['deskripsi'];
    $habitat = $_POST['habitat'];
    $habitat_detail = $_POST['habitat_detail'];
    $asal_daerah = $_POST['asal_daerah'];
    $status_konservasi = $_POST['status_konservasi'];
    $makanan = $_POST['makanan'];
    $perilaku = $_POST['perilaku'];
    $ciri_fisik = $_POST['ciri_fisik'];
    
    // Handle image upload
    $image_name = $fauna['image']; // Keep existing image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Get file info
        $original_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        
        // Get file extension
        $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Get MIME type for additional validation
        $allowed_mime_types = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        $file_mime_type = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_mime_type = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);
        } else {
            $file_mime_type = $_FILES['image']['type'];
        }
        
        // Validate extension
        if (!in_array($file_extension, $allowed_extensions)) {
            $error_message = "Format gambar tidak didukung! File: '$original_name' dengan ekstensi '$file_extension'. Gunakan JPG, JPEG, PNG, GIF, atau WebP.";
        }
        // Validate MIME type
        elseif (!in_array($file_mime_type, $allowed_mime_types)) {
            $error_message = "Tipe file tidak valid! MIME type: '$file_mime_type'. Pastikan file adalah gambar yang valid.";
        }
        // Validate file size
        elseif ($file_size > 5 * 1024 * 1024) {
            $error_message = "Ukuran gambar terlalu besar! Ukuran: " . number_format($file_size / 1024 / 1024, 2) . "MB. Maksimal 5MB.";
        }
        // All validations passed, try upload
        else {
            $new_image_name = 'fauna_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = '../assets/images/' . $new_image_name;
            
            // Create directory if it doesn't exist
            if (!is_dir('../assets/images/')) {
                mkdir('../assets/images/', 0755, true);
            }
            
            if (move_uploaded_file($tmp_name, $upload_path)) {
                // Delete old image if it exists and is not default
                if ($fauna['image'] && 
                    $fauna['image'] != 'assets/images/default-fauna.svg' && 
                    strpos($fauna['image'], 'assets/images/') === 0 && 
                    file_exists('../' . $fauna['image'])) {
                    unlink('../' . $fauna['image']);
                }
                
                // Update image name with relative path
                $image_name = 'assets/images/' . $new_image_name;
            } else {
                $error_message = "Gagal mengupload gambar! Periksa permission direktori. Upload path: " . $upload_path . " | Temp file: " . $tmp_name . " | Directory writable: " . (is_writable('../assets/images/') ? 'Yes' : 'No');
            }
        }
    }
    
    // Update data if no error
    if (empty($error_message)) {
        $update_query = "UPDATE fauna SET 
            nama = ?,
            nama_ilmiah = ?,
            deskripsi = ?,
            habitat = ?,
            habitat_detail = ?,
            asal_daerah = ?,
            status_konservasi = ?,
            makanan = ?,
            perilaku = ?,
            ciri_fisik = ?,
            image = ?,
            updated_at = NOW()
            WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'sssssssssssi', 
            $nama, $nama_ilmiah, $deskripsi, $habitat, $habitat_detail, 
            $asal_daerah, $status_konservasi, $makanan, $perilaku, 
            $ciri_fisik, $image_name, $fauna_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Data fauna berhasil diperbarui!";
            // Refresh data
            $result = mysqli_query($conn, "SELECT * FROM fauna WHERE id = $fauna_id");
            $fauna = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Gagal memperbarui data fauna: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fauna - EduFlora Admin</title>
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
                        <i class="fas fa-edit"></i>
                        Edit Fauna: <?php echo htmlspecialchars($fauna['nama']); ?>
                    </h1>
                    <div class="breadcrumb">
                        <a href="fauna.php">Kelola Fauna</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Edit Fauna</span>
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
                            Edit Data Fauna
                        </h2>
                        <p>Perbarui informasi fauna yang sudah ada dalam database</p>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="fauna-form">
                        <!-- Informasi Dasar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Dasar
                            </h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nama">
                                        <i class="fas fa-paw"></i>
                                        Nama Fauna <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($fauna['nama']); ?>" required>
                                    <div class="form-help">Nama umum fauna dalam bahasa Indonesia</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nama_ilmiah">
                                        <i class="fas fa-microscope"></i>
                                        Nama Ilmiah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama_ilmiah" name="nama_ilmiah" value="<?php echo htmlspecialchars($fauna['nama_ilmiah']); ?>" required>
                                    <div class="form-help">Nama ilmiah dalam bahasa Latin</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="deskripsi">
                                        <i class="fas fa-align-left"></i>
                                        Deskripsi <span class="required">*</span>
                                    </label>
                                    <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($fauna['deskripsi']); ?></textarea>
                                    <div class="form-help">Deskripsi lengkap tentang fauna (minimal 100 karakter)</div>
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
                                        <option value="Hutan Hujan Tropis" <?php echo $fauna['habitat'] == 'Hutan Hujan Tropis' ? 'selected' : ''; ?>>Hutan Hujan Tropis</option>
                                        <option value="Hutan Mangrove" <?php echo $fauna['habitat'] == 'Hutan Mangrove' ? 'selected' : ''; ?>>Hutan Mangrove</option>
                                        <option value="Savana" <?php echo $fauna['habitat'] == 'Savana' ? 'selected' : ''; ?>>Savana</option>
                                        <option value="Pegunungan" <?php echo $fauna['habitat'] == 'Pegunungan' ? 'selected' : ''; ?>>Pegunungan</option>
                                        <option value="Pantai" <?php echo $fauna['habitat'] == 'Pantai' ? 'selected' : ''; ?>>Pantai</option>
                                        <option value="Rawa" <?php echo $fauna['habitat'] == 'Rawa' ? 'selected' : ''; ?>>Rawa</option>
                                        <option value="Laut" <?php echo $fauna['habitat'] == 'Laut' ? 'selected' : ''; ?>>Laut</option>
                                        <option value="Sungai" <?php echo $fauna['habitat'] == 'Sungai' ? 'selected' : ''; ?>>Sungai</option>
                                        <option value="Lainnya" <?php echo $fauna['habitat'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                    <div class="form-help">Jenis habitat tempat fauna hidup</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="asal_daerah">
                                        <i class="fas fa-globe-asia"></i>
                                        Asal Daerah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="asal_daerah" name="asal_daerah" value="<?php echo htmlspecialchars($fauna['asal_daerah']); ?>" required>
                                    <div class="form-help">Daerah asal fauna di Indonesia</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="habitat_detail">
                                        <i class="fas fa-map"></i>
                                        Detail Habitat
                                    </label>
                                    <textarea id="habitat_detail" name="habitat_detail" rows="3"><?php echo htmlspecialchars($fauna['habitat_detail']); ?></textarea>
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
                                        <option value="Aman" <?php echo $fauna['status_konservasi'] == 'Aman' ? 'selected' : ''; ?>>Aman (Least Concern)</option>
                                        <option value="Terancam" <?php echo $fauna['status_konservasi'] == 'Terancam' ? 'selected' : ''; ?>>Terancam (Vulnerable)</option>
                                        <option value="Langka" <?php echo $fauna['status_konservasi'] == 'Langka' ? 'selected' : ''; ?>>Langka (Endangered)</option>
                                        <option value="Kritis" <?php echo $fauna['status_konservasi'] == 'Kritis' ? 'selected' : ''; ?>>Kritis (Critically Endangered)</option>
                                        <option value="Punah di Alam" <?php echo $fauna['status_konservasi'] == 'Punah di Alam' ? 'selected' : ''; ?>>Punah di Alam (Extinct in Wild)</option>
                                    </select>
                                    <div class="form-help">Status konservasi berdasarkan IUCN</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="makanan">
                                        <i class="fas fa-utensils"></i>
                                        Makanan
                                    </label>
                                    <input type="text" id="makanan" name="makanan" value="<?php echo htmlspecialchars($fauna['makanan']); ?>">
                                    <div class="form-help">Jenis makanan utama fauna</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="ciri_fisik">
                                        <i class="fas fa-eye"></i>
                                        Ciri Fisik
                                    </label>
                                    <textarea id="ciri_fisik" name="ciri_fisik" rows="3"><?php echo htmlspecialchars($fauna['ciri_fisik']); ?></textarea>
                                    <div class="form-help">Ciri-ciri fisik yang membedakan fauna ini</div>
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="perilaku">
                                        <i class="fas fa-running"></i>
                                        Perilaku
                                    </label>
                                    <textarea id="perilaku" name="perilaku" rows="3"><?php echo htmlspecialchars($fauna['perilaku']); ?></textarea>
                                    <div class="form-help">Perilaku dan kebiasaan fauna</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Upload Gambar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-image"></i>
                                Gambar Fauna
                            </h3>
                            <div class="form-grid">
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <?php if ($fauna['image']): ?>
                                        <div class="current-image">
                                            <h4>Gambar Saat Ini:</h4>
                                            <img src="../<?php echo $fauna['image']; ?>" alt="<?php echo htmlspecialchars($fauna['nama']); ?>" style="max-width: 200px; height: auto; border-radius: 8px; border: 1px solid #ddd;">
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
                            <a href="fauna.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="button" onclick="confirmDelete()" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Hapus Fauna
                            </button>
                            <button type="button" onclick="resetForm()" class="btn btn-outline">
                                <i class="fas fa-undo"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Fauna
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
                        help.textContent = `Deskripsi lengkap tentang fauna (${length}/100 karakter minimum)`;
                        
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
            const form = document.querySelector('.fauna-form');
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
                    nama: '<?php echo addslashes($fauna['nama']); ?>',
                    nama_ilmiah: '<?php echo addslashes($fauna['nama_ilmiah']); ?>',
                    deskripsi: '<?php echo addslashes($fauna['deskripsi']); ?>',
                    habitat: '<?php echo addslashes($fauna['habitat']); ?>',
                    habitat_detail: '<?php echo addslashes($fauna['habitat_detail']); ?>',
                    asal_daerah: '<?php echo addslashes($fauna['asal_daerah']); ?>',
                    status_konservasi: '<?php echo addslashes($fauna['status_konservasi']); ?>',
                    makanan: '<?php echo addslashes($fauna['makanan']); ?>',
                    perilaku: '<?php echo addslashes($fauna['perilaku']); ?>',
                    ciri_fisik: '<?php echo addslashes($fauna['ciri_fisik']); ?>'
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
        document.querySelector('.fauna-form').addEventListener('submit', function(e) {
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
            help.textContent = `Deskripsi lengkap tentang fauna (${length}/100 karakter minimum)`;
            
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
        document.querySelector('.fauna-form').addEventListener('submit', () => formChanged = false);

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