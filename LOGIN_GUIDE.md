# Panduan Fitur Login - POLRI Backend

## 📋 Overview

Fitur login telah ditambahkan ke aplikasi POLRI Backend dengan menggunakan Laravel Sanctum untuk token-based authentication. Sistem ini dirancang untuk satu user saja sesuai permintaan.

## 🔐 Default User

**Kredensial Default:**
- **Email**: `admin@polri.com`
- **Password**: `password123`

## 🚀 Cara Menggunakan

### 1. Login

**Endpoint:** `POST /api/login`

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@polri.com",
    "password": "password123"
  }'
```

**Response Success:**
```json
{
    "status": "success",
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@polri.com"
    },
    "token": "1|C7enPda6Nvi5kGcBpysv5NPHNozWmRzFOHJ6j86L1d04f178"
}
```

**Response Error:**
```json
{
    "status": "error",
    "message": "Invalid credentials"
}
```

### 2. Get Current User

**Endpoint:** `GET /api/me`

**Request:**
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
    "status": "success",
    "user": {
        "id": 1,
        "name": "Admin",
        "email": "admin@polri.com"
    }
}
```

### 3. Logout

**Endpoint:** `POST /api/logout`

**Request:**
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {token}"
```

**Response:**
```json
{
    "status": "success",
    "message": "Logout successful"
}
```

## 🔒 Security Features

1. **Token-based Authentication**: Menggunakan Laravel Sanctum
2. **Password Hashing**: Password di-hash menggunakan bcrypt
3. **Token Expiration**: Token dapat dikonfigurasi untuk expired
4. **CSRF Protection**: Built-in CSRF protection dari Laravel
5. **Rate Limiting**: Rate limiting untuk mencegah abuse

## 🛠️ Implementation Details

### Files yang Ditambahkan/Dimodifikasi:

1. **`app/Http/Controllers/AuthController.php`** - Controller untuk authentication
2. **`app/Models/User.php`** - Model User dengan trait HasApiTokens
3. **`routes/api.php`** - Routes untuk authentication
4. **`database/seeders/DatabaseSeeder.php`** - Seeder untuk user default
5. **`README.md`** - Dokumentasi yang diupdate

### Dependencies yang Ditambahkan:

- `laravel/sanctum` - Untuk token-based authentication

### Database Tables:

1. **`users`** - Tabel user
2. **`personal_access_tokens`** - Tabel untuk menyimpan token

## 🧪 Testing

### Test Login dengan cURL:

```bash
# Test login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@polri.com",
    "password": "password123"
  }'

# Test protected route
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer {token}"

# Test logout
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {token}"
```

### Test dengan Postman:

1. **Login Request:**
   - Method: `POST`
   - URL: `http://localhost:8000/api/login`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
   ```json
   {
       "email": "admin@polri.com",
       "password": "password123"
   }
   ```

2. **Protected Route Request:**
   - Method: `GET`
   - URL: `http://localhost:8000/api/me`
   - Headers: `Authorization: Bearer {token}`

## 🔧 Configuration

### Sanctum Configuration

File: `config/sanctum.php`

```php
'expiration' => null, // Token tidak expired (null = tidak expired)
'guard' => ['web'], // Guard yang digunakan
```

### Environment Variables

Pastikan environment variables berikut sudah diset:

```env
APP_KEY=base64:...
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

## 📝 Notes

1. **Single User**: Sistem dirancang untuk satu user saja
2. **Token Persistence**: Token akan tetap valid sampai user logout atau token di-delete
3. **No Registration**: Tidak ada fitur registrasi user baru
4. **Password Change**: Password dapat diubah melalui database atau seeder
5. **Security**: Token disimpan dengan hash di database

## 🚨 Troubleshooting

### Common Issues:

1. **"Invalid credentials" error:**
   - Pastikan email dan password benar
   - Pastikan user sudah dibuat dengan seeder

2. **"Unauthenticated" error:**
   - Pastikan token valid dan tidak expired
   - Pastikan header Authorization sudah benar

3. **"Token not found" error:**
   - Token mungkin sudah di-delete atau expired
   - Login ulang untuk mendapatkan token baru

### Reset User:

Jika perlu reset user, jalankan:

```bash
php artisan migrate:fresh --seed
```

Ini akan menghapus semua data dan membuat user default baru.
