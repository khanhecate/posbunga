# Database Mapping - Aplikasi POS Toko Bunga

## 1. users (Pengguna)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| username | VARCHAR(50) | Unique, Not Null |
| password | VARCHAR(255) | Hashed, Not Null |
| nama_lengkap | VARCHAR(100) | Not Null |
| role | ENUM('admin', 'kasir') | Default: 'kasir' |
| no_telp | VARCHAR(20) | Nullable |
| is_active | BOOLEAN | Default: True |
| created_at | TIMESTAMP | Default: NOW() |
| updated_at | TIMESTAMP | On Update |

---

## 2. kategori_produk

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| nama_kategori | VARCHAR(50) | Unique, Not Null (Mawar, Lily, Tulip, Buket, dll.) |
| deskripsi | TEXT | Nullable |
| created_at | TIMESTAMP | Default: NOW() |

---

## 3. produk (Data Produk / Bunga)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| kode_bunga | VARCHAR(20) | Unique, Not Null (misal: BNG-001) |
| nama_bunga | VARCHAR(100) | Not Null |
| kategori_id | INTEGER | FK → kategori_produk.id |
| harga_beli | DECIMAL(12,2) | Not Null |
| harga_jual | DECIMAL(12,2) | Not Null |
| stok | INTEGER | Default: 0 |
| foto | VARCHAR(255) | Path file foto, Nullable |
| deskripsi | TEXT | Nullable |
| is_active | BOOLEAN | Default: True |
| created_at | TIMESTAMP | Default: NOW() |
| updated_at | TIMESTAMP | On Update |

---

## 4. pelanggan

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| nama | VARCHAR(100) | Not Null |
| no_telp | VARCHAR(20) | Nullable |
| email | VARCHAR(100) | Nullable |
| alamat | TEXT | Nullable |
| catatan | TEXT | Preferensi/catatan khusus |
| created_at | TIMESTAMP | Default: NOW() |
| updated_at | TIMESTAMP | On Update |

---

## 5. supplier

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| nama | VARCHAR(100) | Not Null |
| no_telp | VARCHAR(20) | Nullable |
| email | VARCHAR(100) | Nullable |
| alamat | TEXT | Nullable |
| kontak_person | VARCHAR(100) | Nullable |
| created_at | TIMESTAMP | Default: NOW() |
| updated_at | TIMESTAMP | On Update |

---

## 6. transaksi (Header Transaksi Penjualan)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| no_transaksi | VARCHAR(30) | Unique, Not Null (misal: TRX-20260716-001) |
| tanggal | TIMESTAMP | Default: NOW() |
| pelanggan_id | INTEGER | FK → pelanggan.id, Nullable (walkin) |
| user_id | INTEGER | FK → users.id (kasir yang handle) |
| subtotal | DECIMAL(12,2) | Total sebelum diskon |
| diskon | DECIMAL(12,2) | Default: 0 |
| total | DECIMAL(12,2) | Subtotal - diskon |
| metode_bayar | ENUM('cash', 'qris', 'transfer') | Not Null |
| jumlah_bayar | DECIMAL(12,2) | Uang yang dibayar |
| kembalian | DECIMAL(12,2) | Jumlah bayar - total |
| catatan | TEXT | Nullable |
| created_at | TIMESTAMP | Default: NOW() |

---

## 7. detail_transaksi (Item per Transaksi)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| transaksi_id | INTEGER | FK → transaksi.id |
| produk_id | INTEGER | FK → produk.id |
| nama_produk | VARCHAR(100) | Snapshot nama saat transaksi |
| harga_jual | DECIMAL(12,2) | Snapshot harga saat transaksi |
| qty | INTEGER | Not Null |
| diskon_item | DECIMAL(12,2) | Default: 0 |
| subtotal | DECIMAL(12,2) | (harga_jual * qty) - diskon_item |

---

## 8. pembelian_stok (Pembelian dari Supplier)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| no_pembelian | VARCHAR(30) | Unique (misal: PBL-20260716-001) |
| tanggal | TIMESTAMP | Default: NOW() |
| supplier_id | INTEGER | FK → supplier.id |
| user_id | INTEGER | FK → users.id (yang input) |
| total | DECIMAL(12,2) | Total pembelian |
| catatan | TEXT | Nullable |
| created_at | TIMESTAMP | Default: NOW() |

---

## 9. detail_pembelian_stok

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| pembelian_id | INTEGER | FK → pembelian_stok.id |
| produk_id | INTEGER | FK → produk.id |
| qty | INTEGER | Not Null |
| harga_beli | DECIMAL(12,2) | Harga beli saat itu |
| subtotal | DECIMAL(12,2) | harga_beli * qty |

---

## 10. pengaturan (Settings Toko)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | INTEGER | Primary Key, Auto Increment |
| key | VARCHAR(50) | Unique (nama_toko, alamat, telp, pajak_persen, dll.) |
| value | TEXT | Nilai setting |
| updated_at | TIMESTAMP | On Update |

---

## Relasi Antar Tabel

```
kategori_produk (1) ──── (N) produk
produk          (1) ──── (N) detail_transaksi
produk          (1) ──── (N) detail_pembelian_stok
transaksi       (1) ──── (N) detail_transaksi
pembelian_stok  (1) ──── (N) detail_pembelian_stok
pelanggan       (1) ──── (N) transaksi
supplier        (1) ──── (N) pembelian_stok
users           (1) ──── (N) transaksi
users           (1) ──── (N) pembelian_stok
```

---

## Catatan Desain

1. **Snapshot harga** di detail_transaksi supaya laporan tetap akurat walau harga produk berubah
2. **Pelanggan nullable** di transaksi untuk pelanggan walk-in yang tidak perlu dicatat
3. **Stok otomatis berkurang** saat transaksi penjualan, **bertambah** saat pembelian stok
4. **Soft delete** via `is_active` (produk & user tidak benar-benar dihapus)
5. **no_transaksi & no_pembelian** di-generate otomatis dengan format tanggal + sequence
