# Sistem Manajemen Forecast (Prediksi Penjualan)

Sistem komprehensif untuk manajemen penjualan, pelacakan stok inventaris, dan pembuatan laporan prediksi (forecasting) berbasis AI. Dibangun menggunakan Laravel dan Python FastAPI.

## 🚀 Teknologi yang Digunakan (Tech Stack)

### 1. Aplikasi Web (Laravel App)
- **Framework**: Laravel 12.0 (PHP 8.2)
- **Panel Admin**: Filament 4.0 (TALL Stack)
- **Database**: MySQL 8.0
- **Paket Utama**:
    - `spatie/laravel-permission`: Manajemen hak akses (Admin, Owner, Employee).
    - `spatie/laravel-activitylog`: Audit Log / Rekam jejak aktivitas pengguna.
    - `barryvdh/laravel-dompdf`: Pembuatan laporan PDF.

### 2. Layanan AI (Python Service)
- **Framework**: Python FastAPI
- **Mesin Prediksi**: Prophet, Numpy, Pandas
- **Komunikasi**: REST API untuk integrasi dengan Laravel.

### 3. Infrastruktur
- **Kontainerisasi**: Docker & Docker Compose
- **Web Server**: Nginx

## 🛠️ Prasyarat (Prerequisites)
- Telah terinstal **Docker & Docker Compose**.
- Telah terinstal **Git**.
- **Spesifikasi Server Minimum** (Teruji di):
    - **OS**: Debian
    - **CPU**: 1.0 GHz
    - **RAM**: 1 GB
    - **Penyimpanan**: 20 GB HDD

## ⚙️ Instalasi & Konfigurasi (Pengembangan)

1. **Clone repositori**:
   ```bash
   git clone <repository-url>
   cd forecast-management-system
   ```

2. **Konfigurasi Lingkungan (.env)**:
   - Salin `.env.example` menjadi `.env` di folder `business-logic/` dan `artificial-intelligence-logic/`.
   - Perbarui `APP_URL` dan konfigurasi database di dalam `.env`.

3. **Jalankan Kontainer**:
   ```bash
   docker compose up -d --build
   ```

4. **Inisialisasi Aplikasi Laravel**:
   ```bash
   # Instal dependensi (hanya untuk instalasi pertama)
   docker compose exec app composer install --no-dev --optimize-autoloader

   # Pengaturan aplikasi
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan db:seed --class=UserSeeder
   docker compose exec app php artisan storage:link

   # Atur izin folder (Permissions)
   docker compose exec app chmod -R 775 storage bootstrap/cache
   docker compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

5. **Akses Aplikasi**:
   - Web App: `http://localhost:8000/admin`
   - AI API Docs: `http://localhost:8025/docs`

### 🔑 Akun Default (Login)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `123` |
| **Owner** | `owner@example.com` | `123` |
| **Employee** | `employee@example.com` | `123` |

> [!IMPORTANT]
> Mohon segera ganti password ini setelah login pertama kali demi keamanan sistem.

---

## 🚀 Panduan Deployment Produksi

### 1. Konfigurasi Lingkungan Produksi
Gunakan file terpisah untuk produksi guna menghindari kesalahan konfigurasi.
- **Laravel**: Buat file `business-logic/.env.production`
- **AI Service**: Buat file `artificial-intelligence-logic/.env.production`

Perbarui nilai kritikal di `.env.production`:
- `APP_KEY=` (generate setelah build)
- `APP_URL=https://domainanda.com`
- `DB_PASSWORD=<password-kuat>`
- `APP_DEBUG=false`
- `APP_ENV=production`

### 2. Perbarui Password Docker Compose
Edit `docker compose.prod.yml` dan ubah nilai `MYSQL_ROOT_PASSWORD` serta `MYSQL_PASSWORD`.

### 3. Build & Jalankan Layanan
```bash
docker compose -f docker compose.yml -f docker compose.prod.yml up -d --build
```

### 4. Inisialisasi Data Produksi
```bash
# Generate key
docker compose exec app php artisan key:generate

# Jalankan migrasi & seed
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=ProductWithHistorySeeder

# Optimasi untuk produksi
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 5. Setup SSL / HTTPS (Direkomendasikan)
Gunakan Nginx reverse proxy dengan Let's Encrypt:
```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d domainanda.com
```

---

## 🛠️ Pemeliharaan & Pemantauan (Maintenance)

### 💾 Backup Database
Buat script backup dan tambahkan ke crontab:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker compose exec -T db mysqldump -u root -p$DB_ROOT_PASSWORD db_forecast > backups/backup_$DATE.sql
```

### 📊 Pemantauan Log
- **Semua layanan**: `docker compose logs -f`
- **Log Laravel**: `docker compose exec app tail -f storage/logs/laravel.log`

### 🔄 Pembaruan (Updates)
```bash
git pull
docker compose -f docker compose.yml -f docker compose.prod.yml up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

---

## 📁 Struktur Proyek

```
├── business-logic/          # Aplikasi Laravel (Backend & Admin Panel)
├── artificial-intelligence-logic/  # Layanan Python FastAPI (Mesin Prediksi)
├── docker/                  # Konfigurasi Docker & Nginx
├── docker compose.yml      # Konfigurasi Dasar
└── docker compose.prod.yml # Konfigurasi Produksi
```

## ✨ Fitur Utama

- **Manajemen Inventaris**: Pelacakan stok produk secara real-time.
- **Sistem Penjualan**: Input transaksi dengan pengurangan stok dan perhitungan total otomatis.
- **AI Forecasting**: Membuat prediksi penjualan 30 hari ke depan berdasarkan data historis.
- **Audit Logs**: Rekaman keamanan mengenai "siapa, melakukan apa, dan kapan" di semua modul.
- **RBAC**: Keamanan berbasis peran (Admin/Owner untuk manajemen, Employee untuk operasional).
- **Dashboard**: Visualisasi analitik penjualan dan tren produk terpopuler.

## 🆘 Troubleshooting (Penanganan Masalah)

- **Cek Log Error**: `docker compose exec app tail -n 100 storage/logs/laravel.log`
- **Hapus Cache**: `docker compose exec app php artisan optimize:clear`
- **Backup Database**: `docker compose exec db mysqldump -u root -p db_forecast > backup.sql`

## 🚀 Deployment Produksi

Untuk panduan lengkap mengenai deployment ke server produksi, silakan merujuk ke **[DEPLOYMENT.md](./DEPLOYMENT.md)**.
