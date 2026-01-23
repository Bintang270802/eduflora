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

if ($_POST) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nama_ilmiah = mysqli_real_escape_string($conn, $_POST['nama_ilmiah']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $habitat = mysqli_real_escape_string($conn, $_POST['habitat']);
    $habitat_detail = mysqli_real_escape_string($conn, $_POST['habitat_detail']);
    $asal_daerah = mysqli_real_escape_string($conn, $_POST['asal_daerah']);
    $status_konservasi = mysqli_real_escape_string($conn, $_POST['status_konservasi']);
    $makanan = mysqli_real_escape_string($conn, $_POST['makanan']);
    $perilaku = mysqli_real_escape_string($conn, $_POST['perilaku']);
    $ciri_fisik = mysqli_real_escape_string($conn, $_POST['ciri_fisik']);
    
    // Handle image upload
    $image_path = 'assets/images/default-fauna.svg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) { // 5MB limit
                $new_filename = 'fauna_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'assets/images/' . $new_filename;
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
    
    // Only proceed with database insert if no upload errors
    if (empty($error_message)) {
        $query = "INSERT INTO fauna (nama, nama_ilmiah, deskripsi, habitat, habitat_detail, asal_daerah, status_konservasi, makanan, perilaku, ciri_fisik, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssssssssss', $nama, $nama_ilmiah, $deskripsi, $habitat, $habitat_detail, $asal_daerah, $status_konservasi, $makanan, $perilaku, $ciri_fisik, $image_path);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Data fauna berhasil ditambahkan!";
        // Reset form
        $_POST = array();
    } else {
        $error_message = "Gagal menambahkan data fauna: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Fauna - EduFlora Admin</title>
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
                <div class="header-left">
                    <h1 class="admin-title">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Fauna Baru
                    </h1>
                    <div class="breadcrumb">
                        <a href="fauna.php">Kelola Fauna</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Tambah Fauna</span>
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
                            <i class="fas fa-paw"></i>
                            Informasi Fauna Baru
                        </h2>
                        <p>Lengkapi semua informasi tentang fauna yang akan ditambahkan ke database</p>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="fauna-form">
                        <!-- Basic Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informasi Dasar
                            </h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nama">
                                        <i class="fas fa-tag"></i>
                                        Nama Fauna <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama" name="nama" required 
                                           placeholder="Contoh: Orangutan Sumatera"
                                           value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                                    <small class="form-help">Nama umum fauna dalam bahasa Indonesia</small>
                                </div>

                                <div class="form-group">
                                    <label for="nama_ilmiah">
                                        <i class="fas fa-microscope"></i>
                                        Nama Ilmiah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="nama_ilmiah" name="nama_ilmiah" required 
                                           placeholder="Contoh: Pongo abelii"
                                           value="<?php echo htmlspecialchars($_POST['nama_ilmiah'] ?? ''); ?>">
                                    <small class="form-help">Nama ilmiah dalam bahasa Latin</small>
                                </div>

                                <div class="form-group">
                                    <label for="habitat">
                                        <i class="fas fa-tree"></i>
                                        Habitat <span class="required">*</span>
                                    </label>
                                    <select id="habitat" name="habitat" required>
                                        <option value="">Pilih Habitat</option>
                                        <option value="Hutan Hujan Tropis" <?php echo ($_POST['habitat'] ?? '') === 'Hutan Hujan Tropis' ? 'selected' : ''; ?>>Hutan Hujan Tropis</option>
                                        <option value="Hutan Monsun" <?php echo ($_POST['habitat'] ?? '') === 'Hutan Monsun' ? 'selected' : ''; ?>>Hutan Monsun</option>
                                        <option value="Savana Kering" <?php echo ($_POST['habitat'] ?? '') === 'Savana Kering' ? 'selected' : ''; ?>>Savana Kering</option>
                                        <option value="Pegunungan" <?php echo ($_POST['habitat'] ?? '') === 'Pegunungan' ? 'selected' : ''; ?>>Pegunungan</option>
                                        <option value="Rawa Gambut" <?php echo ($_POST['habitat'] ?? '') === 'Rawa Gambut' ? 'selected' : ''; ?>>Rawa Gambut</option>
                                        <option value="Pantai" <?php echo ($_POST['habitat'] ?? '') === 'Pantai' ? 'selected' : ''; ?>>Pantai</option>
                                        <option value="Laut" <?php echo ($_POST['habitat'] ?? '') === 'Laut' ? 'selected' : ''; ?>>Laut</option>
                                        <option value="Sungai" <?php echo ($_POST['habitat'] ?? '') === 'Sungai' ? 'selected' : ''; ?>>Sungai</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="asal_daerah">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Asal Daerah <span class="required">*</span>
                                    </label>
                                    <input type="text" id="asal_daerah" name="asal_daerah" required 
                                           placeholder="Contoh: Sumatera, Kalimantan"
                                           value="<?php echo htmlspecialchars($_POST['asal_daerah'] ?? ''); ?>">
                                    <small class="form-help">Daerah asal atau sebaran fauna</small>
                                </div>

                                <div class="form-group">
                                    <label for="status_konservasi">
                                        <i class="fas fa-shield-alt"></i>
                                        Status Konservasi <span class="required">*</span>
                                    </label>
                                    <select id="status_konservasi" name="status_konservasi" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Aman" <?php echo ($_POST['status_konservasi'] ?? '') === 'Aman' ? 'selected' : ''; ?>>Aman</option>
                                        <option value="Terancam" <?php echo ($_POST['status_konservasi'] ?? '') === 'Terancam' ? 'selected' : ''; ?>>Terancam</option>
                                        <option value="Langka" <?php echo ($_POST['status_konservasi'] ?? '') === 'Langka' ? 'selected' : ''; ?>>Langka</option>
                                        <option value="Kritis" <?php echo ($_POST['status_konservasi'] ?? '') === 'Kritis' ? 'selected' : ''; ?>>Kritis</option>
                                        <option value="Punah di Alam" <?php echo ($_POST['status_konservasi'] ?? '') === 'Punah di Alam' ? 'selected' : ''; ?>>Punah di Alam</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="makanan">
                                        <i class="fas fa-utensils"></i>
                                        Jenis Makanan
                                    </label>
                                    <input type="text" id="makanan" name="makanan" 
                                           placeholder="Contoh: Herbivora, Karnivora, Omnivora"
                                           value="<?php echo htmlspecialchars($_POST['makanan'] ?? ''); ?>">
                                    <small class="form-help">Jenis makanan atau pola makan fauna</small>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-file-alt"></i>
                                Informasi Detail
                            </h3>
                            
                            <div class="form-group">
                                <label for="deskripsi">
                                    <i class="fas fa-align-left"></i>
                                    Deskripsi <span class="required">*</span>
                                </label>
                                <textarea id="deskripsi" name="deskripsi" required rows="5" 
                                          placeholder="Tuliskan deskripsi lengkap tentang fauna ini..."><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                                <small class="form-help">Deskripsi lengkap tentang fauna (minimal 100 karakter)</small>
                            </div>

                            <div class="form-group">
                                <label for="habitat_detail">
                                    <i class="fas fa-map"></i>
                                    Detail Habitat
                                </label>
                                <textarea id="habitat_detail" name="habitat_detail" rows="3" 
                                          placeholder="Jelaskan detail habitat dan kondisi lingkungan..."><?php echo htmlspecialchars($_POST['habitat_detail'] ?? ''); ?></textarea>
                                <small class="form-help">Penjelasan detail tentang habitat dan kondisi lingkungan</small>
                            </div>

                            <div class="form-group">
                                <label for="perilaku">
                                    <i class="fas fa-running"></i>
                                    Perilaku
                                </label>
                                <textarea id="perilaku" name="perilaku" rows="3" 
                                          placeholder="Jelaskan perilaku dan kebiasaan fauna ini..."><?php echo htmlspecialchars($_POST['perilaku'] ?? ''); ?></textarea>
                                <small class="form-help">Perilaku, kebiasaan, dan pola hidup fauna</small>
                            </div>

                            <div class="form-group">
                                <label for="ciri_fisik">
                                    <i class="fas fa-eye"></i>
                                    Ciri Fisik
                                </label>
                                <textarea id="ciri_fisik" name="ciri_fisik" rows="3" 
                                          placeholder="Jelaskan ciri-ciri fisik yang khas dari fauna ini..."><?php echo htmlspecialchars($_POST['ciri_fisik'] ?? ''); ?></textarea>
                                <small class="form-help">Ciri-ciri fisik yang membedakan fauna ini</small>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-image"></i>
                                Gambar Fauna
                            </h3>
                            
                            <div class="form-group">
                                <label for="image">
                                    <i class="fas fa-upload"></i>
                                    Upload Gambar
                                </label>
                                <div class="file-upload-area">
                                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    <div class="file-upload-content">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Klik untuk memilih gambar atau drag & drop</p>
                                        <small>Format: JPG, PNG, GIF, WebP (Max: 5MB)</small>
                                    </div>
                                    <div id="imagePreview" class="image-preview" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview">
                                        <button type="button" onclick="removeImage()" class="remove-image">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <a href="fauna.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>
                            <button type="reset" class="btn btn-outline">
                                <i class="fas fa-undo"></i>
                                Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary" style="background: var(--gradient-secondary);">
                                <i class="fas fa-save"></i>
                                Simpan Fauna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Image preview functionality
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    input.parentElement.querySelector('.file-upload-content').style.display = 'none';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('image');
            const preview = document.getElementById('imagePreview');
            const uploadContent = document.querySelector('.file-upload-content');
            
            input.value = '';
            preview.style.display = 'none';
            uploadContent.style.display = 'block';
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

        // Auto-hide alerts
        document.querySelectorAll('.alert-close').forEach(btn => {
            btn.addEventListener('click', function() {
                this.parentElement.style.opacity = '0';
                setTimeout(() => this.parentElement.remove(), 300);
            });
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

        // Drag and drop functionality
        const fileUploadArea = document.querySelector('.file-upload-area');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            fileUploadArea.classList.add('drag-over');
        }

        function unhighlight(e) {
            fileUploadArea.classList.remove('drag-over');
        }

        fileUploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            document.getElementById('image').files = files;
            previewImage(document.getElementById('image'));
        }

        // Add form animation
        document.querySelectorAll('.form-section').forEach((section, index) => {
            section.style.animationDelay = `${index * 0.1}s`;
            section.style.animation = 'slideInUp 0.6s ease-out forwards';
        });
    </script>

    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fauna-form .section-title {
            background: linear-gradient(135deg, #e67e22, #d35400);
        }

        .fauna-form .section-title:hover {
            background: linear-gradient(135deg, #d35400, #c0392b);
        }
    </style>
</body>
</html>