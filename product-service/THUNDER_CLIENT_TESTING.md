# Panduan Testing dengan Thunder Client

Thunder Client adalah extension VS Code untuk testing API. Berikut cara menggunakannya untuk test Product Service.

## Setup Awal

1. Install Thunder Client extension di VS Code
2. Buka Thunder Client dari sidebar VS Code
3. Pastikan server berjalan di `http://localhost:8001`

## Collection: Product Service API

### 1. GET - Get All Products

**Request:**
- Method: `GET`
- URL: `http://localhost:8001/api/products`
- Headers: (tidak perlu)

**Query Parameters (Optional):**
- `page`: 1
- `per_page`: 10
- `search`: katun

**Expected Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Kain Katun",
      "description": "Kain katun berkualitas tinggi",
      "price": "50000.00",
      "stock": 100,
      "category_id": 1,
      "supplier_id": 1,
      "created_at": "2025-12-10T12:00:00.000000Z",
      "updated_at": "2025-12-10T12:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  }
}
```

---

### 2. GET - Get Product by ID

**Request:**
- Method: `GET`
- URL: `http://localhost:8001/api/products/1`
- Headers: (tidak perlu)

**Expected Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Kain Katun",
    "description": "Kain katun berkualitas tinggi",
    "price": "50000.00",
    "stock": 100,
    "category_id": 1,
    "supplier_id": 1,
    "created_at": "2025-12-10T12:00:00.000000Z",
    "updated_at": "2025-12-10T12:00:00.000000Z"
  }
}
```

**Expected Response (404 - Not Found):**
```json
{
  "success": false,
  "message": "Produk tidak ditemukan"
}
```

---

### 3. POST - Create Product

**Request:**
- Method: `POST`
- URL: `http://localhost:8001/api/products`
- Headers:
  - `Content-Type`: `application/json`
- Body (JSON):
```json
{
  "name": "Kain Katun",
  "description": "Kain katun berkualitas tinggi",
  "price": 50000,
  "stock": 100,
  "category_id": 1,
  "supplier_id": 1
}
```

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
    "created_at": "2025-12-10T12:00:00.000000Z",
    "updated_at": "2025-12-10T12:00:00.000000Z"
  }
}
```

**Expected Response (422 - Validation Error):**
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "name": ["Nama produk wajib diisi"],
    "price": ["Harga wajib diisi"],
    "stock": ["Stok wajib diisi"],
    "category_id": ["Kategori wajib dipilih"],
    "supplier_id": ["Supplier wajib dipilih"]
  }
}
```

---

### 4. PUT - Update Product

**Request:**
- Method: `PUT`
- URL: `http://localhost:8001/api/products/1`
- Headers:
  - `Content-Type`: `application/json`
- Body (JSON):
```json
{
  "name": "Kain Katun Premium",
  "description": "Kain katun premium berkualitas tinggi",
  "price": 60000,
  "stock": 150,
  "category_id": 1,
  "supplier_id": 1
}
```

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Produk berhasil diperbarui",
  "data": {
    "id": 1,
    "name": "Kain Katun Premium",
    "description": "Kain katun premium berkualitas tinggi",
    "price": "60000.00",
    "stock": 150,
    "category_id": 1,
    "supplier_id": 1,
    "created_at": "2025-12-10T12:00:00.000000Z",
    "updated_at": "2025-12-10T12:01:00.000000Z"
  }
}
```

**Expected Response (404 - Not Found):**
```json
{
  "success": false,
  "message": "Produk tidak ditemukan"
}
```

---

### 5. DELETE - Delete Product

**Request:**
- Method: `DELETE`
- URL: `http://localhost:8001/api/products/1`
- Headers: (tidak perlu)

**Expected Response (200):**
```json
{
  "success": true,
  "message": "Produk berhasil dihapus"
}
```

**Expected Response (404 - Not Found):**
```json
{
  "success": false,
  "message": "Produk tidak ditemukan"
}
```

---

## Test Cases untuk Validasi

### Test 1: Validasi Name Kosong
```json
{
  "name": "",
  "price": 50000,
  "stock": 100,
  "category_id": 1,
  "supplier_id": 1
}
```
**Expected:** Status 422, error "Nama produk wajib diisi"

### Test 2: Validasi Price Negatif
```json
{
  "name": "Test Product",
  "price": -100,
  "stock": 100,
  "category_id": 1,
  "supplier_id": 1
}
```
**Expected:** Status 422, error "Harga tidak boleh negatif"

### Test 3: Validasi Stock Negatif
```json
{
  "name": "Test Product",
  "price": 50000,
  "stock": -10,
  "category_id": 1,
  "supplier_id": 1
}
```
**Expected:** Status 422, error "Stok tidak boleh negatif"

### Test 4: Validasi Category ID Tidak Ada
```json
{
  "name": "Test Product",
  "price": 50000,
  "stock": 100,
  "category_id": 999,
  "supplier_id": 1
}
```
**Expected:** Status 422, error "Kategori tidak ditemukan"

### Test 5: Validasi Supplier ID Tidak Ada
```json
{
  "name": "Test Product",
  "price": 50000,
  "stock": 100,
  "category_id": 1,
  "supplier_id": 999
}
```
**Expected:** Status 422, error "Supplier tidak ditemukan"

---

## Tips Menggunakan Thunder Client

1. **Save Request**: Klik icon "Save" untuk menyimpan request ke collection
2. **Environment Variables**: Buat environment untuk mudah switch antara development/production
   - Variable: `{{base_url}}` = `http://localhost:8001`
   - Gunakan: `{{base_url}}/api/products`
3. **Test Scripts**: Bisa tambahkan test assertions di tab "Tests"
4. **History**: Semua request tersimpan di history untuk referensi
5. **Collections**: Organize requests dalam collections untuk mudah manage

---

## Setup Environment Variables

1. Klik "Environments" di Thunder Client
2. Buat environment baru: "Local Development"
3. Tambahkan variable:
   - `base_url`: `http://localhost:8001`
4. Gunakan di URL: `{{base_url}}/api/products`

---

## Troubleshooting

### Error: Connection Refused
- Pastikan server berjalan: `php artisan serve --port=8001`
- Atau jika menggunakan Docker: `docker-compose up -d`

### Error: 404 Not Found
- Pastikan route sudah terdaftar: `php artisan route:list`
- Cek URL apakah benar: `/api/products` (bukan `/api/product`)

### Error: 422 Validation Error
- Cek semua field required sudah diisi
- Pastikan `category_id` dan `supplier_id` sudah ada di database
- Cek format data (price harus numeric, stock harus integer)

### Error: 500 Internal Server Error
- Cek log: `storage/logs/laravel.log`
- Pastikan database sudah di-migrate: `php artisan migrate`
- Pastikan `.env` sudah dikonfigurasi dengan benar

