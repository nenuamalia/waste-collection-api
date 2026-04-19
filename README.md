# Community Waste Collection API

REST API untuk sistem pengelolaan sampah komunitas.
Dibangun dengan Laravel + MongoDB + Service-Repository Pattern.

## Tech Stack

- Laravel 11
- MongoDB
- Service-Repository Pattern
- JWT Authentication
- Docker

## Cara Setup (Tanpa Docker)

### 1. Clone & Install

git clone https://github.com/username/waste-collection-api.git
cd waste-collection-api
composer install

### 2. Konfigurasi Environment

cp .env.example .env
php artisan key:generate
php artisan jwt:secret

Edit .env:
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=waste_collection

### 3. Jalankan Migration & Seeder

php artisan migrate
php artisan db:seed

### 4. Jalankan Server

php artisan serve

API tersedia di: http://localhost:8000

---

## Cara Setup (Dengan Docker)

docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

---

## Contoh Request API

### Register

POST /api/auth/register
{
"name": "Budi",
"email": "budi@email.com",
"password": "rahasia123"
}

### Login

POST /api/auth/login
{
"email": "budi@email.com",
"password": "rahasia123"
}
Gunakan token dari response di header: Authorization: Bearer {token}

### Buat Household

POST /api/households
{
"owner_name": "Budi Santoso",
"address": "Jl. Melati No. 1",
"block": "A",
"no": "01"
}

### Buat Pickup Request

POST /api/pickups
Authorization: Bearer {token}
{
"household_id": "64ffec8c2a1",
"type": "organic"
}

### Schedule Pickup

PUT /api/pickups/{id}/schedule
Authorization: Bearer {token}
{
"pickup_date": "2026-04-25 09:00:00"
}

### Complete Pickup (otomatis buat payment)

PUT /api/pickups/{id}/complete
Authorization: Bearer {token}

### Konfirmasi Payment

PUT /api/payments/{id}/confirm
Authorization: Bearer {token}

---

## Arsitektur

Controller → Service → Repository → Model → MongoDB

- Controller : Terima request, kirim response
- Service : Business logic & validasi aturan bisnis
- Repository : Query ke database
- Model : Representasi data (OOP Inheritance untuk Waste)

## Business Rules

1. Household dengan unpaid payment tidak bisa buat pickup baru
2. Pickup hanya bisa dijadwalkan jika status = pending
3. Pickup completed → otomatis generate payment (Rp 50rb / Rp 100rb)
4. WasteElectronic wajib safety_check = true sebelum dijadwalkan
5. WasteOrganic auto-cancel jika tidak dijemput dalam 3 hari

## Jalankan Tests

php artisan test

## Auto-cancel Organic (manual trigger)

php artisan waste:auto-cancel-organic
