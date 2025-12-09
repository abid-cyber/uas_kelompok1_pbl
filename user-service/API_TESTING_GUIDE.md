# 🧪 Panduan Testing API

## Opsi 1: Thunder Client (Recommended - VS Code Extension)

### Setup Thunder Client

1. **Install Thunder Client** di VS Code:
   - Buka VS Code
   - Klik Extensions (Ctrl+Shift+X)
   - Search "Thunder Client"
   - Install extension oleh Ranga Vadhineni

2. **Buka Thunder Client**:
   - Klik ikon petir (⚡) di sidebar kiri VS Code
   - Atau tekan `Ctrl+Shift+P` → ketik "Thunder Client"

### Test Endpoints

#### 1. Register User

**Method:** `POST`  
**URL:** `http://localhost:8000/api/register`  
**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
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

**💡 Tips:** Simpan token yang didapat untuk test endpoint berikutnya!

---

#### 2. Login

**Method:** `POST`  
**URL:** `http://localhost:8000/api/login`  
**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
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

**💡 Simpan token ini untuk endpoint protected!**

---

#### 3. Get User Profile (Protected)

**Method:** `GET`  
**URL:** `http://localhost:8000/api/user/profile`  
**Headers:**
```
Authorization: Bearer {token_yang_didapat_dari_login}
Content-Type: application/json
X-Correlation-ID: test-123
```

**Contoh:**
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
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

---

#### 4. Get All Users (Admin Only)

**Method:** `GET`  
**URL:** `http://localhost:8000/api/users`  
**Headers:**
```
Authorization: Bearer {token_admin}
Content-Type: application/json
```

**💡 Note:** Hanya admin yang bisa akses. Buat user dengan role "admin" dulu.

**Expected Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "081234567890",
            "address": "Jl. Contoh No. 123",
            "role": "pembeli",
            "created_at": "2025-12-09T..."
        }
    ]
}
```

---

#### 5. Get User by ID

**Method:** `GET`  
**URL:** `http://localhost:8000/api/users/1`  
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
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
        "role": "pembeli",
        "created_at": "2025-12-09T..."
    }
}
```

---

#### 6. Update User

**Method:** `PUT`  
**URL:** `http://localhost:8000/api/users/1`  
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "name": "John Updated",
    "phone": "081999999999"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {
        "id": 1,
        "name": "John Updated",
        "email": "john@example.com",
        "phone": "081999999999",
        "address": "Jl. Contoh No. 123",
        "role": "pembeli"
    }
}
```

---

#### 7. Delete User (Admin Only)

**Method:** `DELETE`  
**URL:** `http://localhost:8000/api/users/2`  
**Headers:**
```
Authorization: Bearer {token_admin}
Content-Type: application/json
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

---

#### 8. Logout

**Method:** `POST`  
**URL:** `http://localhost:8000/api/logout`  
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Logout successful"
}
```

---

## Opsi 2: Postman (Chrome/Desktop App)

### Setup Postman

1. **Download Postman:**
   - Desktop App: https://www.postman.com/downloads/
   - Chrome Extension (legacy): Tidak direkomendasikan

2. **Create Collection:**
   - Klik "New" → "Collection"
   - Nama: "User Service API"

3. **Setup Environment (Optional):**
   - Klik "Environments" → "Create Environment"
   - Variables:
     - `base_url`: `http://localhost:8000`
     - `token`: (akan diisi setelah login)

### Test Endpoints

Gunakan method, URL, headers, dan body yang sama seperti di Thunder Client.

**Tips Postman:**
- Set token di Environment variable setelah login
- Gunakan `{{base_url}}/api/login` untuk URL
- Gunakan `{{token}}` di Authorization header

---

## Opsi 3: cURL (Command Line)

### Test Register
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

### Test Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Test Profile (Ganti {token} dengan token yang didapat)
```bash
curl -X GET http://localhost:8000/api/user/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

---

## Opsi 4: Browser (Chrome) - Hanya untuk GET

Untuk endpoint GET saja, bisa langsung buka di browser:

```
http://localhost:8000/api/users
```

**Note:** Akan error karena butuh Authorization header. Gunakan extension seperti "ModHeader" untuk menambahkan header.

---

## 🚨 Common Errors & Solutions

### Error 401: Unauthorized
- **Penyebab:** Token tidak valid atau expired
- **Solusi:** Login lagi dan dapatkan token baru

### Error 422: Validation Failed
- **Penyebab:** Data yang dikirim tidak valid
- **Solusi:** Cek body request sesuai dengan format yang benar

### Error 403: Forbidden
- **Penyebab:** User tidak memiliki permission (bukan admin)
- **Solusi:** Gunakan token dari user dengan role "admin"

### Error 500: Internal Server Error
- **Penyebab:** Server error
- **Solusi:** Cek log di `storage/logs/laravel.log`

---

## 📝 Tips Testing

1. **Simpan Token:** Setelah login, simpan token untuk test endpoint protected
2. **Test Validation:** Coba kirim data invalid untuk test validasi
3. **Test Authorization:** Test dengan user biasa vs admin
4. **Check Response Format:** Pastikan format response sesuai spesifikasi

---

## 🎯 Quick Test Flow

1. ✅ Register user baru
2. ✅ Login dengan user tersebut
3. ✅ Simpan token
4. ✅ Get profile dengan token
5. ✅ Update profile
6. ✅ (Optional) Register admin dan test CRUD

**Selamat Testing! 🚀**

