# Şampiyonlar Ligi Simülasyonu

Bu proje, 4 futbol takımları arasında gerçekleşen bir ligin simülasyonu yapan web uygulamasıdır. Takımların gücüne, ev sahibi avantajına ve diğer faktörlere dayanarak maç sonuçlarını simüle eder ve şampiyonluk tahminleri sunar.

## Özellikler

- Otomatik 4 adet takım oluşturma
- Otomatik fikstür oluşturma
- Gerçekçi maç simülasyonları
- Hafta hafta turnuva simülasyonu
- Şampiyonluk tahmin sistemi
- Puan durumu tablosu


## Teknolojik Altyapı

- **Backend:** Laravel 11
- **Frontend:** Vue.js 3 + Inertia.js
- **Veritabanı:** MySQL / SQLite (unit testler için)
- **Containerization:** Docker
- **CSS Framework:** Bootstrap 5

## Proje Kurulum Adımları

Bu adımları izleyerek projeyi yerel geliştirme ortamınızda çalıştırabilirsiniz.

### 1. Projeyi Klonlama

```bash
cd champions-league-simulation
```

### 2. Docker ile Kurulum

```bash
# Container'ları oluşturup başlatma
docker-compose up -d --build

# Composer bağımlılıklarını yükleme
docker-compose exec app composer install

# .env dosyasını oluşturma
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate

# Veritabanını hazırlama
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 3. Frontend Kurulumu

```bash
# Node.js bağımlılıklarını yükleme
docker-compose exec node npm install

# Frontend'i geliştirme modunda başlatma
docker-compose exec node npm run dev
```

### 4. Uygulamaya Erişim

Kurulum tamamlandıktan sonra, tarayıcınızdan aşağıdaki URL'i açarak uygulamaya erişebilirsiniz:
```
http://localhost
```

## Proje Yapısı

- **app/Models:** Veritabanı modelleri (Team, Fixture, Game, ScoreBoard)
- **app/Services:** İş mantığı servisleri (FixtureGeneratorService, MatchSimulatorService vb.)
- **app/Http/Controllers:** Kontrol sınıfları
- **resources/js/Pages:** Vue.js bileşenleri
- **tests/:** Birim ve entegrasyon testleri

## Testlerin Çalıştırılması

```bash
docker-compose exec app php artisan test
```
