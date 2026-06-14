# Accounting & Cashflow System

Accounting System adalah aplikasi pembukuan keuangan modern dan responsif yang dirancang khusus untuk mengelola arus kas bimbingan belajar (bimbel). Sistem ini menggunakan prinsip akuntansi double-entry (penjurnalan otomatis) di latar belakang sehingga memudahkan Bendahara untuk mencatat transaksi harian tanpa perlu keahlian akuntansi mendalam.

---

## 🌟 Fitur Utama

### 1. Menu Bendahara (Input Sederhana)
* **📥 Form Pemasukan SPP**: Pencatatan cepat pembayaran SPP bulanan siswa aktif (Reguler/Intensif) dengan metode pembayaran Kas Kecil atau Transfer Bank. Menjurnal otomatis ke kas/bank dan pendapatan SPP.
* **📤 Form Pengeluaran**: Pencatatan biaya operasional harian bimbel (Gaji, Listrik, Internet, Konsumsi, dll) dilengkapi dengan upload foto nota belanja/kuitansi fisik.

### 2. Menu Akuntansi (Laporan Keuangan Otomatis)
* **📑 Jurnal Umum / General Ledger (GL)**: Daftar riwayat jurnal double-entry otomatis yang dapat difilter berdasarkan bulan atau rentang tanggal dan diekspor langsung ke spreadsheet Excel.
* **⚖️ Neraca Saldo / Trial Balance (TB)**: Perhitungan saldo akhir otomatis untuk tiap akun CoA dengan indikator status penyeimbang otomatis (*Balance Warning* jika debit & kredit tidak balance).
* **📈 Laba Rugi / Profit & Loss (LR)**: Perhitungan untung/rugi bersih berjalan secara real-time berdasarkan total pendapatan operasional dikurangi beban usaha.
* **💸 Arus Kas / Cash & Bank (LAK)**: Modul pelacakan mutasi dan rekonsiliasi dana riil pada dompet *Kas Kecil H.O* dan *Dana di Bank*.

### 3. Manajemen Data Master
* **👥 Manajemen Siswa**: CRUD profil siswa aktif/non-aktif, pencatatan nomor HP/WhatsApp, dan kategori program kelas (Reguler/Intensif).
* **🗂️ Chart of Accounts (CoA)**: Stuktur klasifikasi akun keuangan bimbel (Kepala 1: Aset, Kepala 5: Beban, dll) dengan proteksi penghapusan untuk akun yang memiliki histori transaksi.

### 4. Audit & Keamanan Sistem
* **🔐 Multi-User Authentication**: Akses setara bagi **Bendahara 1** dan **Bendahara 2** tanpa batasan peran (role).
* **📱 Login Device Detection & Audit Logs**: Mencatat detail waktu login/logout, alamat IP, sistem operasi, peramban (browser), tipe perangkat (Desktop/Mobile), serta riwayat tindakan administratif (tulis/hapus data).
* **✉️ Password Reset Routing**: Fitur lupa password dengan verifikasi pengiriman tautan reset ke email cadangan (backup email) terdaftar.

### 5. 💡 Interactive Tour Guide Onboarding
* Fitur panduan interaktif step-by-step yang responsif menggunakan *pure vanilla JavaScript*.
* Menampilkan overlay kartu tooltip penjelasan dan menyorot elemen penting di halaman yang sedang dibuka menggunakan animasi lingkaran berdenyut (*pulsating highlight ring*).

---

## 🛠️ Teknologi & Stack
* **Framework**: Laravel 10/11 (PHP 8.x)
* **Frontend**: HTML5, Vanilla JavaScript, Tailwind CSS (Vite compiler)
* **Database**: SQLite (Local Database)
* **Visualisasi**: Chart.js (Grafik Tren Arus Kas Bulanan)
* **Ekspor Laporan**: Maatwebsite Excel

---

## 🚀 Panduan Instalasi Lokal

### Prerequisites
Pastikan perangkat Anda sudah terinstal **PHP >= 8.1**, **Composer**, **Node.js**, **NPM**, dan **SQLite**.

1. **Clone dan Masuk ke Direktori Project**
   ```bash
   git clone <repository-url> cashflowBimbel
   cd cashflowBimbel
   ```

2. **Instal Dependensi PHP (Composer) & Node (NPM)**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment File**
   Salin berkas `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Konfigurasi Database SQLite**
   Pastikan konfigurasi database di file `.env` mengarah ke SQLite:
   ```env
   DB_CONNECTION=sqlite
   # Kosongkan DB_DATABASE jika ingin menggunakan database default database/database.sqlite
   ```
   Buat file database kosong (jika belum ada):
   ```bash
   touch database/database.sqlite
   ```

6. **Migrasi Database & Seeding Data Awal**
   Jalankan perintah berikut untuk membuat tabel dan memasukkan data akun default, data siswa contoh, dan kredensial Bendahara:
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compile Frontend Assets**
   * Untuk pengembangan lokal (Hot Reloading):
     ```bash
     npm run dev
     ```
   * Untuk build produksi:
     ```bash
     npm run build
     ```

8. **Jalankan Local Server**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di alamat [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 🔑 Kredensial Login Default (Hasil Seeder)

Anda dapat menggunakan salah satu dari akun bendahara berikut untuk masuk ke sistem:

* **Bendahara 1**:
  * **Email**: `bendahara1@fikra.com`
  * **Password**: `password`
  * **Email Cadangan**: `backup1@example.com`

* **Bendahara 2**:
  * **Email**: `bendahara2@fikra.com`
  * **Password**: `password`
  * **Email Cadangan**: `backup2@example.com`

---

## 🧪 Pengujian & Verifikasi (Testing)

Sistem ini dilengkapi dengan unit dan feature testing (menggunakan Pest PHP Framework) untuk memvalidasi alur otentikasi, audit log, reset password, transaksi kas, dan pembuatan jurnal keuangan.

Jalankan seluruh pengujian dengan perintah:
```bash
php artisan test
```
