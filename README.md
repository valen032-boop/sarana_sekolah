# Aplikasi Pengaduan Sarana Sekolah
PHP Native + PDO + MySQL + Bootstrap 5 (CDN).

## Cara menjalankan (Laragon)
1. Start All di Laragon (Apache + MySQL).
2. Salin folder `pengaduan_sekolah` ke `C:\laragon\www\`.
3. Impor `database/pengaduan_sekolah.sql` lewat phpMyAdmin / HeidiSQL
   (database `pengaduan_sekolah` dibuat otomatis oleh file SQL).
4. Buka `http://localhost/pengaduan_sekolah` atau `http://pengaduan_sekolah.test`.

## Akun demo
| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |
| Murid | budi / sari / rizky | murid123 |

## Pengaturan database
Ubah di `config/koneksi.php` bila user/password MySQL berbeda (default Laragon: root, tanpa password).

## Struktur
- `config/`   koneksi PDO & helper auth/session
- `includes/` header, footer, partial detail
- `murid/`    dashboard, buat pengaduan, riwayat, detail
- `admin/`    dashboard, data pengaduan (filter), detail + update status
- `uploads/`  foto bukti
- `database/` file SQL
