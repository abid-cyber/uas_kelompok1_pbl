# ⚡ Quick Test Guide - Testing Cepat

## 🚀 Cara Cepat Testing

### 1. Unit Test (Automated)

```bash
# Test semua service sekaligus
cd user-service && php artisan test && cd ..
cd product-service && php artisan test && cd ..
cd order-service && php artisan test && cd ..
```

### 2. Manual Testing dengan Postman/Thunder Client

#### Step 1: Jalankan Semua Service

**Terminal 1:**
```bash
cd user-service
php artisan serve --port=8000
```

**Terminal 2:**
```bash
cd product-service
php artisan serve --port=8001
```

**Terminal 3:**
```bash
cd order-service
php artisan serve --port=8002
```

#### Step 2: Test Flow Lengkap

**1. Register User:**
```
POST http://localhost:8000/api/register
Body: {
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "pembeli"
}
```
**Simpan token dari response!**

**2. Create Product:**
```
POST http://localhost:8001/api/products
Body: {
  "name": "Test Product",
  "price": 50000,
  "stock": 100,
  "category_id": 1,
  "supplier_id": 1
}
```
**Simpan product_id dari response!**

**3. Create Order (Test Inter-Service):**
```
POST http://localhost:8002/api/orders
Headers:
  Authorization: Bearer {token_dari_step_1}
  X-Correlation-ID: test-123
Body: {
  "user_id": 1,
  "items": [{
    "product_id": 1,
    "quantity": 2,
    "price": 50000
  }],
  "total": 100000
}
```

**4. Cek Log untuk Distributed Tracing:**
```bash
# Terminal 4 - Cek log User Service
cd user-service
tail -f storage/logs/laravel.log | grep "test-123"

# Terminal 5 - Cek log Product Service
cd product-service
tail -f storage/logs/laravel.log | grep "test-123"

# Terminal 6 - Cek log Order Service
cd order-service
tail -f storage/logs/laravel.log | grep "test-123"
```

**Verifikasi:** Semua log harus memiliki correlation_id yang sama: `test-123`

---

## ✅ Checklist Quick Test

### User Service
- [ ] `php artisan test` - Semua test pass
- [ ] Register user - Response 201 dengan token
- [ ] Login - Response 200 dengan token
- [ ] Get profile - Response 200 dengan data user
- [ ] Response header memiliki X-Correlation-ID

### Product Service
- [ ] `php artisan test` - Semua test pass
- [ ] Create product - Response 201
- [ ] Get product by ID - Response 200
- [ ] Response header memiliki X-Correlation-ID

### Order Service
- [ ] `php artisan test` - Semua test pass
- [ ] Create order - Response 201
- [ ] Response memiliki correlation_id
- [ ] Correlation ID sama di semua service logs

---

## 🎯 Test Minimal untuk Demo

Jika waktu terbatas, test minimal ini:

1. **Unit Test:**
   ```bash
   cd user-service && php artisan test
   cd ../product-service && php artisan test
   cd ../order-service && php artisan test
   ```

2. **Manual Test - Create Order:**
   - Register user → dapat token
   - Create product → dapat product_id
   - Create order dengan correlation ID → cek log di 3 service

3. **Proof of Distributed Tracing:**
   - Screenshot log dari 3 service dengan correlation_id yang sama

---

**Selamat Testing! 🚀**
