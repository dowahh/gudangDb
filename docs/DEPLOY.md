# Panduan Deploy ke Wasmer Edge (Production) + TiDB Cloud

Panduan ngdeploy project ini (Laravel + TiDB Cloud) ke Wasmer Edge dari nol
sampai live. Santai aja, ikutin urut-urutan.

> **Jujur-jujuran dulu:** Wasmer Edge agak cepat ganti-gantinya. Detail yang
> aku tandai dengan ⚠️ *CEK* perlu kamu verifikasi di dokumentasi resmi
> Wasmer (https://docs.wasmer.io) sebelum dieksekusi, terutama:
> -Halaman utama: https://docs.wasmer.io/edge/guides/laravel
> -Config app: https://docs.wasmer.io/edge/configuration
> -Git deploy: https://docs.wasmer.io/edge/git
> -Secrets: https://docs.wasmer.io/edge/learn/secrets
> -Laravel + MySQL/TiDB: https://docs.wasmer.io/edge/guides/laravel/with-mysql
> Aku tidak mengarang detail yang gak aku yakin.

---

## 1. Prasyarat

- **Repo GitHub** project ini sudah ter-push (kamu yang commit).
- **Akun Wasmer**: daftar di https://wasmer.io/signup.
- **Wasmer CLI** terinstall di komputer: https://docs.wasmer.io/install
  (cek versi: `wasmer --version`).
- **Kredensial TiDB Cloud** production:
  - `DB_HOST` (gateway TiDB, mis. `gateway01.<region>.tidbcloud.com`)
  - `DB_PORT` (biasanya `4000`)
  - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - **Pakai user DB khusus production**, jangan user root/share yang sama
    dengan lokal. Buat di TiDB Cloud console → org/cluster → SQL user.
- PHP 8.3+ dan Composer di komputer lokal (untuk generate APP_KEY).

---

## 2. Langkah Deploy ke Wasmer Edge

### 2.1 Install & login

```bash
wasmer login
```

### 2.2 Siapkan file konfigurasi di repo

Wasmer deploy app PHP pakai **`wasmer.toml`** (manifest runtime) dan
**`app.yaml`** (config app Edge).

Contoh `wasmer.toml` di root project (⚠️ *CEK* versi PHP terbaru di
https://docs.wasmer.io/edge/guides/php — jangan asal copy versi lama):

```toml
[dependencies]
"php/php" = "=8.3.4"

[fs]
"/app/" = "."

[[command]]
name = "run"
module = "php/php:php"
runner = "wasi"

[command.annotations.wasi]
main-args = ["-t", "/app/public", "-S", "localhost:8080"]
```

Catatan: starter Laravel resmi Wasmer menjalankan start command seperti
`php -S 0.0.0.0:$PORT -t public`. ⚠️ *CEK* detail terbaik di
https://github.com/wasmer-examples/laravel-wasmer-starter.

Contoh `app.yaml` di root project (⚠️ *CEK* schema terbaru di
https://docs.wasmer.io/edge/configuration):

```yaml
kind: wasmer.io/App.v0
name: gudang-app          # harus unik, lowercase + angka
owner: username-wasmer-kamu
package: username-wasmer-kamu/gudang-app
# scaling:
#   mode: single_concurrency   # PHP sync biasanya butuh ini (⚠️ CEK)
```

### 2.3 Build aplikasi

Jalankan di komputer lokal **sebelum** deploy:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

**PENTING (khusus Wasmer):** hasil `config:cache` / `bootstrap/cache/*.php`
menyimpan path absolut. Kalau kamu jalankan `php artisan config:cache` di
Windows, path-nya `C:\...` gak akan cocok dengan `/app/...` di Wasmer.
⚠️ *CEK*: starter resmi Wasmer menyarankan rewrite path jadi `/app`:

```bash
sed -i "s|$PWD|/app|g" bootstrap/cache/*.php
```

(Gulp, ini di Windows coba pakai WSL/Git Bash, atau skip `config:cache`
dan biarkan config dibaca runtime — aman meski agak lambat.)

### 2.4 Test lokal dengan runtime Wasmer (opsional tapi recommended)

```bash
wasmer run .
```

Buka http://localhost:8080 — kalau muncul app kamu, berarti packaging-nya
udah bener.

### 2.5 Isi environment variable / secret

Ada 2 cara (⚠️ *CEK* UI terkini):

**A. Dashboard web** — buka app kamu di wasmer.io → **Settings** →
**Environment Vars** → *Add variable* → isi nama + value → **Save and
Redeploy**.

**B. CLI** — bulk dari file `.env` lokal:

```bash
wasmer app secrets create --from-file=.env-production
```

(atau satu-satu: `wasmer app secrets create APP_KEY "base64:..."`)
⚠️ *CEK*: variable rahasia (APP_KEY, DB_PASSWORD, SSL CA) masuk secret;
variable non-rahasia (APP_NAME dll.) bisa masuk `app.yaml` bagian `env:`.

### 2.6 Deploy

```bash
wasmer deploy
```

Atau deploy otomatis dari GitHub: connect repo → pilih branch production
(`main`) → setiap push ke branch itu otomatis redeploy. ⚠️ *CEK*: https://docs.wasmer.io/edge/git

Setelah selesai, Wasmer kasih URL live (mis. `https://<app>.wasmer.app`).

---

## 3. Tabel Environment Variable

Isi di dashboard Wasmer (Settings → Environment Vars) atau via CLI:

| Nama                     | Contoh nilai                              | Keterangan |
|--------------------------|-------------------------------------------|------------|
| APP_NAME                 | "Sistem Manajemen Inventaris Gudang"      | Nama app |
| APP_ENV                  | production                                | Wajib! Bikin Laravel ke mode prod (force https, dll.) |
| APP_KEY                  | base64:xxxx...                            | Lihat cara generate di bawah |
| APP_DEBUG                | false                                     | ⚠️ Wajib false, jangan expose error ke publik |
| APP_URL                  | https://<app>.wasmer.app                  | URL live kamu |
| APP_LOCALE               | id                                        | Bahasa Indonesia |
| DB_CONNECTION            | mysql                                     | TiDB kompatibel MySQL |
| DB_HOST                  | gateway01.<region>.tidbcloud.com          | Host TiDB Cloud |
| DB_PORT                  | 4000                                      | Port TiDB (bukan 3306) |
| DB_DATABASE              | <nama-db>                                 | Database production |
| DB_USERNAME              | <user>.root                               | User TiDB khusus production |
| DB_PASSWORD              | **********                                | Password DB |
| MYSQL_ATTR_SSL_CA        | /etc/ssl/certs/ca-certificates.crt        | Lihat section 4 |
| SESSION_DRIVER           | cookie                                    | Lihat catatan di bawah |
| SESSION_ENCRYPT          | true                                      | Enkripsi cookie session |
| CACHE_STORE              | file                                      | Lihat catatan di bawah |
| QUEUE_CONNECTION         | sync                                      | Tidak ada worker queue |
| LOG_CHANNEL              | stderr                                    | Log keluar di stream Wasmer, gampang di-debug |

**Cara buat APP_KEY** (jalankan di komputer lokal, copy hasilnya ke Wasmer):

```bash
php artisan key:generate --show
```

Copy output `base64:...` → isi ke env var `APP_KEY` di Wasmer.

**Kenapa `SESSION_DRIVER=cookie` dan `CACHE_STORE=file`?**
Karena database ini **nggak punya tabel `sessions` dan `cache`** (sengaja,
DB udah live & ter-seed, nggak mau nambah-tambah tabel). Session cookie
tidak butuh tabel db (data masuk cookie browser, dienkripsi). Cache file
disimpan di `storage/framework/cache`.

---

## 4. MYSQL_ATTR_SSL_CA di Wasmer

TiDB Cloud **wajib SSL**. `config/database.php` sudah membaca
`MYSQL_ATTR_SSL_CA` dari env.

- Di **Windows lokal**: path server CA beda, mis.
  `C:\xampp\php\extras\ssl\cacert.pem`.
- Di **Wasmer Edge (Linux/WASI)**: path CA mengikuti OS/build PHP yang
  dijalankan Wasmer, contoh umum di Linux:
  `/etc/ssl/certs/ca-certificates.crt`

⚠️ *CEK* apakah path `/etc/ssl/...` memang tersedia di runtime Wasmer PHP
(cara paling gampang: tanya support Wasmer di Discord atau coba deploy
dan lihat error koneksi MySQL). Kalau **path gak ada / gak ketahuan**,
alternatifnya:

1. Download sertifikat CA resmi TiDB/ISRG Root X1
   (https://letsencrypt.org/certificates/ → *ISRG Root X1*, format PEM),
   simpan sebagai file di repo (mis. `storage/certs/isrg-root-x1.pem`),
   lalu set:
   `MYSQL_ATTR_SSL_CA=/app/storage/certs/isrg-root-x1.pem`
   (path harus cocok dengan mount `fs` di `wasmer.toml`).
2. Atau sementara kosongi `MYSQL_ATTR_SSL_CA` (PDO MySQL biasanya akan
   pakai CA bawaan sistem kalau ada) ⚠️ *CEK* — perilaku ini tergantung
   build PHP di Wasmer; kalau masih error koneksi SSL pakai opsi 1.

---

## 5. Catatan Database Production

- **JANGAN `php artisan migrate:fresh` di production** — itu drop semua
  tabel + data live hilang. DB udah ter-seed.
- Kalau ada migration baru, jalankan sementara hanya sekali:
  ```bash
  php artisan migrate --force
  ```
  (di Wasmer agak ribet jalankan artisan, ⚠️ *CEK*: bisa via SSH app
  https://docs.wasmer.io/edge/learn/ssh/app_ssh, atau jalankan dari lokal
  langsung ke TiDB production pakai kredensial production).
- Ada protective guard di production sudah aktif di
  `AppServiceProvider` (`DB::prohibitDestructiveCommands`) — kalau kamu
  coba `migrate:fresh` / `db:wipe` di production, Laravel bakal nolak.

---

## 6. Verifikasi Setelah Live

1. **Buka dari HP pakai data seluler** (bukan wi-fi rumah):
   `https://<app>.wasmer.app` — pastikan gak ada error.
2. **Cek https**: address bar HP harus tampil gembok/https. Kalau browser
   warning "not secure" = mixed content, cek section 7.
3. **Cek CSS muncul** (bukan halaman polos): kalau polos → Vite manifest
   bermasalah, cek section 7.
4. **Login 3 akun** (admin, staff, viewer/user biasa) satu-satu:
   - Admin: bisa akses menu users, master data.
   - Staff: bisa transaksi tapi gak bisa kelola users.
   - User biasa: cuma lihat aja.
5. **Coba transaksi**: bikin stok masuk, bikin stok keluar, cek dashboard
   (grafik & kartu stok) berubah.
6. Cek URL `/up` — harus `200 OK` (health check Laravel).

---

## 7. Troubleshooting

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Error `SQLSTATE[42S02]: Table 'sessions' / 'cache' doesn't exist` | Env masih pakai `SESSION_DRIVER=database` atau `CACHE_STORE=database` | Ganti ke `SESSION_DRIVER=cookie`, `CACHE_STORE=file` di Wasmer (kita udah siapin di `.env.example`) |
| Halaman polos / `Vite manifest not found` | Build frontend belum dijalankan / `public/build` gak ikut ter-deploy | Jalankan `npm ci && npm run build` di lokal sebelum deploy, pastikan `public/build` ikut ke-deploy |
| `ERESOLVE` error pas `npm install` | Konflik versi node/vite package | Upgrade Node ke versi LTS terbaru. Kalau maksa: `npm install --legacy-peer-deps` (solusi darurat) |
| `SSL connection error` / `PDO connection failed` ke TiDB | Path `MYSQL_ATTR_SSL_CA` salah (beda Windows vs Linux) | Cek section 4. Windows beda path sama Linux. |
| Halaman blank 500 tanpa pesan | `APP_DEBUG=false` menyembunyikan error. Penyebab paling umum: `APP_KEY` kosong di production | Cek log di dashboard Wasmer, pastikan `APP_KEY` terisi (`php artisan key:generate --show` di lokal untuk bikin) |
| Icon/foto di-`http://` padahal halaman https (mixed content) | Asset pakai hardcoded `http://` atau scheme gak dipaksa https | Kita udah force https di production via `AppServiceProvider` + `trustProxies`. Kalau masih, cek blade template yang menulis path asset manual |
| `SQLSTATE[HY000] [2002] Connection refused` | `DB_HOST`/`DB_PORT` salah | TiDB: port `4000`, host gateway (bukan localhost) |
| Deploy error saat build | `node_modules` ikut ter-host | Jangan sertakan `node_modules` di package deploy; Wasmer build sendiri ⚠️ *CEK* cara build mereka |

---

## 8. Checklist Keamanan Sebelum Submit

- [ ] `APP_DEBUG=false` di Wasmer (jangan `true`, error akan bocor).
- [ ] **Ganti password akun admin live** jadi password kuat (min. 12
      huruf besar/kecil+angka+simbol — validation rule sudah aktif).
- [ ] Pastikan `.env` asli **tidak ter-push** ke GitHub (cek `git status`,
      `.env` harus tidak muncul di tracked).
- [ ] **User DB khusus production** di TiDB (bukan root user yang sama
      dengan lokal), dengan privilege minimal.
- [ ] `APP_ENV=production`.
- [ ] `trustProxies` + `URL::forceScheme('https')` sudah aktif (sudah
      dicommit di repo ini).
- [ ] Log jangan simpan secret: `LOG_CHANNEL=stderr` (bukan verbose).
- [ ] Setelah live, coba akses admin pakai akun non-admin → harus 403.
