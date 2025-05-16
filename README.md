# RentBuild

Laravel + Vite + Tailwind CSS + Bootstrap

## Wymagania

- PHP
- Composer
- Node.js + npm
- PostgreSQL

---

## Instalacja

1. **Sklonuj repozytorium lub pobierz paczkę**
```bash
git clone https://github.com/uzytkownik/RentBuild.git
cd RentBuild
```
Zainstaluj zależności PHP

```bash
composer install
```

Zainstaluj zależności JS
```bash
npm install
```

Skopiuj plik .env
```bash
cp .env.example .env
```

Wygeneruj klucz aplikacji
```bash
php artisan key:generate
```
(Opcjonalnie) Ustaw dane do bazy w .env
(Opcjonalnie) Migracje
```bash
php artisan migrate
```

Uruchomienie środowiska developerskiego
Backend (Laravel):
```bash
php artisan serve
```

Frontend (Vite + CSS/JS):
```bash
npm run dev
```
