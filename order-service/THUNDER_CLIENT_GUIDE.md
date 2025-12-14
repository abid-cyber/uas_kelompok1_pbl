# 🧪 Thunder Client Testing Guide - Order Service

Panduan lengkap untuk testing Order Service menggunakan Thunder Client (VS Code Extension).

## 📋 Prerequisites

1. **Install Thunder Client** di VS Code
2. **Pastikan semua service berjalan:**
   - User Service: `http://localhost:8000`
   - Product Service: `http://localhost:8001`
   - Order Service: `http://localhost:8002`

## 🔐 Step 1: Get Authentication Token

Sebelum testing Order Service, kita perlu mendapatkan JWT token dari User Service.

### 1.1 Register User (jika belum ada)

**Request:**
```http
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

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    }
}
```

**Simpan token** dari response untuk digunakan di request berikutnya.

### 1.2 Login (Alternatif)

**Request:**
```http
POST http://localhost:8000/api/login
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
    }
}
```

---

## 📦 Step 2: Setup Product (jika belum ada)

Pastikan Product Service memiliki product untuk di-test. Jika Product Service belum memiliki endpoint create, gunakan mock data yang sudah ada di `ProductController.php`.

**Note:** Product Service saat ini menggunakan mock data:
- Product ID 1: Stock 100
- Product ID 2: Stock 50
- Product ID 3: Stock 25

---

## 🛒 Step 3: Testing Order Service

### 3.1 Create Order

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: test-correlation-123
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

**Headers di Thunder Client:**
- `Authorization`: `Bearer eyJ0eXAiOiJKV1QiLCJhbGc...` (token dari login/register)
- `X-Correlation-ID`: `test-correlation-123` (optional, akan di-generate otomatis jika tidak ada)
- `Content-Type`: `application/json`

**Expected Response (201 Created):**
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
    "correlation_id": "test-correlation-123"
}
```

### 3.2 Get All Orders

**Request:**
```http
GET http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: test-correlation-456
```

**Headers:**
- `Authorization`: `Bearer {YOUR_TOKEN_HERE}`
- `X-Correlation-ID`: `test-correlation-456` (optional)

**Expected Response (200 OK):**
```json
{
    "success": true,
    "message": "Orders retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "items": [...],
            "total": "100000.00",
            "status": "completed",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ],
    "correlation_id": "test-correlation-456"
}
```

### 3.3 Get Order by ID

**Request:**
```http
GET http://localhost:8002/api/orders/1
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: test-correlation-789
```

**Expected Response (200 OK):**
```json
{
    "success": true,
    "message": "Order retrieved successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "items": [...],
        "total": "100000.00",
        "status": "completed",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "correlation_id": "test-correlation-789"
}
```

---

## 🧪 Test Cases untuk Thunder Client

### Test Case 1: Create Order dengan Multiple Items

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: multi-item-test
Content-Type: application/json

{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "price": 50000
        },
        {
            "product_id": 2,
            "quantity": 1,
            "price": 75000
        }
    ],
    "total": 175000
}
```

### Test Case 2: Create Order tanpa Correlation ID (Auto-generate)

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
Content-Type: application/json

{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 1,
            "price": 50000
        }
    ],
    "total": 50000
}
```

**Note:** Correlation ID akan otomatis di-generate dan dikembalikan di response.

### Test Case 3: Validation Error - Missing Required Fields

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: validation-test
Content-Type: application/json

{
    "user_id": 1
}
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "items": ["The items field is required."],
        "total": ["The total field is required."]
    },
    "correlation_id": "validation-test"
}
```

### Test Case 4: Insufficient Stock

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: stock-test
Content-Type: application/json

{
    "user_id": 1,
    "items": [
        {
            "product_id": 1,
            "quantity": 1000,
            "price": 50000
        }
    ],
    "total": 50000000
}
```

**Expected Response (400 Bad Request):**
```json
{
    "success": false,
    "message": "Insufficient stock for product ID: 1",
    "correlation_id": "stock-test"
}
```

### Test Case 5: Unauthorized - Missing Token

**Request:**
```http
POST http://localhost:8002/api/orders
X-Correlation-ID: unauthorized-test
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

