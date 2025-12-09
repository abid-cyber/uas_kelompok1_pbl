# User Service - Authentication & User Management

Microservice untuk autentikasi dan manajemen user dengan JWT authentication.

## 📋 Fitur

- ✅ **Register** - Pendaftaran user baru
- ✅ **Login** - Autentikasi dengan JWT
- ✅ **User Profile** - Mengambil profil user yang sedang login
- ✅ **User CRUD** - Create, Read, Update, Delete user (admin only)
- ✅ **Validasi Input** - Request validation untuk semua endpoint
- ✅ **Error Handling** - Custom exception handler dengan format response konsisten
- ✅ **Unit Tests** - Feature tests untuk authentication dan user CRUD

## 🚀 Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 3. Setup Database

Edit `.env` file dan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=userr_service
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Run Tests

```bash
php artisan test
```

## 🐳 Docker Setup (Optional)

### Prerequisites
- Docker Desktop harus **berjalan** sebelum menjalankan docker-compose
- Pastikan Docker Desktop sudah di-start dari Start Menu atau System Tray

### Using Docker Compose

```bash
# Pastikan Docker Desktop berjalan terlebih dahulu
docker-compose up -d
```

Service akan berjalan di:
- **API**: http://localhost:8000+
- **Database**: localhost:3306

### Troubleshooting Docker

Jika error: `unable to connect to docker engine`:
1. Buka **Docker Desktop** dari Start Menu
2. Tunggu sampai status "Docker Desktop is running"
3. Coba jalankan `docker ps` untuk verifikasi
4. Jika masih error, restart Docker Desktop

**Catatan**: Karena Anda sudah menggunakan Laragon untuk database, Docker tidak wajib. Anda bisa langsung menggunakan Laragon MySQL dan menjalankan aplikasi dengan `php artisan serve`.

## 📡 API Endpoints

### Public Endpoints

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890",
    "address": "Jl. Contoh No. 123",
    "role": "pembeli"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    }
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "081234567890",
            "address": "Jl. Contoh No. 123",
            "role": "pembeli"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    }
}
```

### Protected Endpoints (Require JWT Token)

#### Get User Profile
```http
GET /api/user/profile
Authorization: Bearer {token}
X-Correlation-ID: {correlation_id}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Get All Users (Admin Only)
```http
GET /api/users
Authorization: Bearer {token}
```

#### Get User by ID
```http
GET /api/users/{id}
Authorization: Bearer {token}
```

#### Update User
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Name",
    "phone": "081234567890"
}
```

#### Delete User (Admin Only)
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

## 🔐 Roles

- **admin** - Full access (can view, update, delete all users)
- **pembeli** - Can view and update own profile
- **kasir** - Can view and update own profile

## 📝 Validasi

### Register
- `name`: required, string, max:255
- `email`: required, email format, unique
- `password`: required, min:8, confirmed
- `phone`: nullable, string, max:20
- `address`: nullable, string
- `role`: required, in:['admin','pembeli','kasir']

### Login
- `email`: required, email format
- `password`: required, string

### Update User
- `name`: sometimes, string, max:255
- `email`: sometimes, email format, unique (except current user)
- `password`: sometimes, min:8, confirmed
- `phone`: nullable, string, max:20
- `address`: nullable, string
- `role`: sometimes, in:['admin','pembeli','kasir'] (admin only)

## 🧪 Testing

Run all tests:
```bash
php artisan test
```

Run specific test:
```bash
php artisan test --filter AuthTest
php artisan test --filter UserCrudTest
```

## 📁 Struktur Project

```
user-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/
│   │   │   ├── JwtAuth.php
│   │   │   └── CorrelationIdMiddleware.php
│   │   └── Requests/
│   │       ├── RegisterRequest.php
│   │       ├── LoginRequest.php
│   │       └── UpdateUserRequest.php
│   ├── Models/
│   │   └── User.php
│   └── Exceptions/
│       └── ApiExceptionHandler.php
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
│       ├── AuthTest.php
│       └── UserCrudTest.php
├── docker-compose.yml
├── Dockerfile
└── .env.example
```

## 🔧 Error Response Format

### Success Response
```json
{
    "success": true,
    "message": "...",
    "data": {...}
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {...}  // untuk validation errors
}
```

## 📦 Dependencies

- **Laravel 12**
- **tymon/jwt-auth** - JWT authentication
- **PHP 8.2+**

## 🧪 Testing API

Untuk panduan lengkap testing API dengan Thunder Client, Postman, atau cURL, lihat:
- **[API_TESTING_GUIDE.md](API_TESTING_GUIDE.md)** - Panduan lengkap semua endpoint
- **[THUNDER_CLIENT_GUIDE.md](THUNDER_CLIENT_GUIDE.md)** - ⚡ Panduan step-by-step Thunder Client

### Quick Test dengan Thunder Client (VS Code)

1. Install extension "Thunder Client" di VS Code
2. Buka Thunder Client (ikon ⚡ di sidebar)
3. Test endpoint:
   - **Register:** `POST http://localhost:8000/api/register`
   - **Login:** `POST http://localhost:8000/api/login`
   - **Profile:** `GET http://localhost:8000/api/user/profile` (butuh token)

### Quick Test dengan cURL

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123","role":"pembeli"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'
```

## 👥 Author

Anggota 1 - User Service (Autentikasi)
