# Kalkulator Kriptografi Klasik

Kalkulator Kriptografi berbasis Web (PHP & Native JS + Tailwind CSS) untuk mendemonstrasikan proses enkripsi dan dekripsi menggunakan berbagai algoritma cipher klasik legendaris. Proyek ini dibangun sebagai sarana **edukasi dan pembelajaran akademis** di bidang kriptografi dan tidak ditujukan untuk pengamanan data sungguhan.

## 🌟 Fitur Utama

- **Dukungan 5 Algoritma Cipher Populer:**
  - **Vigenere Cipher:** Cipher substitusi polyalphabetic.
  - **Affine Cipher:** Cipher substitusi monoalphabetic matematika. 
  - **Playfair Cipher:** Cipher substitusi digram (pasangan huruf).
  - **Hill Cipher:** Cipher substitusi polyalphabetic berbasis aljabar linier (mendukung matriks kunci 2x2 dan 3x3).
  - **Enigma Machine Simulasi:** Simulasi mesin Enigma Perang Dunia II (mendukung 3 Rotor, Reflektor, dan Plugboard).
- **Proses Input Ganda:**
  - **Teks:** Ketik atau tempel teks langsung untuk dienkripsi/didekripsi.
  - **File:** Enkripsi file biner apa saja (gambar, dokumen, dll) dengan batas wajar 100 KB. Hasil di-export sebagai `.txt` dan dapat dikembalikan (dekripsi) ke wujud aslinya tanpa cacat.
- **Antarmuka Modern:** Desain antarmuka responsif dan bersih menggunakan *framework* Tailwind CSS.
- **Penyalinan Hasil Instan:** Salin teks ciphertext bebas repot.
- **Mitigasi Padding Biner:** Penghapusan karakter *padding* secara otomatis pada dekripsi file (menghindari file korup pada Hill/Playfair Cipher).

## 🚀 Instalasi & Konfigurasi Server

Aplikasi ini sangat ringan dan dibangun tanpa *framework wrapper* backend yang kompleks sehingga sangat mudah di-*deploy* ke server lokal Apache/Nginx secara langsung.

### Prasyarat:
- Web Server (Apache/Nginx dsb.) terinstall di lokal (misalnya XAMPP, MAMP, WAMP).
- **PHP** versi 7.4 (direkomendasikan versi 8.0 ke atas).

### Langkah-langkah:
1. Pindahkan/ekstrak folder repository `kriptografi` ini ke dalam direktori induk web _server_ lokal Anda (misalnya `htdocs` untuk XAMPP, `www` untuk WAMP).
2. Akses melalui browser pada alamat URL: `http://localhost/kriptografi` atau sesuaikan jika menggunakan *port* *custom* (`http://localhost:8080/kriptografi`).
3. **Praktik Keamanan:** Saat mendemonstrasinya via jaringn publik ke internet, akses layanan wajib menggunakan **HTTPS/SSL** agar parameter interaksi, *plaintext*, atau konfigurasi *keys* tidak di-*sniffing* oleh *man-in-the-middle attack*.

### Pengaturan `php.ini`
Sebagai lapisan perlindungan _server resource_, sangat direkomendasikan batas unggahan disetel ke nilai moderat untuk mencegah _Out of Memory_ (OOM) atau _Maximum Execution Time Exceeded_ akibat kelelahan utilitas iteratif algoritma sirkuit modifikasi biner (*string manipulation*) PHP terhadap file besar:
```ini
upload_max_filesize = 2M
post_max_size = 2M
display_errors = Off
```
_Catatan Performa: Program utama telah dilimitasi batasan maksimal besaran byte upload sebesar 100 KB baik dari JavaScript front-end dan pemeriksaan hard code backend `process.php` agar tetap aman._

## 📖 Panduan Penggunaan

