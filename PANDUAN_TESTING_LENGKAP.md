# 📋 Panduan Testing Lengkap - UAS Arsitektur Layanan

## 🎯 Overview

Panduan ini menjelaskan cara melakukan testing untuk semua service (User Service, Product Service, dan Order Service) dengan berbagai metode.

---

## 📦 1. Unit Test (Automated Testing)

### Setup

Pastikan semua service sudah di-setup dengan benar:

```bash
# User Service
cd user-service
composer install
php artisan migrate
php artisan test

# Product Service
cd product-service
composer install
php artisan migrate
php artisan test

# Order Service
cd order-service
composer install
php artisan migrate
php artisan test
```

### Menjalankan Unit Test

#### User Service
```bash
cd user-service
php artisan test
```

**Tests yang akan dijalankan:**
- ✅ `test_user_can_register`
- ✅ `test_user_registration_validation`
- ✅ `test_user_can_login`
- ✅ `test_user_login_with_invalid_credentials`
- ✅ `test_user_can_get_profile`
- ✅ `test_user_profile_requires_authentication`

#### Product Service
```bash
cd product-service
php artisan test
```

**Tests yang akan dijalankan:**
- ✅ `test_can_create_product`
- ✅ `test_can_get_product_by_id`
- ✅ `test_can_get_list_of_products`
- ✅ `test_can_update_product`
- ✅ `test_can_delete_product`
- ✅ `test_validation_fails_for_invalid_data`
- ✅ `test_returns_404_for_nonexistent_product`

#### Order Service
```bash
cd order-service
php artisan test
```

**Tests yang akan dijalankan:**
- ✅ `test_can_create_order_with_inter_service_calls`
- ✅ `test_order_creation_fails_when_user_service_unavailable`
- ✅ `test_order_creation_fails_when_product_service_unavailable`
- ✅ `test_order_creation_fails_when_stock_insufficient`
- ✅ `test_order_creation_validates_request`
- ✅ `test_correlation_id_is_generated_if_not_provided`

### Menjalankan Test Spesifik

```bash
# Test spesifik di User Service
php artisan test --filter test_user_can_register

# Test spesifik di Product Service
php artisan test --filter test_can_create_product

# Test spesifik di Order Service
php artisan test --filter test_can_create_order_with_inter_service_calls
```

---

## 🚀 2. Manual Testing dengan API Client

### Setup Service

Jalankan semua service di terminal terpisah:

#### Terminal 1 - User Service
```bash
cd user-service
php artisan serve --port=8000
```

#### Terminal 2 - Product Service
```bash
cd product-service
php artisan serve --port=8001
```

#### Terminal 3 - Order Service
```bash
cd order-service
php artisan serve --port=8002
```

### Tools untuk Testing

