# Panduan Deployment - TikTok Live Manager

Deploy ke: **https://live.groovy-media.com**

## Struktur Folder di Server

```
/home/diantor2/live.groovy-media.com/    ← Upload SEMUA file Laravel di sini
├── app/
├── bootstrap/
├── config/
├── database/
├── public/                               ← Document root (ubah di cPanel)
│   ├── index.php
│   ├── .htaccess
│   ├── build/
│   ├── css/
│   ├── js/
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/
├── artisan
├── .env
└── ...
```

---

## Step 1: Kosongkan Folder Subdomain

Hapus/backup semua file yang ada di `/home/diantor2/live.groovy-media.com/`

## Step 2: Upload Semua File Laravel

Upload SEMUA file dan folder dari project Laravel ke `/home/diantor2/live.groovy-media.com/`:

- `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `vendor/`
- `artisan`, `composer.json`, `composer.lock`
- **JANGAN upload**: `node_modules/`, `.git/`, `tests/`

**Tips**: ZIP semua → Upload → Extract

## Step 3: Ubah Document Root di cPanel

1. Pergi ke **Domains** → **Subdomains** atau **Domains**
2. Cari `live.groovy-media.com`
3. Ubah **Document Root** dari:
   - `/home/diantor2/live.groovy-media.com`
   - Menjadi: `/home/diantor2/live.groovy-media.com/public`
4. Save

## Step 4: Konfigurasi .env

1. Copy `.env.production` ke `/home/diantor2/live.groovy-media.com/.env`
2. Isi kredensial database

## Step 5: Setup di Terminal

```bash
cd ~/live.groovy-media.com
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

## Step 6: Test

Buka https://live.groovy-media.com

---

## Troubleshooting

**Error 500**: `cat ~/live.groovy-media.com/storage/logs/laravel.log`

**Blank page**: Set `APP_DEBUG=true` sementara
