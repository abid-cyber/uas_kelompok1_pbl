# Arsitektur Database - Microservices

## 📊 Database per Service Pattern

Setiap microservice memiliki database sendiri sesuai dengan prinsip **Database per Service** pattern.

## 🗄️ Database Setup

### 1. User Service
- **Database Name**: `user_service` (atau `userr_service` sesuai konfigurasi)
- **Tables**:
  - `users` - Menyimpan data user (name, email, password, phone, address, role)
  - `password_reset_tokens`
  - `sessions`
  - `jobs`, `job_batches`, `failed_jobs` (Laravel default)

**File:** `user-service/.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_service
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Product Service
- **Database Name**: `product_service`
- **Tables**:
  - `products` - Menyimpan data produk (name, description, price, stock, category_id, supplier_id)
  - `categories` - Kategori produk
  - `suppliers` - Supplier produk
  - `users`, `password_reset_tokens`, `sessions` (Laravel default)
  - `jobs`, `job_batches`, `failed_jobs` (Laravel default)

**File:** `product-service/.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_service
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Order Service
- **Database Name**: `order_service`
- **Tables**:
  - `orders` - Menyimpan data order (user_id, items, total, status)
  - `users`, `password_reset_tokens`, `sessions` (Laravel default)
  - `jobs`, `job_batches`, `failed_jobs` (Laravel default)
  - `cache`, `cache_locks` (Laravel default)

**File:** `order-service/.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orderr_service
DB_USERNAME=root
DB_PASSWORD=
```

## 📋 Struktur Tabel Orders

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    items JSON NOT NULL,  -- [{"product_id": 1, "quantity": 2, "price": 50000}]
    total DECIMAL(10, 2) NOT NULL,
    status VARCHAR(255) DEFAULT 'pending',  -- pending, completed, cancelled
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);
```

## 🔄 Data Flow

### Create Order Flow:
1. **Order Service** menerima request create order
2. **Order Service** → **User Service**: Validasi token dan user (tidak menyimpan data user)
3. **Order Service** → **Product Service**: Validasi product dan stock (tidak menyimpan data product)
4. **Order Service**: Menyimpan order ke database sendiri (`order_service.orders`)
5. **Order Service** → **Product Service**: Update stock (hanya update, tidak menyimpan order)

### Get Orders Flow:
1. **Order Service**: Membaca data dari database sendiri (`order_service.orders`)
2. **Order Service** → **User Service**: Hanya untuk validasi token (jika diperlukan)

## ✅ Keuntungan Database per Service

1. **Independence**: Setiap service bisa di-deploy dan di-scale secara independen
2. **Data Ownership**: Setiap service memiliki data domain-nya sendiri
3. **Loose Coupling**: Tidak ada shared database yang menyebabkan tight coupling
4. **Technology Freedom**: Setiap service bisa menggunakan teknologi database berbeda
5. **Isolation**: Masalah di satu database tidak mempengaruhi service lain

## 🚀 Setup Database

### Untuk User Service:
```bash
cd user-service
php artisan migrate
```

### Untuk Product Service:
```bash
cd product-service
php artisan migrate
```

### Untuk Order Service:
```bash
cd order-service
php artisan migrate
```

## 📝 Catatan Penting

- ✅ Setiap service memiliki database **terpisah**
- ✅ Order Service **TIDAK** menyimpan data user atau product
- ✅ Order Service hanya menyimpan data **order** (user_id, items, total, status)
- ✅ Data user dan product tetap di service masing-masing
- ✅ Order Service hanya **memanggil** service lain untuk validasi, bukan untuk menyimpan data
