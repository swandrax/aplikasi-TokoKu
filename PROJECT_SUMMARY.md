# Kesimpulan Proyek: TokoKu (Sistem POS & E-Commerce Terintegrasi AI)

Dokumen ini merangkum keseluruhan sistem, arsitektur, teknologi, dan metode yang digunakan dalam pengembangan proyek **TokoKu**.

## 1. Tech Stack (Teknologi yang Digunakan)
- **Bahasa Pemrograman:** PHP 8.4, JavaScript (Vanilla)
- **Framework Backend:** Laravel 11 / 12
- **Framework Frontend (Styling):** Tailwind CSS v4
- **Asset Bundler:** Vite
- **Database:** MySQL / SQLite (melalui Laravel Eloquent ORM)
- **AI Integration:** Groq API (LLM) untuk layanan KikiBot

## 2. Arsitektur Sistem
Proyek ini mengadopsi arsitektur **MVC (Model-View-Controller)** bawaan Laravel dengan pemisahan wilayah kerja (Workspace) berdasarkan sistem otorisasi **RBAC (Role-Based Access Control)** yang ketat:
- **Admin Workspace:** Mengelola Master Data, Stok (CRUD Produk & Chatbot Prompts), dan Laporan keseluruhan.
- **Kasir Workspace:** Berfokus pada sistem POS (Point of Sale) untuk transaksi offline/di tempat.
- **Pembeli Workspace:** Halaman E-Commerce (Katalog Produk) untuk pelanggan berbelanja secara online.

## 3. Fitur Utama & Sistem
1. **Sistem Inventaris & Penjualan (POS & E-Commerce):**
   - Transaksi hibrida (Offline oleh Kasir dan Online oleh Pembeli).
   - Pengelolaan batch stok terpusat.
2. **Autentikasi Aman:**
   - Registrasi dengan dukungan validasi peran.
   - Verifikasi ganda menggunakan **Email OTP (One-Time Password)** dengan batasan kedaluwarsa waktu.
3. **KikiBot (AI Chatbot Assistant):**
   - Integrasi AI cerdas menggunakan Groq API.
   - *Quick Prompts* interaktif yang dinamis (dapat diatur (CRUD) oleh admin dari *database*).
   - Mampu memberikan rekomendasi produk dan menyapa pengguna berdasarkan status login mereka (Guest vs User).
4. **Simulasi Payment Gateway:**
   - Mendukung berbagai pratinjau metode pembayaran: Tunai (Cash), Debit, Transfer Virtual Account, dan Barcode Digital.

## 4. Algoritma & Metode
1. **Algoritma FIFO (First In First Out) pada Manajemen Stok:**
   Sistem stok menggunakan pendekatan angkatan (Batch) di mana barang yang pertama kali masuk (dengan `batch_date` paling lama) akan dikurangi pertama kali saat terjadi transaksi pembelian. Ini menjaga akurasi Harga Pokok Penjualan (HPP) barang.
2. **Sistem Skoring (Reward/Penalty) Model AI:**
   - Algoritma pencatatan interaksi Chatbot menerapkan parameter skoring dinamis untuk evaluasi performa respon.
   - *Positive Reward* (+0.988796) jika respons AI dianggap baik dan membantu.
   - *Negative Penalty* (-0.34551) sebagai *feedback loop* pemrosesan belakang layar (*background processing*) apabila interaksi AI kurang memuaskan atau *user* tidak setuju dengan layanan.
3. **Metode Asynchronous (AJAX):**
   - Menggunakan *AJAX Fetch API* untuk fitur *Load More* pada katalog produk, mengurangi proses muat ulang halaman secara penuh (*full-page reload*) demi efisiensi waktu muat (UX).
4. **Soft Deletes:**
   - Mencegah hilangnya data histori (transaksi/produk) dengan mengaplikasikan metode penghapusan semu di mana data hanya ditandai "terhapus" namun tetap ada secara fisik di database.
