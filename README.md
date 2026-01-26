# 🌿 EduFlora - Sistem Informasi Edukasi Flora dan Fauna Indonesia

EduFlora adalah sistem informasi berbasis web yang dirancang untuk memberikan edukasi tentang keanekaragaman flora dan fauna Indonesia. Aplikasi ini menyediakan informasi lengkap mengenai berbagai spesies tumbuhan dan hewan endemik Indonesia beserta status konservasinya.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi Database](#-konfigurasi-database)
- [Struktur Proyek](#-struktur-proyek)
- [Penggunaan](#-penggunaan)
- [Panel Admin](#-panel-admin)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)

## ✨ Fitur Utama

### 🌱 Fitur Publik
- **Katalog Flora**: Jelajahi berbagai spesies tumbuhan Indonesia
- **Katalog Fauna**: Temukan informasi lengkap tentang hewan Indonesia
- **Pencarian & Filter**: Cari berdasarkan nama, habitat, atau status konservasi
- **Detail Spesies**: Informasi lengkap meliputi:
  - Nama ilmiah dan nama umum
  - Deskripsi dan karakteristik
  - Habitat dan distribusi
  - Status konservasi
  - Manfaat dan kegunaan
- **Responsive Design**: Tampilan optimal di desktop, tablet, dan mobile

### 🔧 Panel Administrasi
- **Dashboard Admin**: Kelola data flora dan fauna
- **CRUD Operations**: Tambah, edit, hapus, dan lihat data
- **Upload Gambar**: Kelola gambar spesies dengan validasi format
- **Sistem Pencarian**: Filter dan cari data dalam panel admin
- **Notifikasi**: Feedback untuk setiap aksi admin

## 🛠 Teknologi yang Digunakan

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Styling**: Custom CSS dengan Flexbox & Grid
- **Icons**: Font Awesome 6.0
- **Fonts**: Google Fonts (Poppins)

## 📋 Persyaratan Sistem

- **Web Server**: Apache 2.4+ atau Nginx
- **PHP**: Versi 7.4 atau lebih tinggi
- **MySQL**: Versi 5.7 atau lebih tinggi
- **Extensions PHP**:
  - mysqli
  - gd (untuk upload gambar)
  - fileinfo (untuk validasi file)

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/username/eduflora.git
cd eduflora
```

### 2. Setup Web Server
Pastikan web server Anda mengarah ke direktori proyek atau salin file ke direktori web server (htdocs/www).

### 3. Konfigurasi Database
Buat database MySQL baru:
```sql
CREATE DATABASE eduflora_db;
```

### 4. Import Database
Import file database yang disediakan:
```bash
mysql -u username -p eduflora_db < database/database.sql
```

### 5. Konfigurasi Koneksi Database
Edit file `config/database.php`:
```php
<?php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'eduflora_db';
?>
```

### 6. Set Permissions
Pastikan direktori `assets/images/` memiliki permission write:
```bash
chmod 755 assets/images/
```

## 🗄 Konfigurasi Database

### Struktur Database

#### Tabel Flora
- `id` - Primary key
- `nama` - Nama umum flora
- `nama_ilmiah` - Nama ilmiah (Latin)
- `deskripsi` - Deskripsi lengkap
- `habitat` - Jenis habitat
- `habitat_detail` - Detail habitat
- `asal_daerah` - Daerah asal
- `status_konservasi` - Status konservasi IUCN
- `manfaat` - Manfaat dan kegunaan
- `ciri_khusus` - Ciri khas spesies
- `image` - Path gambar
- `created_at` - Tanggal dibuat
- `updated_at` - Tanggal diperbarui

#### Tabel Fauna
- `id` - Primary key
- `nama` - Nama umum fauna
- `nama_ilmiah` - Nama ilmiah (Latin)
- `deskripsi` - Deskripsi lengkap
- `habitat` - Jenis habitat
- `habitat_detail` - Detail habitat
- `asal_daerah` - Daerah asal
- `status_konservasi` - Status konservasi IUCN
- `makanan` - Jenis makanan
- `perilaku` - Perilaku dan kebiasaan
- `ciri_fisik` - Ciri fisik
- `image` - Path gambar
- `created_at` - Tanggal dibuat
- `updated_at` - Tanggal diperbarui

## 📁 Struktur Proyek

```
eduflora/
├── admin/                  # Panel administrasi
│   ├── login.php          # Halaman login admin
│   ├── flora.php          # Kelola data flora
│   ├── fauna.php          # Kelola data fauna
│   ├── flora_add.php      # Tambah flora
│   ├── fauna_add.php      # Tambah fauna
│   ├── flora_edit.php     # Edit flora
│   ├── fauna_edit.php     # Edit fauna
│   ├── flora_view.php     # Detail flora
│   └── fauna_view.php     # Detail fauna
├── assets/                # Asset statis
│   ├── css/              # File CSS
│   ├── js/               # File JavaScript
│   └── images/           # Gambar upload
├── config/               # Konfigurasi
│   └── database.php      # Konfigurasi database
├── database/             # Database
│   └── database.sql      # Schema dan data awal
├── index.php             # Halaman utama
├── flora.php             # Katalog flora
├── fauna.php             # Katalog fauna
├── get_detail.php        # API detail spesies
└── README.md             # Dokumentasi
```

## 🎯 Penggunaan

### Akses Publik
1. Buka `http://localhost/eduflora` di browser
2. Jelajahi katalog flora dan fauna
3. Gunakan fitur pencarian dan filter
4. Klik spesies untuk melihat detail lengkap

### Panel Admin
1. Akses `http://localhost/eduflora/admin/login.php`
2. Login dengan kredensial admin
3. Kelola data flora dan fauna:
   - Tambah spesies baru
   - Edit informasi spesies
   - Upload gambar spesies
   - Hapus data yang tidak diperlukan

## 🔐 Panel Admin

### Login Admin
- **URL**: `/admin/login.php`
- **Username**: admin (default)
- **Password**: admin123 (default)

> ⚠️ **Penting**: Ubah kredensial default setelah instalasi untuk keamanan.

### Fitur Admin
- **Dashboard**: Overview data flora dan fauna
- **Manajemen Flora**: CRUD lengkap untuk data tumbuhan
- **Manajemen Fauna**: CRUD lengkap untuk data hewan
- **Upload Gambar**: Dukungan format JPG, PNG, GIF, WebP (max 5MB)
- **Validasi Data**: Validasi form dan file upload
- **Responsive**: Panel admin responsive untuk semua device

## 🤝 Kontribusi

Kami menyambut kontribusi dari komunitas! Untuk berkontribusi:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

### Guidelines Kontribusi
- Ikuti standar coding PHP PSR-12
- Tulis komentar yang jelas dan deskriptif
- Test fitur baru sebelum submit
- Update dokumentasi jika diperlukan

## 📝 Changelog

### Version 1.0.0 (2026-01-26)
- ✅ Rilis awal EduFlora
- ✅ Katalog flora dan fauna Indonesia
- ✅ Panel administrasi lengkap
- ✅ Sistem pencarian dan filter
- ✅ Upload dan manajemen gambar
- ✅ Responsive design
- ✅ Validasi form dan keamanan dasar

## 🐛 Bug Reports & Feature Requests

Jika Anda menemukan bug atau ingin mengusulkan fitur baru:
1. Cek [Issues](https://github.com/username/eduflora/issues) yang sudah ada
2. Buat issue baru dengan template yang sesuai
3. Berikan informasi detail dan langkah reproduksi

## 📄 Lisensi

Proyek ini dilisensikan under MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 👥 Tim Pengembang

- **Developer**: [Nama Developer]
- **Designer**: [Nama Designer]
- **Content**: [Nama Content Creator]

## 🙏 Acknowledgments

- Data flora dan fauna dari berbagai sumber ilmiah
- Icons dari [Font Awesome](https://fontawesome.com/)
- Fonts dari [Google Fonts](https://fonts.google.com/)
- Inspirasi dari berbagai sistem informasi biodiversitas

## 📞 Kontak

- **Email**: contact@eduflora.com
- **Website**: https://eduflora.com
- **GitHub**: https://github.com/username/eduflora

---

**EduFlora** - Melestarikan Keanekaragaman Hayati Indonesia Melalui Edukasi Digital

© 2026 EduFlora. Semua hak cipta dilindungi.