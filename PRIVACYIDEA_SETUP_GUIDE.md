# Panduan Setup PrivacyIDEA Webhook untuk POLRI Backend

## Overview
Dokumen ini menjelaskan cara mengkonfigurasi PrivacyIDEA v3.11.4 untuk mengirim data login ke POLRI Backend melalui webhook.

## Endpoint Webhook
```
POST {{base_url}}/api/webhook/privacyidea
```

## Field yang Diterima
Berikut adalah field-field yang dapat diterima dari PrivacyIDEA:

### Field Wajib
- `username` (string) - Nama pengguna
- `success` (boolean) - Status keberhasilan login

### Field Opsional
- `client_ip` (string) - IP address client (PrivacyIDEA menggunakan `client_ip` bukan `ip_address`)
- `realm` (string) - Realm pengguna
- `resolver` (string) - Resolver yang digunakan
- `token_type` (string) - Jenis token (totp, hotp, push, dll)
- `serial` (string) - Serial number token
- `action` (string) - Aksi yang dilakukan (authenticate, enroll, dll)

## Konfigurasi PrivacyIDEA

### 1. Aktifkan Webhook di PrivacyIDEA
Edit file konfigurasi PrivacyIDEA (`/etc/privacyidea/pi.cfg`):

```ini
[webhook]
enabled = true
url = http://your-polri-backend.com/api/webhook/privacyidea
method = POST
headers = {"Content-Type": "application/json"}
```

### 2. Konfigurasi Event Webhook
Tambahkan konfigurasi berikut untuk mengirim data pada event tertentu:

```ini
[webhook_events]
# Kirim data pada setiap autentikasi
auth_success = true
auth_fail = true

# Kirim data pada event token enrollment
token_enroll = true
token_unassign = true
```

### 3. Format Data yang Dikirim
PrivacyIDEA akan mengirim data dalam format JSON seperti berikut:

**Login Berhasil:**
```json
{
  "username": "john.doe",
  "success": true,
  "client_ip": "192.168.1.100",
  "realm": "polri.local",
  "resolver": "useridresolver",
  "token_type": "totp",
  "serial": "PI123456789",
  "action": "authenticate"
}
```

**Login Gagal:**
```json
{
  "username": "john.doe",
  "success": false,
  "client_ip": "192.168.1.100",
  "realm": "polri.local",
  "token_type": "totp",
  "action": "authenticate"
}
```

## Testing Webhook

### 1. Menggunakan Postman
- Import collection `POLRI_Backend_API.postman_collection.json`
- Gunakan endpoint "PrivacyIDEA Webhook" untuk testing
- Pastikan environment variable `base_url` sudah diset

### 2. Menggunakan cURL
```bash
# Test login berhasil
curl -X POST http://localhost:8000/api/webhook/privacyidea \
  -H "Content-Type: application/json" \
  -d '{
    "username": "test.user",
    "success": true,
    "client_ip": "192.168.1.100",
    "realm": "polri.local",
    "token_type": "totp",
    "action": "authenticate"
  }'

# Test login gagal
curl -X POST http://localhost:8000/api/webhook/privacyidea \
  -H "Content-Type: application/json" \
  -d '{
    "username": "test.user",
    "success": false,
    "client_ip": "192.168.1.100",
    "realm": "polri.local",
    "token_type": "totp",
    "action": "authenticate"
  }'
```

## Troubleshooting

### 1. Webhook tidak terkirim
- Periksa konfigurasi PrivacyIDEA
- Pastikan endpoint dapat diakses dari server PrivacyIDEA
- Cek log PrivacyIDEA untuk error

### 2. Data tidak tersimpan
- Periksa log Laravel (`storage/logs/laravel.log`)
- Pastikan semua field wajib terisi
- Cek koneksi database

### 3. Field tidak sesuai
- PrivacyIDEA menggunakan `client_ip` bukan `ip_address`
- Field `realm`, `resolver`, dll bersifat opsional
- Semua data tambahan akan disimpan di `raw_payload`

## Monitoring dan Logging

### 1. Log Laravel
Semua request webhook akan di-log di:
```
storage/logs/laravel.log
```

### 2. Database
Data login tersimpan di tabel `login_logs` dengan field tambahan:
- `realm` - Realm pengguna
- `resolver` - Resolver yang digunakan  
- `token_type` - Jenis token
- `serial` - Serial number token
- `action` - Aksi yang dilakukan

### 3. API Statistics
Gunakan endpoint statistics untuk monitoring:
- `/api/stats/active-users` - User aktif
- `/api/stats/success-logins` - Login berhasil
- `/api/stats/failed-logins` - Login gagal

## Keamanan

### 1. Rate Limiting
Webhook endpoint dibatasi 30 request per menit untuk mencegah abuse.

### 2. Validasi Data
Semua data yang masuk akan divalidasi sesuai schema yang ditentukan.

### 3. Logging
Semua request dan error akan di-log untuk audit trail.

## Support
Untuk bantuan teknis atau pertanyaan, silakan hubungi tim development POLRI Backend.