### 1. Manipulasi Teks (Teks Biasa)
- Pilih **Algoritma Cipher** menggunakan *Tab* Navigasi di bagian atas.
- Pastikan interaksi mode input tipe ada di **Teks**.
- Masukkan kalimat/pesan ke area **Teks**.
- Tentukan parameter *Key / Kunci* sandi penyesuaian untuk Cipher yang ditentukan (Setiap Cipher mempunyai karakteristik yang unik untuk parameter enkripsi).
- Tekan tombol hitam **Enkripsi** untuk *scrambling* (*ciphertext*) atau tombol **Dekripsi** untuk menerjemahkan ke mode teks awalnya.

### 2. Manipulasi File Biner
- Bergeser mode tipe masukan berjenis **File** (bisa mode *drag & drop*).
- **Enkripsi File:** Pilih (atau lempar) berkas biner/multimedia apa saja (dokumen `.pdf`, foto `.png`/`.jpg` asal tidak lebih ukuran toleransi **100 KB**), lalu klik **Enkripsi**. Aplikasi akan mendekomposisi bentuk data mentah heksadesimalnya dan menghasilkan unduhan _ciphertext_ bernama spesifik berekstensi `.txt`. File text ini juga memuat *metadata original filename*.
- **Dekripsi File:** Masukkan secara seksama **file `.txt` dari hasil di atas**. Ketikkan nilai/parameter *Key* yang sama persis seperti sesi enkripsi sebelumnya. Tekan **Dekripsi**, browser akan sekejap langsung mendownload nama berkas biner sebagaimana ekspektasi nama, ukuran, tipe asalnya.

## ⚠️ Keamanan, Term of Service, & Disclaimer

Dengan melihat basis kode dan menjalankan perangkat web ini, Anda sadar dan menyetujui poin kapabilitas standar keamanan aplikasi berikut:

1. **Hanya untuk Edutech:** Sistem simulasi sandi kriptografi ini mengadaptasi Algoritma Klasik (*Classical Cryptography*). Tujuan pembuatannya murni 100% untuk presentasi dan demonstrasi pendidikan akademik. 
2. **Kekurangan Standar Keamanan Modern:** Vigenere, Hill, Affine, maupun mesin Enigma di era komputasi masa kini **DIANGGAP TIDAK MEMADAI**. Enkripsi tersebut **sangat sangat rentan** untuk dibongkar secara presisi melalui pola kriptanalisis mutakhir, frekuensi kemunculan abjad *(Frequency Analysis)*, atau sekadar injeksi eksploitasi serangan *brute force*.
3. **Data Pribadi / Konfidensialitas Institusi:** Mohon tidak mencoba bereksperimen mengamankan arsip kredensial yang nyata rahasia (catatan permodalan korporasi, dompet bitcoin, scan KTP/paspor, password *vault*) maupun file privat. 
4. **Pelepasan Tanggung Jawab Hukum (Disclaimer):** Penulis (pengembang utama sistem ini) secara tegas **melepaskan segala kompensasi liabilitas dan pertanggungjawaban hukum** yang timbul kelak sekiranya musibah terjadi atas penyalahgunaan utilitas. Resiko tersebut mencakup:
    - Kerusakan file eksistensi atau distorsi integritas (*corrupt*) impak kegagalan rasio dari residu blok algoritma *padding*.
    - Terjadi insiden kerugian pembobolan sandi *Data Breach*.
    - Miskonsepsi publik untuk kepentingan kerahasiaannya dengan alat demonstrasi awam ini.

## 🛠 Struktur Direktori (*Source Code Map*)
```text
/
├── index.php             # Utama halaman antarmuka Frontend. HTML / Tailwind CSS / Main JS logic form event handler
├── process.php           # API Router & Controller Backend PHP (Menangani logic upload, payload req, error-handling)
├── ciphers/              # Direktori spesifik kumpulan modul algoritma sandi kalkulasi matematika & maniputasi matrix
│   ├── affine.php
│   ├── enigma.php
│   ├── hill.php
│   ├── playfair.php
│   └── vigenere.php
└── README.md             # Dokumentasi Panduan Penggunaan ini
```
