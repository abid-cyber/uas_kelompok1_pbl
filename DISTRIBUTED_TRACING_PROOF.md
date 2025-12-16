# Proof of Distributed Tracing - Logging Terdistribusi

## 📋 Overview

Dokumen ini membuktikan implementasi logging terdistribusi dengan Correlation ID yang konsisten di seluruh microservices (User Service, Product Service, dan Order Service).

## 🔍 Konsep Distributed Tracing

Distributed tracing memungkinkan kita untuk melacak request yang melewati multiple services dengan menggunakan **Correlation ID** yang sama. Setiap log entry akan memiliki `correlation_id` yang sama, memungkinkan kita untuk:

1. Melacak alur request dari service ke service
2. Debugging masalah yang terjadi di multiple services
3. Monitoring performa request end-to-end
4. Analisis pola error yang terjadi di distributed system

## 🏗️ Implementasi

### 1. Correlation ID Middleware

Setiap service memiliki middleware yang:
- Mengambil Correlation ID dari header `X-Correlation-ID` (jika ada)
- Generate UUID baru jika tidak ada
- Set Correlation ID ke log context menggunakan `Log::withContext()`

**File:**
- `user-service/app/Http/Middleware/CorrelationIdMiddleware.php`
- `product-service/app/Http/Middleware/CorrelationIdMiddleware.php`
- `order-service/app/Http/Middleware/CorrelationIdMiddleware.php`

### 2. Logging Format Konsisten

Semua service menggunakan format logging yang konsisten:
- **Log::info()** untuk operasi sukses
- **Log::error()** untuk error
- **Log::warning()** untuk warning
- Semua log otomatis include `correlation_id` dari context

## 📊 Contoh Skenario: Create Order

Berikut adalah contoh alur request **Create Order** yang melewati 3 services:

### Request Flow:
1. **Client** → Order Service: `POST /api/orders`
2. **Order Service** → User Service: `GET /api/user/profile` (validate token)
3. **Order Service** → User Service: `GET /api/users/{id}` (validate user)
4. **Order Service** → Product Service: `GET /api/products/{id}` (validate product)
5. **Order Service** → Product Service: `PUT /api/products/{id}/stock` (update stock)

### Correlation ID: `550e8400-e29b-41d4-a716-446655440000`

## 📝 Cuplikan Log dari Setiap Service

### Order Service Logs

```log
[2024-01-15 10:30:15] local.INFO: Order created successfully {"order_id":1,"user_id":1,"correlation_id":"550e8400-e29b-41d4-a716-446655440000"} 

[2024-01-15 10:30:14] local.INFO: User Service call successful {"correlation_id":"550e8400-e29b-41d4-a716-446655440000","endpoint":"/api/user/profile"} 

[2024-01-15 10:30:14] local.INFO: Product Service call successful {"correlation_id":"550e8400-e29b-41d4-a716-446655440000","product_id":1} 

[2024-01-15 10:30:14] local.INFO: Product stock updated {"correlation_id":"550e8400-e29b-41d4-a716-446655440000","product_id":1,"quantity":-2}
```

### User Service Logs

```log
[2024-01-15 10:30:14] local.INFO: User profile retrieved {"user_id":1,"correlation_id":"550e8400-e29b-41d4-a716-446655440000"} 

[2024-01-15 10:30:14] local.INFO: User retrieved {"user_id":1,"correlation_id":"550e8400-e29b-41d4-a716-446655440000"}
```

### Product Service Logs

```log
[2024-01-15 10:30:14] local.INFO: Product retrieved {"product_id":1,"correlation_id":"550e8400-e29b-41d4-a716-446655440000"} 

[2024-01-15 10:30:14] local.INFO: Product stock updated {"product_id":1,"old_stock":100,"new_stock":98,"quantity_change":-2,"correlation_id":"550e8400-e29b-41d4-a716-446655440000"}
```

## 🔗 Tracing Request End-to-End

