# Forecast Management System

A comprehensive system for sales management, inventory tracking, and AI-driven forecasting reports. Built with Laravel and Python FastAPI.

## 🚀 Tech Stack

### 1. Web Application (Laravel App)

- **Framework**: Laravel 12.0 (PHP 8.2)
- **Admin Panel**: Filament 4.0 (TALL Stack)
- **Database**: MySQL 8.0
- **Packages**:
  - `spatie/laravel-permission`: Role-Based Access Control (Admin, Owner, Employee).
  - `spatie/laravel-activitylog`: Comprehensive Audit Logs/Activity tracking.
  - `barryvdh/laravel-dompdf`: PDF report generation.

### 2. AI Service (Python Service)

- **Framework**: Python FastAPI
- **Forecasting Engine**: Prophet, Numpy, Pandas
- **Communication**: REST API for Laravel integration.

### 3. Infrastructure

- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx

## 🛠️ Prerequisites

- **Docker & Docker Compose** installed.
- **Git** (for cloning).
- **Minimum Server Specifications** (Tested on):
  - **OS**: Debian
  - **CPU**: 1.0 GHz
  - **RAM**: 1 GB
  - **Storage**: 20 GB HDD

## ⚙️ Installation & Setup (Development)

1. **Clone the repository**:

   ```bash
   git clone <repository-url>
   cd forecast-system
   ```

2. **Configure Environment**:
   - Copy `.env.example` to `.env` in both `business-logic/` and `artificial-intelligence-logic/`.
   - Update `APP_URL` and database credentials in `.env`.

3. **Start the containers**:

   ```bash
   docker compose up -d --build
   ```

4. **Initialize Laravel Application**:

   ```bash
   # Install dependencies (only for first-time setup)
   docker compose exec app composer install --no-dev --optimize-autoloader

   # Application setup
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan db:seed --class=UserSeeder
   docker compose exec app php artisan storage:link

   # Set folder permissions
   docker compose exec app chmod -R 775 storage bootstrap/cache
   docker compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

5. **Access the application**:
   - Web App: `http://localhost:8000/admin`
   - AI API Docs: `http://localhost:8025/docs`

### 🔑 Default Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@example.com` | `123` |
| **Owner** | `owner@example.com` | `123` |
| **Employee** | `employee@example.com` | `123` |

> [!IMPORTANT]
> Please change these passwords immediately after your first login for security reasons.

---

## 🚀 Production Deployment Guide

### 1. Configure Production Environment

We use separate files for production to prevent configuration errors.

- **Laravel**: Create `business-logic/.env.production`
- **AI Service**: Create `artificial-intelligence-logic/.env.production`

Update critical values in `.env.production`:

- `APP_KEY=` (generate after build)
- `APP_URL=https://yourdomain.com`
- `DB_PASSWORD=<strong-password>`
- `APP_DEBUG=false`
- `APP_ENV=production`

### 2. Update Docker Compose Passwords

Edit `docker compose.prod.yml` and change `MYSQL_ROOT_PASSWORD` and `MYSQL_PASSWORD`.

### 3. Build & Start Services

```bash
docker compose -f docker compose.yml -f docker compose.prod.yml up -d --build
```

### 4. Initialize Production Data

```bash
# Generate key
docker compose exec app php artisan key:generate

# Run migrations & seed
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --class=ProductWithHistorySeeder

# Optimize for production
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 5. SSL / HTTPS Setup (Recommended)

Add Nginx reverse proxy with Let's Encrypt:

```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

---

## 🛠️ Maintenance & Monitoring

### 💾 Database Backups

Create a backup script and add to crontab:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker compose exec -T db mysqldump -u root -p$DB_ROOT_PASSWORD db_forecast > backups/backup_$DATE.sql
```

### 📊 Monitoring Logs

- **All services**: `docker compose logs -f`
- **Laravel logs**: `docker compose exec app tail -f storage/logs/laravel.log`

### 🔄 Updates

```bash
git pull
docker compose -f docker compose.yml -f docker compose.prod.yml up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```

---

## 📁 Project Structure

```
├── business-logic/          # Laravel application (Backend & Admin Panel)
├── artificial-intelligence-logic/  # Python FastAPI service (Forecasting Engine)
├── docker/                  # Docker & Nginx configurations
├── docker compose.yml      # Base setup
└── docker compose.prod.yml # Production overrides
```

## ✨ Key Features

- **Inventory Management**: Real-time product stock tracking.
- **Sales System**: Transaction input with automated stock reduction and price calculation.
- **AI Forecasting**: Generate 30-day sales projections based on historical data.
- **Audit Logs**: Secure tracking of "who did what and when" across all modules.
- **RBAC**: Multi-role security (Admin/Owner for management, Employee for operations).
- **Dashboard**: Visual sales analytics and top product trends.

## 🆘 Troubleshooting

- **Check Logs**: `docker compose exec app tail -n 100 storage/logs/laravel.log`
- **Clear Cache**: `docker compose exec app php artisan optimize:clear`
- **Database Backup**: `docker compose exec db mysqldump -u root -p db_forecast > backup.sql`

## 🚀 Production Deployment

For complete production deployment instructions, please refer to **[DEPLOYMENT.md](./DEPLOYMENT.md)**...
