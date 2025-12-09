# ⚡ Panduan Thunder Client - Step by Step

## 🚀 Setup Awal

### 1. Pastikan Server Running
Buka terminal di VS Code dan jalankan:
```bash
php artisan serve
```

Server akan berjalan di: `http://localhost:8000`

---

## 📝 Test Endpoint Register

### Step 1: Buka Thunder Client
- Klik ikon **petir (⚡)** di sidebar kiri VS Code
- Atau tekan `Ctrl+Shift+P` → ketik "Thunder Client"

### Step 2: Buat Request Baru
- Klik tombol **"New Request"** (biru) di sidebar kiri
- Atau klik ikon **"+"** di tab "New Request"

### Step 3: Set Method & URL
- **Method:** Pilih `POST` dari dropdown
- **URL:** Ketik `http://localhost:8000/api/register`

### Step 4: Set Headers
- Klik tab **"Headers"**
- Pastikan ada header:
  - `Content-Type: application/json`
  
  Jika belum ada, klik **"Add Header"** dan tambahkan:
  - **Name:** `Content-Type`
  - **Value:** `application/json`

### Step 5: Set Body (JSON)
- Klik tab **"Body"**
- Pilih **"JSON"** dari dropdown (bukan form-data atau raw)
- Masukkan JSON berikut:

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

### Step 6: Send Request
- Klik tombol **"Send"** (biru) di kanan URL
- Atau tekan `Ctrl+Enter`

### Step 7: Lihat Response
Response akan muncul di panel kanan:
- **Status:** 201 (Created)
- **Response Body:** JSON dengan data user dan token

**Simpan token yang didapat!** (untuk test endpoint protected)

---

## 🔐 Test Endpoint Login

### Step 1: Buat Request Baru
- Klik **"New Request"** lagi

### Step 2: Set Method & URL
- **Method:** `POST`
- **URL:** `http://localhost:8000/api/login`

### Step 3: Set Headers
- Tab **"Headers"**
- `Content-Type: application/json`

### Step 4: Set Body
- Tab **"Body"** → Pilih **"JSON"**
- Masukkan:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

### Step 5: Send & Simpan Token
- Klik **"Send"**
- **Copy token** dari response (field `data.token`)
- Token ini untuk test endpoint protected!

---

## 👤 Test Endpoint Profile (Protected)

### Step 1: Buat Request Baru
- Klik **"New Request"**

### Step 2: Set Method & URL
- **Method:** `GET`
- **URL:** `http://localhost:8000/api/user/profile`

### Step 3: Set Headers
- Tab **"Headers"**
- Tambahkan:
  - `Content-Type: application/json`
  - `Authorization: Bearer {token_yang_didapat_dari_login}`
  
  **Contoh:**
  ```
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
  ```

### Step 4: Send
- Klik **"Send"**
- Response akan menampilkan data profile user

---

## 💡 Tips Thunder Client

### 1. Simpan Request ke Collection
- Setelah request berhasil, klik **"Save"** atau **"Save As"**
- Buat Collection baru: "User Service API"
- Simpan semua request di collection ini

### 2. Gunakan Environment Variables
- Klik tab **"Env"** di sidebar
- Buat environment baru: "Local"
- Tambahkan variables:
  - `base_url`: `http://localhost:8000`
  - `token`: (isi setelah login)
  
- Gunakan di URL: `{{base_url}}/api/register`
- Gunakan di Header: `Bearer {{token}}`

### 3. Copy Request sebagai cURL
- Setelah request, klik **"..."** (3 dots) di response
- Pilih **"Copy as cURL"**
- Bisa digunakan di terminal

### 4. Test Response
- Tab **"Tests"** untuk menulis test assertions
- Contoh:
```javascript
test("Status is 200", () => {
    expect(res.status).toBe(200);
});
```

---

## 📋 Daftar Semua Endpoint

### Public Endpoints (Tidak butuh token)

1. **Register**
   - Method: `POST`
   - URL: `http://localhost:8000/api/register`
   - Body: JSON dengan name, email, password, dll

2. **Login**
   - Method: `POST`
   - URL: `http://localhost:8000/api/login`
   - Body: JSON dengan email dan password

### Protected Endpoints (Butuh token)

3. **Get Profile**
   - Method: `GET`
   - URL: `http://localhost:8000/api/user/profile`
   - Header: `Authorization: Bearer {token}`

4. **Logout**
   - Method: `POST`
   - URL: `http://localhost:8000/api/logout`
   - Header: `Authorization: Bearer {token}`

5. **Get All Users** (Admin only)
   - Method: `GET`
   - URL: `http://localhost:8000/api/users`
   - Header: `Authorization: Bearer {token_admin}`

6. **Get User by ID**
   - Method: `GET`
   - URL: `http://localhost:8000/api/users/{id}`
   - Header: `Authorization: Bearer {token}`

7. **Update User**
   - Method: `PUT`
   - URL: `http://localhost:8000/api/users/{id}`
   - Header: `Authorization: Bearer {token}`
   - Body: JSON dengan field yang ingin diupdate

8. **Delete User** (Admin only)
   - Method: `DELETE`
   - URL: `http://localhost:8000/api/users/{id}`
   - Header: `Authorization: Bearer {token_admin}`

---

## 🎯 Quick Test Flow

1. ✅ **Register** → Dapat user baru
2. ✅ **Login** → Dapat token
3. ✅ **Simpan token** → Copy ke clipboard atau environment variable
4. ✅ **Get Profile** → Test dengan token
5. ✅ **Update Profile** → Test update
6. ✅ (Optional) **Register Admin** → Test CRUD dengan admin

---

## 🚨 Troubleshooting

### Error: Connection refused
- **Penyebab:** Server tidak running
- **Solusi:** Jalankan `php artisan serve` di terminal

### Error: 401 Unauthorized
- **Penyebab:** Token tidak valid atau expired
- **Solusi:** Login lagi dan dapatkan token baru

### Error: 422 Validation Failed
- **Penyebab:** Data yang dikirim tidak valid
- **Solusi:** Cek body JSON sesuai format yang benar

### Response tidak muncul
- **Penyebab:** Request belum di-send
- **Solusi:** Pastikan klik tombol "Send" atau tekan Ctrl+Enter

---

**Selamat Testing! 🚀**