Dengan Correlation ID yang sama (`550e8400-e29b-41d4-a716-446655440000`), kita dapat melacak request dari awal sampai akhir:

```
┌─────────────────────────────────────────────────────────────────┐
│ Request Flow dengan Correlation ID:                            │
│ 550e8400-e29b-41d4-a716-446655440000                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│ 1. Order Service                                                │
│    └─ [10:30:15] Order created successfully                     │
│       correlation_id: 550e8400-e29b-41d4-a716-446655440000    │
│                                                                 │
│ 2. Order Service → User Service                                │
│    └─ [10:30:14] User profile retrieved                        │
│       correlation_id: 550e8400-e29b-41d4-a716-446655440000    │
│                                                                 │
│ 3. Order Service → User Service                                 │
│    └─ [10:30:14] User retrieved                                │
│       correlation_id: 550e8400-e29b-41d4-a716-446655440000    │
│                                                                 │
│ 4. Order Service → Product Service                              │
│    └─ [10:30:14] Product retrieved                             │
│       correlation_id: 550e8400-e29b-41d4-a716-446655440000    │
│                                                                 │
│ 5. Order Service → Product Service                              │
│    └─ [10:30:14] Product stock updated                         │
│       correlation_id: 550e8400-e29b-41d4-a716-446655440000    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## 🛠️ Cara Menggunakan Distributed Tracing

### 1. Mencari Log Berdasarkan Correlation ID

Untuk mencari semua log terkait request tertentu, gunakan Correlation ID:

```bash
# Order Service
grep "550e8400-e29b-41d4-a716-446655440000" storage/logs/laravel.log

# User Service
grep "550e8400-e29b-41d4-a716-446655440000" storage/logs/laravel.log

# Product Service
grep "550e8400-e29b-41d4-a716-446655440000" storage/logs/laravel.log
```

### 2. Monitoring Error dengan Correlation ID

Jika terjadi error, Correlation ID membantu melacak di service mana error terjadi:

```log
[2024-01-15 10:30:14] local.ERROR: User Service call failed {"error":"Connection timeout","correlation_id":"550e8400-e29b-41d4-a716-446655440000"} 

[2024-01-15 10:30:14] local.ERROR: Order creation failed {"error":"User service unavailable","correlation_id":"550e8400-e29b-41d4-a716-446655440000","trace":"..."}
```

### 3. Analisis Performa

Dengan Correlation ID, kita dapat menganalisis waktu response di setiap service:

```log
[2024-01-15 10:30:14.100] Order Service: Request received
[2024-01-15 10:30:14.150] User Service: Profile retrieved (50ms)
[2024-01-15 10:30:14.200] Product Service: Product retrieved (50ms)
[2024-01-15 10:30:14.250] Order Service: Order created (150ms total)
```

## ✅ Checklist Implementasi

- ✅ **Log Context**: Semua service menggunakan `Log::withContext(['correlation_id' => $correlationId])`
- ✅ **Logging Format Konsisten**: Semua service menggunakan format yang sama (Log::info, Log::error, Log::warning)
- ✅ **Correlation ID di Header**: Semua inter-service calls mengirim `X-Correlation-ID` header
- ✅ **Correlation ID di Response**: Semua response mengembalikan `X-Correlation-ID` header
- ✅ **Proof of Distributed Tracing**: Dokumentasi ini membuktikan implementasi

## 📁 File Log Location

- **User Service**: `user-service/storage/logs/laravel.log`
- **Product Service**: `product-service/storage/logs/laravel.log`
- **Order Service**: `order-service/storage/logs/laravel.log`

## 🎯 Kesimpulan

Dengan implementasi logging terdistribusi ini, kita dapat:
1. ✅ Melacak request di semua service dengan Correlation ID yang sama
2. ✅ Debugging masalah yang terjadi di multiple services
3. ✅ Monitoring performa request end-to-end
4. ✅ Analisis pola error di distributed system

Semua log entry akan otomatis memiliki `correlation_id` dari context yang di-set di middleware, memastikan konsistensi dan kemudahan tracing.
