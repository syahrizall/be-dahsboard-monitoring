# 📋 Panduan Setup Postman Collection - POLRI Backend API

## 🚀 Cara Import Collection

### 1. Import Collection
1. Buka Postman
2. Klik **Import** button
3. Pilih file `POLRI_Backend_API.postman_collection.json`
4. Klik **Import**

### 2. Import Environment
1. Klik **Import** button lagi
2. Pilih file `POLRI_Backend_Environment.postman_environment.json`
3. Klik **Import**
4. Pilih environment **"POLRI Backend Environment"** di dropdown

## 📁 Struktur Collection

### 🔗 **Webhook Endpoints**
- **POST Webhook - Success Login**: Test login berhasil
- **POST Webhook - Failed Login**: Test login gagal
- **POST Webhook - Minimal Data**: Test data minimal
- **POST Webhook - Validation Error (Empty)**: Test validasi data kosong
- **POST Webhook - Validation Error (Wrong Type)**: Test validasi tipe data

### 📊 **Statistics Endpoints**
- **GET Active Users**: Pengguna aktif (15 menit terakhir)
- **GET Unique Users Count**: Jumlah pengguna unik
- **GET List Unique Users**: Daftar pengguna unik
- **GET Last Login**: Data login terakhir per pengguna
- **GET Success Logins Count**: Jumlah login berhasil
- **GET Failed Logins Count**: Jumlah login gagal
- **GET Logins by Date - Valid Range**: Statistik per tanggal
- **GET Logins by Date - Validation Error**: Test validasi tanggal

### 🏥 **Health Check**
- **GET Health Check**: Cek status aplikasi

## ⚙️ Environment Variables

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | `http://localhost:8000` | Base URL aplikasi |
| `api_version` | `v1` | Versi API |
| `content_type` | `application/json` | Content type default |

## 🔧 Cara Menggunakan

### 1. Setup Environment
```bash
# Pastikan server Laravel berjalan
php artisan serve
```

### 2. Test Webhook
1. Pilih **"POST Webhook - Success Login"**
2. Klik **Send**
3. Pastikan response status **201 Created**

### 3. Test Statistics
1. Pilih **"GET Active Users"**
2. Klik **Send**
3. Pastikan response status **200 OK**

### 4. Run Collection
1. Klik kanan pada collection
2. Pilih **"Run collection"**
3. Pilih environment
4. Klik **Run POLRI Backend API**

## 📝 Contoh Request Body

### Webhook Success Login
```json
{
    "username": "john.doe",
    "success": true,
    "ip_address": "192.168.1.100"
}
```

### Webhook Failed Login
```json
{
    "username": "john.doe",
    "success": false,
    "ip_address": "192.168.1.100"
}
```

### Webhook Minimal Data
```json
{
    "username": "jane.smith",
    "success": true
}
```

## 📊 Expected Responses

### ✅ Success Response (201)
```json
{
    "status": "success",
    "message": "Login log created successfully",
    "data": {
        "id": 1,
        "username": "john.doe",
        "ip_address": "192.168.1.100",
        "success": true,
        "raw_payload": {...},
        "created_at": "2025-08-08T00:30:00.000000Z",
        "updated_at": "2025-08-08T00:30:00.000000Z"
    }
}
```

### ❌ Error Response (422)
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "username": ["The username field is required."],
        "success": ["The success field is required."]
    }
}
```

## 🧪 Automated Tests

Setiap request memiliki automated tests yang akan:
- ✅ Memverifikasi status code
- ✅ Memverifikasi response structure
- ✅ Memverifikasi data types
- ✅ Memverifikasi required fields

## 🔒 Rate Limiting

- **Webhook**: 30 requests per minute
- **Statistics**: 60 requests per minute

Jika melebihi limit, akan mendapat response **429 Too Many Requests**.

## 🛠️ Troubleshooting

### Error 422 (Unprocessable Content)
- Pastikan `username` adalah string
- Pastikan `success` adalah boolean (true/false)
- Pastikan Content-Type: application/json

### Error 429 (Too Many Requests)
- Tunggu 1 menit sebelum request berikutnya
- Atau gunakan endpoint yang berbeda

### Error 500 (Internal Server Error)
- Cek apakah server Laravel berjalan
- Cek log di `storage/logs/laravel.log`

## 📱 Mobile/Desktop Testing

### Android (Postman Mobile)
1. Install Postman Mobile
2. Import collection via QR code
3. Scan QR code dari Postman Desktop

### iOS (Postman Mobile)
1. Install Postman Mobile
2. Import collection via QR code
3. Scan QR code dari Postman Desktop

## 🔄 Continuous Integration

Collection ini bisa digunakan untuk:
- **Automated Testing**
- **API Documentation**
- **Team Collaboration**
- **Performance Testing**

## 📞 Support

Jika ada masalah dengan collection:
1. Cek environment variables
2. Pastikan server berjalan
3. Cek network connectivity
4. Review error logs
