Muhasebe Panel

Küçük işletmeler için geliştirilmiş, kullanımı kolay bir ön muhasebe yönetim paneli.

 Özellikler
   Şirket kullanıcı ID'si ile güvenli giriş sistemi
   Dashboard — anlık gelir, gider ve bakiye özeti ile grafik görünümü
   Gelir & gider işlemleri ekleme, düzenleme ve silme
   Fatura oluşturma ve takibi (otomatik fatura numarası)
   Tarih aralığı ve kategori bazlı filtreleme
   Token tabanlı API güvenliği (Laravel Sanctum)


Teknolojiler

Backend-> Laravel 13, PHP 8.4
Frontend-> Vue 3 (Composition API), Vue Router
Veritabanı-> MySQL
Kimlik Doğrulama-> Laravel Sanctum
Stil-> Tailwind CSS
Build-> Vite

Kurulum

Gereksinimler
  PHP 8.2+
  Composer
  Node.js 18+
  MySQL


Adımlar

bash# Repoyu klonla
git clone https://github.com/irem0ckmk/Muhasebe-panel-.git
cd Muhasebe-panel-

# PHP bağımlılıklarını yükle
composer install

# Node bağımlılıklarını yükle
npm install

# .env dosyasını oluştur
cp .env.example .env
php artisan key:generate

# Veritabanı ayarlarını .env dosyasında düzenle
DB_DATABASE=muhasebe_panel
DB_USERNAME=root
DB_PASSWORD=şifren

# Veritabanını oluştur ve migrate et
php artisan migrate --seed

# Uygulamayı başlat
php artisan serve
npm run dev

Uygulama http://localhost:8000 adresinde çalışacaktır.

Proje Yapısı

├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php        # Giriş/çıkış işlemleri
│   │   ├── TransactionController.php # Gelir/gider CRUD
│   │   ├── InvoiceController.php     # Fatura CRUD
│   │   └── DashboardController.php   # Özet veriler
│   └── Models/
│       ├── User.php
│       ├── Transaction.php
│       └── Invoice.php
├── routes/
│   └── api.php                       # API route tanımları
└── resources/js/
    ├── views/                        # Vue sayfaları
    ├── components/                   # Sidebar vb. bileşenler
    └── api/                          # API istek fonksiyonları

Notlar
  Kullanıcılar şirket tarafından sisteme tanımlanır, kayıt formu yoktur.
  Tüm API endpoint'leri Sanctum token koruması altındadır.



Muğla Sıtkı Koçman Üniversitesi — Yazılım Mühendisliği Staj Projesi
