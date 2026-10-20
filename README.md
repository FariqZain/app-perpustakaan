# API Perpustakaan

REST API ini menyediakan data buku, anggota, peminjaman, dan statistik dalam JSON. Endpoint API saat ini bersifat publik dan belum memakai autentikasi token.

## Menjalankan dan Menguji

Jalankan aplikasi Laravel, lalu gunakan base URL `http://127.0.0.1:8000/api` (sesuaikan port jika berbeda). Sertakan header berikut pada request:

```http
Accept: application/json
Content-Type: application/json
```

Verifikasi route dan jalankan tes endpoint:

```bash
php artisan route:list --path=api
php artisan test --compact tests/Feature/ApiEndpointsTest.php
```

## Endpoint

Respons berikut adalah cuplikan struktur resource; isi record, timestamp, dan metadata pagination mengikuti database saat request dijalankan. Cuplikan ini bukan salinan response dari Postman.

| Method | URL | Body | Response sukses | Status sukses / gagal |
|---|---|---|---|---|
| GET | `/api/books` | - | `200`: `{ "data": [ { "id": ..., "judul": ..., "isbn": ..., "category": { "id": ..., "nama_kategori": ... } } ], "links": {...}, "meta": {...} }` | `200` |
| GET | `/api/books/{id}` | - | `200`: `{ "data": { "id": ..., "judul": ..., "category": { "id": ..., "nama_kategori": ... } } }` | `200`; `404` jika buku tidak ditemukan |
| POST | `/api/books` | `{"judul":"...","penulis":"...","penerbit":"...","tahun_terbit":2020,"isbn":"...","stok":1,"category_id":1}` | `201`: `{ "data": { "id": ..., "judul": ..., "isbn": ..., "category": {...} } }` | `201`; `422` jika validasi gagal |
| PUT | `/api/books/{id}` | Field buku yang sama seperti `POST /api/books` | `200`: `{ "data": { "id": ..., "judul": ..., "category": {...} } }` | `200`; `404` jika buku tidak ditemukan; `422` jika validasi gagal |
| DELETE | `/api/books/{id}` | - | `200`: `{"message":"Buku berhasil dihapus."}` | `200`; `404` jika buku tidak ditemukan |
| GET | `/api/members` | - | `200`: `{ "data": [ { "id": ..., "nama": ..., "nim": ..., "email": ..., "status": ... } ], "links": {...}, "meta": {...} }` | `200` |
| GET | `/api/members/{id}` | - | `200`: `{ "data": { "id": ..., "nama": ..., "nim": ..., "email": ..., "status": ... } }` | `200`; `404` jika anggota tidak ditemukan |
| GET | `/api/loans` | - | `200`: `{ "data": [ { "id": ..., "status": ..., "member": {...}, "petugas": {...}, "books": [...] } ], "links": {...}, "meta": {...} }` | `200` |
| POST | `/api/loans` | `{"member_id":1,"user_id":1,"tanggal_pinjam":"2026-10-20","tanggal_kembali":"2026-10-27","book_ids":[1]}` | `201`: `{ "data": { "id": ..., "status": "dipinjam", "member": {...}, "petugas": {...}, "books": [...] } }` | `201`; `422` jika validasi gagal |
| PUT | `/api/loans/{id}/kembalikan` | - | `200`: `{ "data": { "id": ..., "status": "dikembalikan", "tanggal_dikembalikan": "YYYY-MM-DD", ... } }` | `200`; `404` jika peminjaman tidak ditemukan |
| GET | `/api/stats` | - | `200`: `{"total_buku":0,"total_anggota":0,"peminjaman_aktif":0}` | `200` |

`GET /api/books` mendukung filter opsional `?judul=` (pencarian sebagian) dan `?category_id=` (pencocokan kategori). Endpoint list buku, anggota, dan peminjaman menggunakan pagination 10 record per halaman dengan `data`, `links`, dan `meta`.

Resource buku memuat `id`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `isbn`, `stok`, `sampul`, timestamp, serta kategori saat relasinya dimuat. Resource anggota memuat field anggota dan timestamp. Resource peminjaman memuat tanggal/status, anggota, `petugas` (id dan nama), serta buku-buku dalam transaksi.

Jika validasi gagal, Laravel mengembalikan HTTP `422` dengan format JSON berikut:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "judul": ["The judul field is required."]
  }
}
```

Pesan validasi dapat berbeda sesuai field yang gagal dan bahasa konfigurasi aplikasi.