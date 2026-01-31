# Setup Login Database - EduFlora

## Langkah-langkah Setup

### 1. Jalankan Setup Database
Buka browser dan akses: `http://localhost/PWD/flora-fauna/admin/setup_database.php`

File ini akan:
- Membuat tabel `admin_users` 
- Menambahkan 2 user default dengan password ter-hash

### 2. Login Credentials Default

**Super Admin:**
- Username: `admin`
- Password: `admin123`

**Admin:**
- Username: `eduflora_admin` 
- Password: `password123`

### 3. Fitur Login Baru

✅ **Password tersimpan di database dengan hash**
✅ **Verifikasi password menggunakan `password_verify()`**
✅ **Session management yang lebih lengkap**
✅ **Update last login timestamp**
✅ **Role-based access (admin/super_admin)**
✅ **User status (active/inactive)**

### 4. Struktur Tabel Admin Users

```sql
CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','super_admin') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
```

### 5. Menambah Admin Baru

Akses: `http://localhost/PWD/flora-fauna/admin/create_admin.php`

Form ini memungkinkan Anda:
- Membuat admin user baru
- Generate password hash
- Set role (admin/super_admin)

### 6. File yang Diubah/Ditambah

**File Baru:**
- `database/admin_users_table.sql` - SQL untuk membuat tabel
- `admin/setup_database.php` - Setup otomatis database
- `admin/create_admin.php` - Form membuat admin baru
- `admin/generate_hash.php` - Generator password hash

**File Diubah:**
- `admin/login.php` - Update untuk menggunakan database

### 7. Keamanan

- Password di-hash menggunakan `password_hash()` PHP
- Verifikasi menggunakan `password_verify()`
- Session management yang aman
- SQL prepared statements untuk mencegah injection
- User status active/inactive

### 8. Testing

1. Akses `admin/setup_database.php` untuk setup
2. Akses `admin/login.php` untuk login
3. Coba login dengan credentials default
4. Akses `admin/create_admin.php` untuk membuat user baru

## Troubleshooting

**Jika ada error "Table doesn't exist":**
- Pastikan sudah menjalankan `setup_database.php`
- Cek koneksi database di `config/database.php`

**Jika login gagal:**
- Pastikan username dan password benar
- Cek apakah user status `is_active = 1`
- Gunakan `create_admin.php` untuk membuat user baru

**Jika ingin reset password:**
- Gunakan `create_admin.php` untuk generate hash baru
- Update manual di database atau buat user baru