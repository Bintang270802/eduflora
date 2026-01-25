<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include '../config/database.php';

$success_message = '';
$error_message = '';

// Ambil ID fauna
$fauna_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($fauna_id <= 0) {
    header('Location: fauna.php');
    exit();
}

// Ambil data fauna
$query = "SELECT * FROM fauna WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $fauna_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: fauna.php');
    exit();
}

$fauna = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = trim($_POST['nama']);
    $nama_ilmiah = trim($_POST['nama_ilmiah']);
    $deskripsi = trim($_POST['deskripsi']);
    $habitat = $_POST['habitat'];
    $habitat_detail = trim($_POST['habitat_detail']);
    $asal_daerah = trim($_POST['asal_daerah']);
    $status_konservasi = $_POST['status_konservasi'];
    $makanan = trim($_POST['makanan']);
    $perilaku = trim($_POST['perilaku']);
    $ciri_fisik = trim($_POST['ciri_fisik']);
    
    // Validasi
    if (empty($nama) || empty($nama_ilmiah) || empty($deskripsi) || empty($habitat) || empty($asal_daerah) || empty($status_konservasi)) {
        $error_message = "Semua field yang wajib harus diisi!";
    } else {
        // Handle upload gambar
        $image_name = $fauna['image']; // Default: gunakan gambar lama
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed) && $file_size <= 5000000) {
                $new_name = 'fauna_' . time() . '.' . $file_ext;
                $upload_path = '../assets/images/' . $new_name;
                
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Hapus gambar lama jika bukan default
                    if ($fauna['image'] && $fauna['image'] != 'assets/images/default-fauna.svg' && file_exists('../' . $fauna['image'])) {
                        unlink('../' . $fauna['image']);
                    }
                    $image_name = 'assets/images/' . $new_name;
                }
            }
        }
        
        // Update database
        $update_sql = "UPDATE fauna SET 
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
        
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, 'sssssssssssi', 
            $nama, $nama_ilmiah, $deskripsi, $habitat, $habitat_detail, 
            $asal_daerah, $status_konservasi, $makanan, $perilaku, 
            $ciri_fisik, $image_name, $fauna_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Data fauna berhasil diperbarui!";
            
            // Ambil data terbaru
            $query = "SELECT * FROM fauna WHERE id = ?";
            $stmt2 = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt2, 'i', $fauna_id);
            mysqli_stmt_execute($stmt2);
            $result = mysqli_stmt_get_result($stmt2);
            $fauna = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt2);
        } else {
            $error_message = "Gagal memperbarui data: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
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
    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin-right: 10px;
        }
        .btn-primary {
            background: #fd7e14;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .current-image {
            margin: 10px 0;
        }
        .current-image img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Fauna: <?php echo htmlspecialchars($fauna['nama']); ?></h1>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nama">Nama Fauna *</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($fauna['nama']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nama_ilmiah">Nama Ilmiah *</label>
                <input type="text" id="nama_ilmiah" name="nama_ilmiah" value="<?php echo htmlspecialchars($fauna['nama_ilmiah']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi *</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($fauna['deskripsi']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="habitat">Habitat *</label>
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
                    <option value="Tanah" <?php echo $fauna['habitat'] == 'Tanah' ? 'selected' : ''; ?>>Tanah</option>
                    <option value="Lainnya" <?php echo $fauna['habitat'] == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="habitat_detail">Detail Habitat</label>
                <textarea id="habitat_detail" name="habitat_detail" rows="3"><?php echo htmlspecialchars($fauna['habitat_detail']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="asal_daerah">Asal Daerah *</label>
                <input type="text" id="asal_daerah" name="asal_daerah" value="<?php echo htmlspecialchars($fauna['asal_daerah']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status_konservasi">Status Konservasi *</label>
                <select id="status_konservasi" name="status_konservasi" required>
                    <option value="">Pilih Status</option>
                    <option value="Aman" <?php echo $fauna['status_konservasi'] == 'Aman' ? 'selected' : ''; ?>>Aman</option>
                    <option value="Terancam" <?php echo $fauna['status_konservasi'] == 'Terancam' ? 'selected' : ''; ?>>Terancam</option>
                    <option value="Langka" <?php echo $fauna['status_konservasi'] == 'Langka' ? 'selected' : ''; ?>>Langka</option>
                    <option value="Kritis" <?php echo $fauna['status_konservasi'] == 'Kritis' ? 'selected' : ''; ?>>Kritis</option>
                    <option value="Punah di Alam" <?php echo $fauna['status_konservasi'] == 'Punah di Alam' ? 'selected' : ''; ?>>Punah di Alam</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="makanan">Makanan</label>
                <input type="text" id="makanan" name="makanan" value="<?php echo htmlspecialchars($fauna['makanan']); ?>">
            </div>
            
            <div class="form-group">
                <label for="perilaku">Perilaku</label>
                <textarea id="perilaku" name="perilaku" rows="3"><?php echo htmlspecialchars($fauna['perilaku']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="ciri_fisik">Ciri Fisik</label>
                <textarea id="ciri_fisik" name="ciri_fisik" rows="3"><?php echo htmlspecialchars($fauna['ciri_fisik']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Gambar Fauna</label>
                <?php if ($fauna['image']): ?>
                    <div class="current-image">
                        <p><strong>Gambar Saat Ini:</strong></p>
                        <img src="../<?php echo $fauna['image']; ?>" alt="<?php echo htmlspecialchars($fauna['nama']); ?>">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Format: JPG, PNG, GIF, WebP (Max: 5MB). Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Fauna
                </button>
                <a href="fauna.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</body>
</html>