## Instrukcja uruchomienia projektu Laravel + Tailwind + Bootstrap + PostgreSQL

### 1. Wymagania:
- PHP 8.1 lub wyższy
- Composer
- Node.js + npm
- PostgreSQL
- Opcjonalnie: Laravel CLI

---

### 2. Instalacja:
Wejdź do katalogu projektu
```bash
cd NAZWA_PROJEKTU
```
Zainstaluj zależności backendu
```bash
composer install
```
Zainstaluj zależności frontendu
```bash 
npm install
npm run dev
```
Skopiuj plik środowiskowy
```bash
cp .env.example .env
```

Ustaw dane dostępowe do PostgreSQL w .env
```bash
DB_DATABASE=twoja_baza
DB_USERNAME=twoj_user
DB_PASSWORD=twoje_haslo
```
# Wygeneruj klucz aplikacji
```bash
php artisan key:generate
```
# Wykonaj migracje bazy danych
```bash
php artisan migrate
```
# Uruchom lokalny serwer
```bash
php artisan serve
```


