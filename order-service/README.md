# Order Service - Inter-Service Communication

Microservice untuk manajemen order yang melakukan inter-service communication dengan User Service dan Product Service.

## 📋 Fitur

- ✅ **Create Order** - Membuat order baru dengan validasi dari User Service dan Product Service
- ✅ **List Orders** - Mengambil daftar semua orders
- ✅ **Get Order** - Mengambil detail order berdasarkan ID
- ✅ **Inter-Service Communication** - Call ke User Service dan Product Service
- ✅ **Correlation ID** - Middleware untuk tracking request across services
- ✅ **Authorization Token Forwarding** - Meneruskan token ke service lain
- ✅ **Error Handling** - Error handling konsisten untuk kegagalan service lain
- ✅ **Unit Tests** - Feature tests dengan mock HTTP calls

## 🚀 Setup

### 1. Install Dependencies

```bash
composer install
composer require guzzlehttp/guzzle
```

### 2. Setup Environment

Buat file `.env` dari template (atau copy dari `.env.example` jika ada):

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file dan konfigurasi:

```env
APP_NAME="Order Service"
APP_URL=http://localhost:8002

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_service
DB_USERNAME=root
DB_PASSWORD=

# Service URLs for inter-service communication
USER_SERVICE_URL=http://localhost:8000
PRODUCT_SERVICE_URL=http://localhost:8001

# Docker service URLs (uncomment if using Docker)
# USER_SERVICE_URL=http://user-service-nginx:80
# PRODUCT_SERVICE_URL=http://product-service-nginx:80
```

### 3. Setup Database

```bash
php artisan migrate
```

### 4. Run Server

```bash
php artisan serve --port=8002
```

## 🐳 Docker Setup

### Prerequisites
- Docker Desktop harus **berjalan**

### Using Docker Compose

```bash
docker-compose up -d
```

Service akan berjalan di:
- **API**: http://localhost:8002
- **Database**: localhost:3307

### Stop Docker

```bash
docker-compose down
```

## 📡 API Endpoints

### Create Order

```http
POST /api/orders
Authorization: Bearer {token}
X-Correlation-ID: {correlation_id}
Content-Type: application/json

{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 50000
        }
    ],
    "total": 100000
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "items": [
            {
                "product_id": 1,
                "quantity": 2,
                "price": 50000
            }
        ],
        "total": "100000.00",
        "status": "completed",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

### Get All Orders

```http
GET /api/orders
Authorization: Bearer {token}
X-Correlation-ID: {correlation_id}
```

### Get Order by ID

```http
GET /api/orders/{id}
Authorization: Bearer {token}
X-Correlation-ID: {correlation_id}
```

## 🔄 Inter-Service Communication Flow

### Create Order Flow:

1. **Validate Token** - Call `GET /api/user/profile` ke User Service
2. **Validate User** - Call `GET /api/users/{user_id}` ke User Service
3. **Validate Products** - Call `GET /api/products/{product_id}` ke Product Service untuk setiap item
4. **Check Stock** - Validasi stock tersedia untuk setiap product
5. **Create Order** - Simpan order ke database
6. **Update Stock** - Call `PUT /api/products/{product_id}/stock` ke Product Service untuk mengurangi stock

## 🔗 Correlation ID

Correlation ID digunakan untuk tracking request across multiple services. Middleware otomatis:
- Generate Correlation ID jika tidak disediakan di header
- Meneruskan Correlation ID ke service lain
- Menambahkan Correlation ID ke response
- Menambahkan Correlation ID ke log context

**Usage:**
```http
X-Correlation-ID: 550e8400-e29b-41d4-a716-446655440000
```

## 🛡️ Error Handling

### Service Unavailable

Jika User Service atau Product Service tidak tersedia:

```json
{
    "success": false,
    "message": "Service temporarily unavailable",
    "service": "User Service",
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

Status Code: `503 Service Unavailable`

### Validation Errors

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "user_id": ["The user id field is required."],
        "items": ["The items field is required."]
    },
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

Status Code: `422 Unprocessable Entity`

### Insufficient Stock

```json
{
    "success": false,
    "message": "Insufficient stock for product ID: 1",
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000"
}
```

Status Code: `400 Bad Request`

## 🧪 Testing

### Unit Tests

Run all tests:
```bash
php artisan test
```

Run specific test:
```bash
php artisan test --filter OrderServiceTest
```

### Manual Testing dengan Thunder Client

Untuk testing manual menggunakan Thunder Client (VS Code Extension), lihat panduan lengkap di:
📖 **[THUNDER_CLIENT_GUIDE.md](./THUNDER_CLIENT_GUIDE.md)**

**Quick Start:**
1. Install Thunder Client extension di VS Code
2. Pastikan semua service berjalan (User, Product, Order)
3. Get token dari User Service (register/login)
4. Test endpoint Order Service dengan token dan Correlation ID

### Test Coverage

- ✅ Create order dengan inter-service calls
- ✅ Order creation fails ketika user service unavailable
- ✅ Order creation fails ketika product service unavailable
- ✅ Order creation fails ketika stock insufficient
- ✅ Validation errors
- ✅ Correlation ID generated jika tidak disediakan

## 📁 Struktur Project

```
order-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── OrderController.php
│   │   ├── Middleware/
│   │   │   └── CorrelationIdMiddleware.php
│   │   └── Services/
│   │       ├── UserServiceClient.php
│   │       └── ProductServiceClient.php
│   ├── Models/
│   │   └── Order.php
│   └── Exceptions/
│       ├── ServiceUnavailableException.php
│       └── ApiExceptionHandler.php
├── routes/
│   └── api.php
├── database/migrations/
│   └── 2024_01_01_000003_create_orders_table.php
├── tests/Feature/
│   └── OrderServiceTest.php
├── docker-compose.yml
├── Dockerfile
└── docker/
    ├── nginx/
    │   └── default.conf
    └── php/
        └── local.ini
```

## 📦 Dependencies

- **Laravel 12**
- **guzzlehttp/guzzle** - HTTP client untuk inter-service communication
- **PHP 8.2+**

## 🔧 Configuration

### Service URLs

Konfigurasi URL service lain di `config/services.php` atau `.env`:

```env
USER_SERVICE_URL=http://localhost:8000
PRODUCT_SERVICE_URL=http://localhost:8001
```

### Timeout Settings

Default timeout untuk HTTP client:
- Connection timeout: 5 seconds
- Request timeout: 10 seconds

Dapat diubah di `UserServiceClient.php` dan `ProductServiceClient.php`.

## 📝 Notes

- Order Service memerlukan User Service dan Product Service untuk berfungsi
- Pastikan semua service berjalan sebelum testing
- Correlation ID otomatis di-generate jika tidak disediakan
- Authorization token harus valid dari User Service
