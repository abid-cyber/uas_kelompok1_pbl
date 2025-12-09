# 🚀 Quick Start Guide

## Opsi 1: Menggunakan Laragon (Recommended - Paling Mudah)

Karena Anda sudah menggunakan Laragon untuk database, ini adalah cara termudah:

### 1. Pastikan Laragon MySQL Running
- Buka Laragon
- Pastikan MySQL service sudah running (hijau)

### 2. Setup Environment
```bash
# Jika belum, copy .env.example ke .env
cp .env.example .env

# Generate key (jika belum)
php artisan key:generate
php artisan jwt:secret
```

### 3. Pastikan .env sudah benar
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=userr_service
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migration (jika belum)
```bash
php artisan migrate
```

### 5. Start Server
```bash
php artisan serve
```

### 6. Test API
Akses: **http://localhost:8000**

**Contoh Test dengan curl atau Postman:**
```bash
# Register
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "pembeli"
}
```

---

## Opsi 2: Menggunakan Docker

### Prerequisites
- Docker Desktop harus **berjalan**

### Start Docker Desktop
1. Buka **Docker Desktop** dari Start Menu
2. Tunggu sampai ikon whale di system tray menunjukkan "Docker Desktop is running"
3. Verifikasi dengan: `docker ps`

### Run Docker Compose
```bash
# Gunakan script helper
.\start-docker.ps1

# Atau langsung
docker-compose up -d
```

### Access
- **API**: http://localhost:8000
- **Database**: localhost:3306

### Stop Docker
```bash
docker-compose down
```

---

## 🧪 Run Tests

```bash
php artisan test
```

---

## 📝 Notes

- **Laragon**: Tidak perlu Docker, langsung pakai `php artisan serve`
- **Docker**: Perlu Docker Desktop running, semua service dalam container
- **Database**: Laragon MySQL sudah cukup untuk development

**Rekomendasi**: Gunakan Laragon untuk development, lebih cepat dan mudah! 🎯

