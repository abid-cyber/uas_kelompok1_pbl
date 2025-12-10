# Panduan Testing Product Service

## Cara Menjalankan Test

### 1. Menjalankan Semua Test

```bash
# Menggunakan Laravel Artisan (Recommended)
php artisan test

# Atau menggunakan composer script
composer test

# Atau menggunakan PHPUnit langsung
vendor/bin/phpunit
```

### 2. Menjalankan Test Spesifik

```bash
# Test untuk ProductCrudTest saja
php artisan test --filter ProductCrudTest

# Test untuk method spesifik
php artisan test --filter test_can_create_product

# Test untuk Feature tests saja
php artisan test tests/Feature

# Test untuk Unit tests saja
php artisan test tests/Unit
```

### 3. Menjalankan Test dengan Verbose Output

```bash
# Menampilkan detail lebih lengkap
php artisan test --verbose

# Atau dengan PHPUnit
vendor/bin/phpunit --verbose
```

### 4. Menjalankan Test dengan Coverage (jika tersedia)

```bash
php artisan test --coverage
```

## Test Cases yang Tersedia

File: `tests/Feature/ProductCrudTest.php`

1. **test_can_create_product** - Test membuat produk baru
2. **test_can_get_product_by_id** - Test mendapatkan produk berdasarkan ID
3. **test_can_get_list_of_products** - Test mendapatkan daftar produk dengan pagination
4. **test_can_update_product** - Test update produk
5. **test_can_delete_product** - Test menghapus produk
6. **test_validation_fails_for_invalid_data** - Test validasi input yang gagal
7. **test_returns_404_for_nonexistent_product** - Test error handling untuk produk tidak ditemukan

## Konfigurasi Test

Test menggunakan:
- **Database**: SQLite in-memory (tidak perlu setup database)
- **Environment**: Testing (otomatis dari phpunit.xml)
- **Refresh Database**: Setiap test akan reset database

## Troubleshooting

### Error: Database tidak ditemukan
- Pastikan file `database/database.sqlite` ada (untuk development)
- Atau test akan menggunakan in-memory database secara otomatis

### Error: Class tidak ditemukan
```bash
composer dump-autoload
```

### Error: Migration error
```bash
php artisan migrate:fresh --env=testing
```

## Contoh Output yang Diharapkan

```
PASS  Tests\Feature\ProductCrudTest
✓ can create product
✓ can get product by id
✓ can get list of products
✓ can update product
✓ can delete product
✓ validation fails for invalid data
✓ returns 404 for nonexistent product

Tests:  7 passed
Duration: 0.50s
```

