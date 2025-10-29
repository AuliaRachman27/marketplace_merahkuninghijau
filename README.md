# Marketplace Merah Kuning Hijau

Sebuah mini marketplace API berbasis **Laravel 12 + MySQL + JWT Auth**  
Menghubungkan **merchant** dan **customer** agar dapat melakukan transaksi dengan fitur **diskon dan bebas ongkir otomatis.**

---

## Fitur Utama
- Register & Login (JWT Auth)
- Merchant:
  - Create, Update, Delete produk
  - Melihat siapa saja customer yang membeli produknya
- Customer:
  - Melihat list produk
  - Membeli produk
  - Otomatis mendapatkan bebas ongkir jika transaksi > 15.000
  - Otomatis mendapatkan diskon 10% jika transaksi > 50.000

---

## Tech Stack
- Laravel 12
- MySQL (Laragon)
- JWT Auth (tymon/jwt-auth)
- Postman Collection untuk testing API

---

## Cara Install

### Clone Repository
```bash
git clone https://github.com/AuliaRachman27/marketplace_merahkuninghijau
cd backend-aulia-rachman-widodo

### Install Depedency
composer install

### Buat File .env
copy dari .env.example

### Generate Key & JWT Secret
php artisan key:generate
php artisan jwt:secret

### Import Database
mysql -u root marketplace_db < marketplace_db.sql

## API Endpoint
### Auth
Method	Endpoint	Deskripsi
POST	/api/auth/register	Registrasi user (role: merchant/customer)
POST	/api/auth/login	Login user

### Merchant
Method	Endpoint	Deskripsi
POST	/api/products	Tambah produk
PUT	/api/products/{id}	Update produk
DELETE	/api/products/{id}	Hapus produk
GET	/api/merchant/buyers	Lihat customer yang membeli produk

### Customer
Method	Endpoint	Deskripsi
GET	/api/products	Lihat semua produk
POST	/api/transactions	Beli produk
GET	/api/transactions	Lihat riwayat transaksi

## Testing

Gunakan file Postman Collection: marketplace.postman_collection.json
Tambahkan header: Authorization: Bearer <token> untuk semua endpoint yang membutuhkan JWT.

## Author

Aulia Rachman Widodo
Laravel Backend Project — Marketplace Merah Kuning Hijau