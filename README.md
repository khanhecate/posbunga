# POS Toko Bunga

Aplikasi Point of Sale sederhana untuk toko bunga. Dibangun dengan PHP + MariaDB, berjalan di Docker container.

---

## Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Web Server | Apache (PHP 8.2) - Docker container |
| Database | MariaDB 10.11 - Docker container |
| DB Admin | phpMyAdmin - Docker container |
| Frontend | PHP + HTML + CSS + Vanilla JS |
| Backend | PHP (PDO) |
| Auth | PHP Session + password_hash/verify |

---

## Infrastructure (Docker)

```yaml
services:
  web:      php:8.2-apache    -> localhost:8080
  db:       mariadb:10.11     -> localhost:3306
  pma:      phpmyadmin        -> localhost:8081
```

Volume mount: `./html` -> `/var/www/html` (document root)

**Commands:**
```bash
# Start semua container
docker compose up -d

# Import database schema + seed
docker exec -i posbunga-db mariadb -u posbunga -pposbunga123 posbunga < database.sql

# Stop
docker compose down
```

---

## Credentials

| User | Username | Password | Role | Akses |
|------|----------|----------|------|-------|
| Admin | sandal | sandal | admin | Panel admin (`/be/admin/`) |
| Customer | user | user | kasir | Katalog + checkout + tracking |

Database:
- Host: `db` (internal) / `localhost:3306` (external)
- Database: `posbunga`
- User: `posbunga` / Password: `posbunga123`
- Root password: `root123`

---

## Folder Structure

```
posbunga/
├── docker-compose.yml           # Container definitions
├── database.sql                 # Schema + seed data
├── database-tables.txt          # Ringkasan tabel (reference)
├── database-mapping.md          # ERD & relasi detail
├── development-plan.md          # Roadmap development
├── users.txt                    # Daftar credentials
├── README.md                    # File ini
│
└── html/                        # Document root (Apache volume)
    ├── fe/                      # Frontend (customer-facing)
    │   ├── index.php            # Katalog produk + keranjang sidebar
    │   ├── login.php            # Halaman login
    │   ├── logout.php           # Logout handler
    │   ├── checkout.php         # Checkout + payment (dummy CC)
    │   ├── tracking.php         # Tracking pesanan
    │   ├── profil.php           # Setting user (nama, alamat, telp)
    │   ├── css/
    │   │   ├── style.css        # Main stylesheet
    │   │   ├── checkout.css     # Checkout page styles
    │   │   └── tracking.css     # Tracking page styles
    │   └── js/
    │       └── app.js           # Cart, filter, lightbox, rating, mobile toggle
    │
    └── be/                      # Backend
        ├── config/
        │   └── database.php     # PDO connection
        ├── api/
        │   ├── produk.php       # GET list produk (filter, search, pagination)
        │   ├── kategori.php     # GET list kategori
        │   └── rating.php       # GET/POST rating produk
        ├── assets/
        │   └── produk/          # Upload foto produk
        └── admin/               # Admin panel (single entry point)
            ├── index.php        # Layout wrapper + router
            ├── css/
            │   └── admin.css    # Admin stylesheet
            └── pages/           # Content pages (loaded by router)
                ├── dashboard.php
                ├── produk.php
                ├── transaksi.php
                ├── order-detail.php
                └── ganti-password.php
```

---

## Arsitektur Admin Panel

Admin panel menggunakan **single entry point** pattern:
- `index.php` = layout (header + sidebar + content area)
- Content di-load dari `pages/` berdasarkan query param `?page=xxx`
- Tidak perlu duplicate layout di setiap file
- Sidebar active state otomatis berdasarkan `$currentPage`

URL pattern: `/be/admin/?page=dashboard`, `?page=produk`, `?page=transaksi`, dll.

---

## 3 Objek Utama

### 1. Frontend (Customer)

Halaman publik untuk customer browse & order bunga.

| Halaman | File | Status |
|---------|------|--------|
| Katalog Produk | `fe/index.php` | Done |
| Login | `fe/login.php` | Done |
| Checkout + Payment | `fe/checkout.php` | Done |
| Tracking Pesanan | `fe/tracking.php` | Done |
| Profil User | `fe/profil.php` | Done |

**Fitur FE:**
- Grid produk dengan foto (dari DB, fallback text jika tidak ada foto)
- Filter per kategori + live search (debounce, tanpa enter)
- Image lightbox preview (klik foto -> view besar)
- Keranjang sidebar (kanan, sticky, responsive)
- Mobile: keranjang jadi FAB button + slide panel
- Checkout: form pengiriman (prefill dari profil) + dummy credit card + skip validation toggle
- Order tracking dengan progress bar
- Konfirmasi penerimaan pesanan (auto-complete 3 hari)
- Star rating per produk (hover + klik, simpan ke DB)
- User dropdown menu (profil, pesanan, logout)
- Halaman profil: setting nama, alamat, telp (prefill saat checkout)

---

### 2. Backend (Admin Panel)

Panel internal untuk admin kelola toko.

| Halaman | Page | Status |
|---------|------|--------|
| Dashboard | `?page=dashboard` | Done |
| CRUD Produk | `?page=produk` | Done |
| CRUD Kategori | popup di produk | Done |
| Manage Orders | `?page=transaksi` | Done |
| Order Detail | `?page=order-detail&id=X` | Done |
| Ganti Password | `?page=ganti-password` | Done |
| CRUD Pelanggan | `?page=pelanggan` | TODO |
| CRUD Supplier | `?page=supplier` | TODO |
| Laporan | `?page=laporan` | TODO |
| Manage Users | `?page=pengguna` | TODO |
| Pengaturan | `?page=pengaturan` | TODO |

