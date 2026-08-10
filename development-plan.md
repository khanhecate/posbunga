# Development Plan - POS Toko Bunga

Urutan pengerjaan berdasarkan prioritas & dependency.

---

## Phase 1: Backend Foundation

> Setup project, koneksi database, models dasar.

- [ ] Init project (requirements.txt, folder structure)
- [ ] Setup FastAPI + Uvicorn
- [ ] Setup SQLAlchemy + SQLite
- [ ] Buat models: `users`, `kategori_produk`, `produk`
- [ ] Buat database migration / auto-create tables
- [ ] Seed data awal (1 admin user, beberapa kategori)

---

## Phase 2: Backend CRUD — Master Data

> API untuk data dasar yang jadi fondasi semua fitur.

- [ ] CRUD Kategori Produk (`/api/kategori`)
- [ ] CRUD Produk / Bunga (`/api/produk`) + upload foto
- [ ] CRUD Pelanggan (`/api/pelanggan`)
- [ ] CRUD Supplier (`/api/supplier`)
- [ ] CRUD Users (`/api/users`)
- [ ] Pagination & search di semua list endpoint

---

## Phase 3: Backend — Auth & Role

> Login system supaya ada akses kontrol.

- [ ] Endpoint login (`POST /api/auth/login`) → return JWT
- [ ] Middleware auth (verify token di setiap request)
- [ ] Role-based access (admin = full, kasir = limited)
- [ ] Endpoint logout / token invalidation

---

## Phase 4: Backend — Transaksi & Logic Bisnis

> Core feature: jual barang, stok berkurang otomatis.

- [ ] Endpoint buat transaksi (`POST /api/transaksi`)
- [ ] Auto-generate nomor transaksi (TRX-YYYYMMDD-XXX)
- [ ] Validasi stok sebelum transaksi
- [ ] Stok otomatis berkurang setelah transaksi sukses
- [ ] Detail transaksi (items per transaksi)
- [ ] List transaksi + filter tanggal
- [ ] Data struk (`GET /api/transaksi/struk/{id}`)

---

## Phase 5: Backend — Pembelian Stok & Laporan

> Stok masuk dari supplier + reporting.

- [ ] Endpoint pembelian stok (`POST /api/pembelian`)
- [ ] Stok otomatis bertambah setelah pembelian
- [ ] Laporan penjualan per range tanggal
- [ ] Produk terlaris
- [ ] Alert stok menipis
- [ ] Endpoint pengaturan toko (`GET/PUT /api/pengaturan`)

---

## Phase 6: Frontend — Layout & Halaman Statis

> Skeleton UI, belum konek ke backend.

- [ ] Halaman Login
- [ ] Layout utama (sidebar + content area)
- [ ] Halaman Dashboard (placeholder data)
- [ ] Halaman Data Produk (tabel + form)
- [ ] Halaman Transaksi / Kasir
- [ ] Halaman Pelanggan
- [ ] Halaman Supplier
- [ ] Halaman Laporan
- [ ] Halaman Pengaturan

---

## Phase 7: Integrasi — CRUD FE ↔ BE

> Sambungkan frontend ke backend API.

- [ ] Login flow (simpan token, redirect)
- [ ] CRUD Produk end-to-end
- [ ] CRUD Pelanggan end-to-end
- [ ] CRUD Supplier end-to-end
- [ ] Manajemen User (admin only)
- [ ] Pengaturan toko

---

## Phase 8: Integrasi — Transaksi End-to-End

> Flow kasir lengkap dari pilih produk sampai cetak struk.

- [ ] Search/pilih produk → tambah ke keranjang
- [ ] Hitung total, diskon, metode bayar
- [ ] Submit transaksi → tampilkan struk
- [ ] Cetak struk (print-friendly view)
- [ ] Input pembelian stok dari supplier

---

## Phase 9: Dashboard & Laporan

> Data real-time di dashboard + export laporan.

- [ ] Dashboard: total penjualan hari ini
- [ ] Dashboard: total transaksi
- [ ] Dashboard: produk terlaris
- [ ] Dashboard: stok hampir habis
- [ ] Dashboard: grafik penjualan (7 hari / 30 hari)
- [ ] Laporan: filter & tampilkan tabel
- [ ] Laporan: export (PDF/CSV) — optional

---

## Phase 10: Polish & Deployment

> Final touch sebelum production.

- [ ] Error handling & loading states di FE
- [ ] Responsive design (tablet-friendly)
- [ ] Input validation di FE + BE
- [ ] Testing API (minimal happy path)
- [ ] Deploy backend (bisa di VPS / Railway / Render)
- [ ] Deploy frontend (static hosting / sama server)

---

## Prioritas Jika Waktu Terbatas

Kalau mau MVP (Minimum Viable Product) yang bisa langsung dipakai:

1. ✅ Phase 1 — Backend setup
2. ✅ Phase 2 — CRUD master data (minimal produk)
3. ✅ Phase 4 — Transaksi (core feature)
4. ✅ Phase 6 — Frontend kasir + produk
5. ✅ Phase 8 — Integrasi transaksi

Dengan 5 phase ini, toko sudah bisa:
- Input produk bunga
- Kasir bisa jual & cetak struk
- Stok berkurang otomatis

Sisanya (auth, laporan, dashboard, supplier) bisa dikembangkan bertahap.
