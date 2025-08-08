# POLRI Backend - Dashboard Monitoring

Backend API untuk sistem monitoring dashboard POLRI yang menangani log login dan statistik pengguna.

## 🚀 Fitur

- **Webhook Receiver**: Menerima data login dari sistem RADIUS
- **Statistics API**: Endpoint untuk mendapatkan statistik login
- **Rate Limiting**: Proteksi API dari abuse
- **Error Handling**: Penanganan error yang konsisten
- **Service Layer**: Arsitektur yang bersih dan maintainable

## 📋 Requirements

- PHP 8.2+
- Laravel 12
- SQLite/MySQL/PostgreSQL

## 🛠️ Installation

1. Clone repository
```bash
git clone <repository-url>
cd polri-be
```

2. Install dependencies
```bash
composer install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database di `.env`
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

5. Run migrations
```bash
php artisan migrate
```

6. Start server
```bash
php artisan serve
```

## 📡 API Endpoints

### Webhook
- `POST /api/webhook` - Menerima data login dari RADIUS

### Statistics
- `GET /api/stats/active-users` - Pengguna aktif (15 menit terakhir)
- `GET /api/stats/unique-users` - Jumlah pengguna unik
- `GET /api/stats/list-unique-users` - Daftar pengguna unik
- `GET /api/stats/last-login` - Data login terakhir per pengguna
- `GET /api/stats/success-logins` - Jumlah login berhasil
- `GET /api/stats/failed-logins` - Jumlah login gagal
- `GET /api/stats/logins-by-date?from=2024-01-01&to=2024-01-31` - Statistik login per tanggal

## 🔒 Security

- Rate limiting: 30 requests/minute untuk webhook, 60 requests/minute untuk stats
- Input validation untuk semua endpoint
- Error handling yang aman
- Logging untuk monitoring

## 🏗️ Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── StatsController.php
│   │   └── WebhookController.php
│   └── Middleware/
│       └── RateLimitMiddleware.php
├── Models/
│   ├── LoginLog.php
│   └── User.php
├── Services/
│   └── LoginLogService.php
└── Exceptions/
    └── Handler.php
```

## 📊 Database Schema

### login_logs
- `id` - Primary key
- `username` - Username pengguna
- `ip_address` - IP address
- `success` - Status login (true/false)
- `raw_payload` - Data mentah dari webhook
- `created_at` - Timestamp login
- `updated_at` - Timestamp update

## 🧪 Testing

```bash
php artisan test
```

## 📝 Logging

Logs tersimpan di `storage/logs/laravel.log` dengan format:
- Info: Login berhasil dibuat
- Warning: Validasi webhook gagal
- Error: Error processing webhook atau stats

## 🔧 Development

```bash
# Development dengan hot reload
composer run dev

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## 📄 License

MIT License
