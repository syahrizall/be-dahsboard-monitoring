# POLRI Backend - Dashboard Monitoring

Backend API untuk sistem monitoring dashboard POLRI yang menangani log login dan statistik pengguna.

## 🚀 Fitur

- **Authentication**: Login/logout dengan token-based authentication
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

6. Seed database (membuat user default)
```bash
php artisan db:seed
```

7. Start server
```bash
php artisan serve
```

## 🔐 Authentication

### Default User
- **Email**: `admin@polri.com`
- **Password**: `password123`

### Login
```bash
POST /api/login
Content-Type: application/json

{
    "email": "admin@polri.com",
    "password": "password123"
}
```

Response:
```json
{
    "status": "success",
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@polri.com"
    },
    "token": "1|abc123..."
}
```

### Logout
```bash
POST /api/logout
Authorization: Bearer {token}
```

### Get Current User
```bash
GET /api/me
Authorization: Bearer {token}
```

## 📡 API Endpoints

### Authentication
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (protected)
- `GET /api/me` - Get current user (protected)

### Webhook
- `POST /api/webhook` - Menerima data login dari RADIUS

### Statistics
- `GET /api/stats/active-users` - Pengguna aktif (15 menit terakhir) (protected)
- `GET /api/stats/unique-users` - Jumlah pengguna unik (protected)
- `GET /api/stats/list-unique-users` - Daftar pengguna unik (protected)
- `GET /api/stats/last-login` - Data login terakhir per pengguna (protected)
- `GET /api/stats/success-logins` - Jumlah login berhasil (protected)
- `GET /api/stats/failed-logins` - Jumlah login gagal (protected)
- `GET /api/stats/logins-by-date?from=2024-01-01&to=2024-01-31` - Statistik login per tanggal (protected)

## 🔒 Security

- Token-based authentication dengan Laravel Sanctum
- Rate limiting: 30 requests/minute untuk webhook, 60 requests/minute untuk stats
- Input validation untuk semua endpoint
- Error handling yang aman
- Logging untuk monitoring

## 🏗️ Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── StatsController.php
│   │   └── WebhookController.php
│   └── Middleware/
│       └── RateLimitMiddleware.php
├── Models/
│   ├── LoginLog.php
│   └── User.php
└── Services/
    └── LoginLogService.php
```

## 📊 Database Schema

### users
- `id` - Primary key
- `name` - Nama user
- `email` - Email user (unique)
- `password` - Password (hashed)
- `email_verified_at` - Timestamp verifikasi email
- `remember_token` - Token untuk remember me
- `created_at` - Timestamp pembuatan
- `updated_at` - Timestamp update

### login_logs
- `id` - Primary key
- `username` - Username dari RADIUS
- `ip_address` - IP address login
- `success` - Status login (true/false)
- `raw_payload` - Data mentah dari RADIUS
- `created_at` - Timestamp login

### personal_access_tokens
- `id` - Primary key
- `tokenable_type` - Model type
- `tokenable_id` - Model ID
- `name` - Token name
- `token` - Hashed token
- `abilities` - Token abilities
- `last_used_at` - Last used timestamp
- `expires_at` - Expiration timestamp
- `created_at` - Created timestamp
- `updated_at` - Updated timestamp

## 🧪 Testing

### Login Test
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@polri.com",
    "password": "password123"
  }'
```

### Protected Route Test
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer {token}"
```

## 📝 Notes

- Sistem ini menggunakan Laravel Sanctum untuk token-based authentication
- Hanya ada satu user default (admin@polri.com)
- Token akan expired sesuai konfigurasi Sanctum
- Semua endpoint stats memerlukan authentication (protected)
- Endpoint webhook tidak memerlukan authentication (untuk sistem RADIUS)