Anda bisa menggunakan:
- **Postman** (https://www.postman.com/)
- **Thunder Client** (VS Code Extension)
- **cURL** (Command Line)
- **Insomnia** (https://insomnia.rest/)

---

## 📝 3. Testing User Service

### 3.1. Register User

**Request:**
```http
POST http://localhost:8000/api/register
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

**Expected Response (201):**
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

**Simpan token** untuk testing selanjutnya!

### 3.2. Login

**Request:**
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Expected Response (200):**
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

### 3.3. Get User Profile

**Request:**
```http
GET http://localhost:8000/api/user/profile
Authorization: Bearer {token}
X-Correlation-ID: test-correlation-id-123
```

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "081234567890",
        "address": "Jl. Contoh No. 123",
        "role": "pembeli"
    }
}
```

**Perhatikan:** Response header akan memiliki `X-Correlation-ID: test-correlation-id-123`

### 3.4. Get All Users (Admin Only)

**Request:**
```http
GET http://localhost:8000/api/users
Authorization: Bearer {admin_token}
X-Correlation-ID: test-correlation-id-123
```

### 3.5. Get User by ID

**Request:**
```http
GET http://localhost:8000/api/users/1
Authorization: Bearer {token}
X-Correlation-ID: test-correlation-id-123
```

### 3.6. Update User

**Request:**
```http
PUT http://localhost:8000/api/users/1
Authorization: Bearer {token}
X-Correlation-ID: test-correlation-id-123
Content-Type: application/json

{
    "name": "John Updated",
    "phone": "081234567891"
}
```

### 3.7. Delete User (Admin Only)

**Request:**
```http
DELETE http://localhost:8000/api/users/2
Authorization: Bearer {admin_token}
X-Correlation-ID: test-correlation-id-123
```

---

## 📦 4. Testing Product Service

### 4.1. Create Product

**Request:**
```http
POST http://localhost:8001/api/products
Content-Type: application/json
X-Correlation-ID: test-correlation-id-123

{
    "name": "Kain Katun",
    "description": "Kain katun berkualitas tinggi",
    "price": 50000,
    "stock": 100,
    "category_id": 1,
    "supplier_id": 1
}
```

**Note:** Pastikan `category_id` dan `supplier_id` sudah ada di database.

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Produk berhasil dibuat",
    "data": {
        "id": 1,
        "name": "Kain Katun",
        "description": "Kain katun berkualitas tinggi",
        "price": "50000.00",
        "stock": 100,
        "category_id": 1,
        "supplier_id": 1,
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-01-15T10:00:00.000000Z"
    }
}
```

### 4.2. Get All Products

**Request:**
```http
GET http://localhost:8001/api/products?page=1&per_page=10&search=katun
X-Correlation-ID: test-correlation-id-123
```

### 4.3. Get Product by ID

**Request:**
```http
GET http://localhost:8001/api/products/1
X-Correlation-ID: test-correlation-id-123
```

### 4.4. Update Product

**Request:**
```http
PUT http://localhost:8001/api/products/1
Content-Type: application/json
X-Correlation-ID: test-correlation-id-123

{
    "name": "Kain Katun Premium",
    "price": 60000,
    "stock": 150
}
```

### 4.5. Delete Product

**Request:**
```http
DELETE http://localhost:8001/api/products/1
X-Correlation-ID: test-correlation-id-123
```

### 4.6. Update Stock (untuk Order Service)

**Request:**
```http
PUT http://localhost:8001/api/products/1/stock
Content-Type: application/json
X-Correlation-ID: test-correlation-id-123

{
    "quantity": -2
}
```

---

## 🛒 5. Testing Order Service (Inter-Service Communication)

### 5.1. Create Order (PENTING: Test Inter-Service)

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {token_dari_user_service}
X-Correlation-ID: test-correlation-id-123
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

**Flow yang terjadi:**
1. Order Service → User Service: Validate token
2. Order Service → User Service: Validate user_id
3. Order Service → Product Service: Validate product
4. Order Service → Product Service: Check stock
5. Order Service: Create order
6. Order Service → Product Service: Update stock

**Expected Response (201):**
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
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-01-15T10:00:00.000000Z"
    },
    "correlation_id": "test-correlation-id-123"
}
```

**Perhatikan:** 
- Response memiliki `correlation_id` yang sama dengan request
- Semua service calls menggunakan correlation_id yang sama

### 5.2. Get All Orders

**Request:**
```http
GET http://localhost:8002/api/orders
Authorization: Bearer {token}
X-Correlation-ID: test-correlation-id-123
```

### 5.3. Get Order by ID

**Request:**
```http
GET http://localhost:8002/api/orders/1
Authorization: Bearer {token}
X-Correlation-ID: test-correlation-id-123
```

---

## 🔍 6. Testing Distributed Tracing (Correlation ID)

### 6.1. Test Correlation ID Propagation

1. **Buat request ke Order Service dengan Correlation ID:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {token}
X-Correlation-ID: tracing-test-123
Content-Type: application/json

{
    "user_id": 1,
    "items": [{"product_id": 1, "quantity": 1, "price": 50000}],
    "total": 50000
}
```

2. **Cek log di semua service:**

**User Service Log:**
```bash
cd user-service
tail -f storage/logs/laravel.log | grep "tracing-test-123"
```

**Product Service Log:**
```bash
cd product-service
tail -f storage/logs/laravel.log | grep "tracing-test-123"
```

**Order Service Log:**
```bash
cd order-service
tail -f storage/logs/laravel.log | grep "tracing-test-123"
```

3. **Verifikasi:** Semua log harus memiliki correlation_id yang sama: `tracing-test-123`

### 6.2. Test Auto-Generate Correlation ID

**Request tanpa Correlation ID:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "user_id": 1,
    "items": [{"product_id": 1, "quantity": 1, "price": 50000}],
    "total": 50000
}
```

**Expected:** Response akan memiliki `correlation_id` yang auto-generated (UUID format)

---

## 🧪 7. Testing Error Handling

### 7.1. Test Validation Error

**Request dengan data invalid:**
```http
POST http://localhost:8000/api/register
Content-Type: application/json

{
    "name": "",
    "email": "invalid-email",
    "password": "123"
}
```

**Expected Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["Nama wajib diisi."],
        "email": ["Format email tidak valid."],
        "password": ["Password minimal 8 karakter."]
    }
}
```

### 7.2. Test Service Unavailable (Order Service)

**Test ketika User Service down:**
1. Stop User Service
2. Buat request ke Order Service
3. Expected: Error response dengan format konsisten

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {token}
X-Correlation-ID: test-error-123
Content-Type: application/json

{
    "user_id": 1,
    "items": [{"product_id": 1, "quantity": 1, "price": 50000}],
    "total": 50000
}
```