**Fitur Admin:**
- Dashboard: omzet hari ini, total transaksi, order perlu diproses, stok rendah, transaksi terbaru, produk terlaris
- CRUD Produk: modal popup (tambah/edit/hapus) + upload foto (auto-delete foto lama)
- CRUD Kategori: modal popup di halaman produk
- Manage Orders: list + filter by status + update status (next step atau manual)
- Order Detail: items, info pengiriman, payment, update status dropdown
- Ganti Password: verify password lama, set baru
- User dropdown: ganti password, pengaturan, lihat toko, logout

---

### 3. Integrasi CRUD (FE <-> BE)

| Flow | Deskripsi | Status |
|------|-----------|--------|
| List Produk | FE fetch `/be/api/produk.php` -> render grid | Done |
| Filter/Search | Query params `?kategori=1&search=mawar` (live) | Done |
| Add to Cart | localStorage di browser | Done |
| Login | POST form -> session -> redirect by role | Done |
| Checkout | POST cart + shipping + payment -> insert DB -> kurangi stok | Done |
| Order Tracking | User lihat status pesanan + progress bar | Done |
| Konfirmasi Pesanan | User klik confirm -> status jadi done | Done |
| Auto-complete | Delivered > 3 hari -> otomatis done | Done |
| Admin Update Status | Admin ubah status via dropdown | Done |
| Rating Produk | User klik star -> POST ke DB -> update avg | Done |
| Profil Prefill | Data profil prefill di checkout | Done |

---

## Order Status Flow

```
pending -> paid -> packing -> shipping -> delivered -> done
                                                       ^ auto-complete 3 hari
                   \-> batal (kapan saja sebelum delivered)
```

| Status | Artinya |
|--------|---------|
| pending | Order dibuat, belum bayar |
| paid | Pembayaran diterima |
| packing | Admin sedang proses/packing |
| shipping | Dalam pengiriman |
| delivered | Sampai ke penerima (menunggu konfirmasi) |
| done | Selesai (dikonfirmasi user atau auto 3 hari) |
| batal | Dibatalkan |

---

## Database Tables

11 tabel (lihat `database-tables.txt` untuk detail):
1. users
2. kategori_produk
3. produk
4. pelanggan
5. supplier
6. transaksi (+ kolom pengiriman & tracking)
7. detail_transaksi
8. pembelian_stok
9. detail_pembelian_stok
10. pengaturan
11. rating_produk

---

## UI Rules

- Tidak menggunakan emoji di UI kecuali star rating (★)
- Semua label dan button menggunakan text biasa
- Admin panel: single entry point, sidebar box, header dengan dropdown user

---

## Prerequisites

Sebelum menjalankan aplikasi, pastikan sistem Anda memiliki:

### Server Requirements
- **PHP 8.2** atau lebih tinggi
- **Apache** web server dengan mod_rewrite enabled
- **MariaDB 10.11** atau MySQL 8.0+
- **PHP Extensions:**
  - PDO
  - PDO MySQL
  - GD (untuk manipulasi gambar)
  - Session support
  - JSON support

### Development Tools (Opsional)
- **Docker & Docker Compose** (untuk menjalankan dengan container)
- **phpMyAdmin** (untuk administrasi database)
- **Git** (untuk version control)

## Installation

### Opsi 1: Menggunakan Docker (Recommended)

```bash
# 1. Clone repository
git clone https://github.com/khanhecate/posbunga.git
cd posbunga

# 2. Start containers
docker compose up -d

# 3. Import database
docker exec -i posbunga-db mariadb -u posbunga -pposbunga123 posbunga < database.sql

# 4. Akses aplikasi
# Customer:  http://localhost:8080/fe/
# Admin:     http://localhost:8080/be/admin/
# phpMyAdmin: http://localhost:8081
```

### Opsi 2: Manual Setup

```bash
# 1. Clone repository
git clone https://github.com/khanhecate/posbunga.git
cd posbunga

# 2. Setup web server
# Copy folder 'html/' ke document root Apache Anda
# Atau setup virtual host yang mengarah ke folder 'html/'

# 3. Create database
mysql -u root -p
CREATE DATABASE posbunga CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'posbunga'@'localhost' IDENTIFIED BY 'posbunga123';
GRANT ALL PRIVILEGES ON posbunga.* TO 'posbunga'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 4. Import database
mysql -u posbunga -pposbunga123 posbunga < database.sql

# 5. Update database config (jika perlu)
# Edit html/be/config/database.php sesuai setting database Anda
```

## Configuration

Jika menggunakan setup manual, edit file `html/be/config/database.php`:

```php
$host = 'localhost';        // atau IP database server
$dbname = 'posbunga';
$username = 'posbunga';
$password = 'posbunga123';
$port = 3306;
```

---

## TODO (Next Steps)

- [ ] Admin: CRUD pelanggan
- [ ] Admin: CRUD supplier
- [ ] Admin: laporan penjualan (filter tanggal, export)
- [ ] Admin: manage users (tambah kasir baru)
- [ ] Admin: pengaturan toko (nama, alamat, pajak)
- [ ] FE: notif pesanan berubah status
- [ ] Export laporan PDF/CSV
