# 🧾 FurniKasir

**FurniKasir** adalah aplikasi kasir sederhana berbasis web untuk warung furniture kecil.  
Aplikasi ini menggunakan pendekatan **kalkulator-first** dengan fitur **pilih furniture via geser (swipe)** serta **struk digital berbentuk gambar** yang dapat dikirim melalui WhatsApp.

---

## ✨ Fitur Utama
- 🧮 Tampilan kasir seperti kalkulator
- 🪑 Pilih furniture → harga otomatis masuk
- 🔄 Mode manual & menu furniture
- 🧾 Struk otomatis berbentuk **image (PNG)**
- 📤 Struk siap dikirim via WhatsApp
- 📜 Riwayat transaksi
- 🛠️ Manajemen data furniture
- 📱 Responsive & mobile-friendly

---

## 🧱 Teknologi yang Digunakan
- **Backend**: PHP Native
- **Database**: MySQL
- **Frontend**:
  - HTML5
  - Tailwind CSS (via CDN)
  - JavaScript (Vanilla)
- **Icon**: Font Awesome
- **Library tambahan**:
  - html2canvas (generate struk image)

---

## 🖥️ Screenshot
> (Tambahkan screenshot tampilan kasir & struk di sini)

---

## 🗂️ Struktur Project (Ringkas)
furnikasir/
├── assets/
│ ├── css/
│ └── js/
├── config/
│ └── database.php
├── kasir/
│ └── index.php
├── furniture/
│ └── manage.php
├── transaksi/
│ └── riwayat.php
├── struk/
│ └── generate.php
├── database/
│ └── furnikasir.sql
└── README.md

yaml
Salin kode

---

## 🗄️ Struktur Database
Database terdiri dari:
- `furniture`
- `transaksi`
- `transaksi_detail`

> File SQL tersedia di folder `/database`

---

## 🚀 Cara Menjalankan Project

1. Clone repository
   ```bash
   git clone https://github.com/username/furnikasir.git
Pindahkan ke folder web server
(contoh: htdocs untuk XAMPP)

Import database

Buka phpMyAdmin

Import file database/furnikasir.sql

Atur koneksi database

Edit file config/database.php

Jalankan di browser

arduino
Salin kode
http://localhost/furnikasir
📌 Catatan
Project ini dibuat untuk pembelajaran & portofolio

Cocok untuk warung furniture dan UMKM kecil

Belum menggunakan framework PHP

📄 Lisensi
Project ini menggunakan lisensi MIT License
Bebas digunakan untuk belajar dan dikembangkan lebih lanjut.