**Expected Response (503):**
```json
{
    "success": false,
    "message": "User service unavailable",
    "service": "User Service",
    "correlation_id": "test-error-123"
}
```

### 7.3. Test Insufficient Stock

**Request dengan quantity lebih besar dari stock:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {token}
X-Correlation-ID: test-stock-123
Content-Type: application/json

{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 1000,  // Stock hanya 100
            "price": 50000
        }
    ],
    "total": 50000000
}
```

**Expected Response (400):**
```json
{
    "success": false,
    "message": "Insufficient stock for product ID: 1",
    "correlation_id": "test-stock-123"
}
```

---

## 📊 8. Testing dengan cURL (Command Line)

### 8.1. Register User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567890",
    "address": "Jl. Contoh No. 123",
    "role": "pembeli"
  }'
```

### 8.2. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 8.3. Get Profile (dengan token)
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."  # Ganti dengan token dari login
CORRELATION_ID="test-123"

curl -X GET http://localhost:8000/api/user/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Correlation-ID: $CORRELATION_ID"
```

### 8.4. Create Order
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGc..."  # Ganti dengan token
CORRELATION_ID="test-123"

curl -X POST http://localhost:8002/api/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Correlation-ID: $CORRELATION_ID" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "price": 50000
      }
    ],
    "total": 100000
  }'
```

---

## ✅ 9. Checklist Testing

### User Service
- [ ] Register user baru
- [ ] Register dengan data invalid (validation error)
- [ ] Login dengan credentials valid
- [ ] Login dengan credentials invalid
- [ ] Get profile dengan token valid
- [ ] Get profile tanpa token (401)
- [ ] Get all users (admin only)
- [ ] Get user by ID
- [ ] Update user
- [ ] Delete user (admin only)
- [ ] Correlation ID di response header

### Product Service
- [ ] Create product
- [ ] Create product dengan data invalid
- [ ] Get all products
- [ ] Get all products dengan search
- [ ] Get product by ID
- [ ] Get product yang tidak ada (404)
- [ ] Update product
- [ ] Delete product
- [ ] Update stock
- [ ] Correlation ID di response header

### Order Service
- [ ] Create order dengan correlation ID
- [ ] Create order tanpa correlation ID (auto-generate)
- [ ] Create order dengan token valid
- [ ] Create order tanpa token (401)
- [ ] Create order ketika User Service down (503)
- [ ] Create order ketika Product Service down (503)
- [ ] Create order dengan stock tidak cukup (400)
- [ ] Get all orders
- [ ] Get order by ID
- [ ] Correlation ID propagation ke service lain
- [ ] Authorization token forwarding ke User Service

### Distributed Tracing
- [ ] Correlation ID sama di semua service logs
- [ ] Correlation ID di response header
- [ ] Correlation ID auto-generate jika tidak ada
- [ ] Log context dengan correlation_id
- [ ] Proof of distributed tracing (cuplikan log)

---

## 📸 10. Screenshot untuk Proof

### Yang Perlu Di-screenshot:

1. **Unit Test Results:**
   - Screenshot hasil `php artisan test` di semua service

2. **API Response dengan Correlation ID:**
   - Screenshot response dari Order Service yang menunjukkan correlation_id
   - Screenshot response header yang menunjukkan X-Correlation-ID

3. **Log dari 3 Service dengan Correlation ID Sama:**
   - Screenshot log User Service
   - Screenshot log Product Service
   - Screenshot log Order Service
   - Semua dengan correlation_id yang sama

4. **Error Handling:**
   - Screenshot error response ketika service lain down
   - Screenshot validation error

---

## 🎯 Quick Test Script

Buat file `test-all.sh` untuk testing cepat:

```bash
#!/bin/bash

echo "=== Testing User Service ==="
cd user-service
php artisan test

echo "=== Testing Product Service ==="
cd ../product-service
php artisan test

echo "=== Testing Order Service ==="
cd ../order-service
php artisan test

echo "=== All tests completed ==="
```

Jalankan dengan:
```bash
chmod +x test-all.sh
./test-all.sh
```

---

## 📝 Catatan Penting

1. **Pastikan semua service berjalan** sebelum testing
2. **Database sudah di-migrate** di semua service
3. **Token dari User Service** digunakan untuk testing Order Service
4. **Product ID dan User ID** harus valid di database
5. **Correlation ID** harus sama di semua service logs untuk distributed tracing

---

**Selamat Testing! 🚀**