**Expected Response (401 Unauthorized):**
```json
{
    "success": false,
    "message": "Authorization token required",
    "correlation_id": "unauthorized-test"
}
```

### Test Case 6: Service Unavailable - User Service Down

**Scenario:** Matikan User Service, lalu coba create order.

**Request:**
```http
POST http://localhost:8002/api/orders
Authorization: Bearer {YOUR_TOKEN_HERE}
X-Correlation-ID: service-down-test
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

**Expected Response (503 Service Unavailable):**
```json
{
    "success": false,
    "message": "User service unavailable",
    "service": "User Service",
    "correlation_id": "service-down-test"
}
```

---

## 📝 Tips Thunder Client

### 1. Setup Environment Variables

Di Thunder Client, buat environment variables untuk memudahkan testing:

**Variables:**
- `base_url`: `http://localhost:8002`
- `user_service_url`: `http://localhost:8000`
- `product_service_url`: `http://localhost:8001`
- `token`: `{paste token setelah login}`
- `correlation_id`: `test-{{$timestamp}}`

**Usage:**
```http
POST {{base_url}}/api/orders
Authorization: Bearer {{token}}
X-Correlation-ID: {{correlation_id}}
```

### 2. Save Requests sebagai Collection

Buat collection di Thunder Client:
- **Order Service Tests**
  - Create Order
  - Get All Orders
  - Get Order by ID
  - Create Order (Multiple Items)
  - Validation Error Test
  - Insufficient Stock Test

### 3. Pre-request Script (Optional)

Untuk auto-generate Correlation ID:

```javascript
// Generate UUID untuk Correlation ID
const uuid = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
};

// Set header
tc.setVar("correlation_id", uuid());
```

### 4. Verify Correlation ID

Setelah setiap request, verifikasi:
- Correlation ID di request header sama dengan di response
- Correlation ID diteruskan ke service lain (cek log)

---

## 🔍 Debugging Tips

### 1. Check Logs

Cek log di setiap service untuk melihat Correlation ID:
```bash
# Order Service
tail -f order-service/storage/logs/laravel.log

# User Service  
tail -f user-service/storage/logs/laravel.log

# Product Service
tail -f product-service/storage/logs/laravel.log
```

### 2. Verify Inter-Service Calls

Pastikan Correlation ID diteruskan dengan benar:
- Order Service → User Service: Cek header `X-Correlation-ID`
- Order Service → Product Service: Cek header `X-Correlation-ID`

### 3. Common Issues

**Issue:** 401 Unauthorized
- **Solution:** Pastikan token valid dan tidak expired
- **Solution:** Pastikan format header: `Bearer {token}`

**Issue:** 503 Service Unavailable
- **Solution:** Pastikan User Service dan Product Service berjalan
- **Solution:** Cek URL di `.env` file

**Issue:** 400 Bad Request - Insufficient Stock
- **Solution:** Cek stock product di Product Service
- **Solution:** Gunakan quantity yang lebih kecil

---

## 📊 Expected Flow untuk Create Order

1. **Request masuk ke Order Service** dengan Correlation ID
2. **Order Service** memanggil User Service dengan Correlation ID yang sama
3. **Order Service** memanggil Product Service dengan Correlation ID yang sama
4. **Response** dikembalikan dengan Correlation ID yang sama

**Verification:**
- Semua log di setiap service harus memiliki Correlation ID yang sama
- Response header harus memiliki Correlation ID yang sama dengan request

---

## 🎯 Quick Test Checklist

- [ ] Get token dari User Service (register/login)
- [ ] Create order dengan valid data
- [ ] Create order tanpa Correlation ID (auto-generate)
- [ ] Get all orders
- [ ] Get order by ID
- [ ] Test validation error
- [ ] Test insufficient stock
- [ ] Test unauthorized (tanpa token)
- [ ] Verify Correlation ID di semua response

---

**Happy Testing! 🚀**

