# 🔐 Instruksi Setup Login Database - EduFlora

## ✅ Apa yang Sudah Dibuat

Sistem login Anda telah diupgrade dengan fitur-fitur berikut:

### 🆕 Fitur Baru:
- ✅ **Username dan password tersimpan di database**
- ✅ **Password di-hash untuk keamanan**
- ✅ **Role-based access (admin/super_admin)**
- ✅ **Session management yang lengkap**
- ✅ **User status active/inactive**
- ✅ **Last login tracking**
- ✅ **Form untuk membuat admin baru**

## 🚀 Langkah Setup (WAJIB DILAKUKAN)

### 1. Setup Database
Buka browser dan akses:
```
http://localhost/PWD/flora-fauna/admin/setup_database.php
```

**Apa yang terjadi:**
- Membuat tabel `admin_users` baru
- Menambahkan 2 user default
- Mengatur password hash yang aman

### 2. Test Koneksi (Opsional)
Untuk memastikan semuanya berjalan:
```
http://localhost/PWD/flora-fauna/admin/test_connection.php
```

### 3. Login dengan Akun Default

**Super Admin:**
- Username: `admin`
- Password: `admin123`

**Admin Biasa:**
- Username: `eduflora_admin`
- Password: `password123`

## 🔧 Mengelola Admin Users

### Membuat Admin Baru
Akses: `http://localhost/PWD/flora-fauna/admin/create_admin.php`

Form ini memungkinkan:
- Buat username dan password baru
- Set nama lengkap dan email
- Pilih role (admin/super_admin)
- Password otomatis di-hash

### Generate Password Hash
Jika ingin membuat hash manual:
```
http://localhost/PWD/flora-fauna/admin/generate_hash.php?password=passwordanda
```

## 📁 File-File Baru

### Database:
- `database/admin_users_table.sql` - SQL untuk tabel admin
- `admin/setup_database.php` - Setup otomatis
- `admin/test_connection.php` - Test koneksi

### Admin Management:
- `admin/create_admin.php` - Form buat admin baru
- `admin/generate_hash.php` - Generator password hash
- `admin/session_info.php` - Komponen info session

### Dokumentasi:
- `SETUP_LOGIN.md` - Dokumentasi teknis
- `INSTRUKSI_LOGIN.md` - Instruksi untuk user

### File yang Diubah:
- `admin/login.php` - Update untuk database login

## 🔒 Keamanan

### Password Hashing:
```php
// Password di-hash dengan PHP password_hash()
$hash = password_hash($password, PASSWORD_DEFAULT);

// Verifikasi dengan password_verify()
if (password_verify($password, $hash)) {
    // Login berhasil
}
```

### SQL Injection Protection:
```php
// Menggunakan prepared statements
$stmt = mysqli_prepare($conn, "SELECT * FROM admin_users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
```

### Session Security:
- Session variables yang lengkap
- Automatic session cleanup saat logout
- Role-based access control

## 🐛 Troubleshooting

### Error "Table doesn't exist":
1. Pastikan sudah menjalankan `setup_database.php`
2. Cek koneksi database di `config/database.php`
3. Pastikan database `eduflora_db` sudah ada

### Login Gagal:
1. Cek username dan password
2. Pastikan user status `is_active = 1`
3. Gunakan `test_connection.php` untuk debug
4. Buat user baru dengan `create_admin.php`

### Lupa Password:
1. Akses `create_admin.php`
2. Buat user baru dengan username berbeda
3. Atau update manual di database

## 📊 Struktur Database Baru

```sql
admin_users:
- id (Primary Key)
- username (Unique)
- password (Hashed)
- full_name
- email
- role (admin/super_admin)
- is_active (1/0)
- last_login
- created_at
- updated_at
```

## 🎯 Langkah Selanjutnya

1. **WAJIB:** Jalankan `setup_database.php`
2. Test login dengan akun default
3. Buat admin user baru sesuai kebutuhan
4. Hapus atau nonaktifkan akun default jika perlu
5. Backup database secara berkala

## 💡 Tips

- Gunakan password yang kuat untuk production
- Backup database sebelum update
- Test semua fitur setelah setup
- Dokumentasikan username/password admin
- Pertimbangkan menambah fitur forgot password

---

**🎉 Selamat! Sistem login Anda sekarang lebih aman dan fleksibel!**