# Skenario Demo Perpustakaan

## Persiapan

1. Pastikan database sudah diisi dengan `php artisan migrate:fresh --seed`.
2. Jalankan `php artisan serve --port=8000` dan `php artisan serve --port=8011` di dua terminal.
3. Siapkan Postman dengan base URL `http://127.0.0.1:8000/api` serta header `Accept: application/json` dan `Content-Type: application/json`.

## Demo Aplikasi

1. Buka `http://127.0.0.1:8000/login`, masuk sebagai `admin@pens.ac.id` dengan password `password`, lalu tunjukkan nama pengguna di navbar.
2. Tampilkan Dashboard. Jelaskan tiga angka ringkasan yang diambil dari endpoint `GET /api/stats`.
3. Buka **Kategori**, tambahkan kategori demo yang belum ada, lalu tampilkan daftar kategori.
4. Buka **Buku**, tambahkan buku dengan kategori yang tersedia. Isi judul, penulis, penerbit, tahun terbit, ISBN, dan stok, lalu pastikan buku muncul di daftar.
5. Buka **Anggota**, tambahkan anggota dengan NIM, nama, email, nomor telepon, alamat, dan status aktif.
6. Buka **Peminjaman**, buat transaksi menggunakan anggota dan satu atau lebih buku, dengan tanggal kembali setelah tanggal pinjam.
7. Tampilkan daftar peminjaman dan tunjukkan status transaksi yang masih **Dipinjam**.
8. Proses pengembalian melalui aksi **Kembalikan**, lalu pastikan status berubah menjadi **Dikembalikan**.
9. Buka **Laporan**. Tunjukkan transaksi yang ditampilkan dari endpoint API dan navigasi halaman jika tersedia.

## Demo API di Postman

1. Kirim `GET {{baseUrl}}/stats`; tunjukkan jumlah buku, anggota, dan peminjaman aktif.
2. Kirim `GET {{baseUrl}}/books` dan `GET {{baseUrl}}/members` untuk melihat resource dan pagination.
3. Kirim `GET {{baseUrl}}/loans`; tunjukkan relasi anggota, petugas, dan daftar buku pada transaksi.
4. Untuk demo penambahan melalui API, kirim `POST {{baseUrl}}/books` dengan kategori yang sudah ada:

```json
{
  "judul": "Pemrograman Laravel",
  "penulis": "Fariq Zain",
  "penerbit": "Penerbit Kampus",
  "tahun_terbit": 2026,
  "isbn": "9786230000001",
  "stok": 3,
  "category_id": 1
}
```

5. Tunjukkan response JSON dan status sukses `201`. Gunakan ID hasil response atau ID data yang sudah tersedia untuk request lanjutan.

## Catatan

- Jalankan dua server sekaligus agar konsumsi API internal pada Dashboard dan Laporan berhasil.
- Seeder membuat akun demo dan data contoh; menjalankan `migrate:fresh --seed` akan menghapus lalu membangun ulang database.
- Endpoint API belum menggunakan autentikasi token. Jangan mengekspos endpoint tulis ke jaringan publik tanpa proteksi yang sesuai.