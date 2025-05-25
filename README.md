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

utwórz plik .env kopiując plik .env.example i uzupełnij:
```bash
cp .env.example .env
```
w pliku:
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE={nazwa_bazy}
DB_USERNAME={użytkownik}
DB_PASSWORD={haslo}
```

wygeneruj klucz aplikacji:
```bash
php artisan key:generate
```

### Migracje + Seedery
Wyczyść i stwórz bazę danych od nowa + załaduj dane startowe:
```bash
php artisan migrate:fresh --seed
```
albo osobno:
```bash
php artisan migrate:fresh
php artisan db:seed --class=SprzetSeeder
```

sprawdzenie czy migracja i seedery działają
```bash
php artisan tinker
> \App\Models\Sprzet::all();
> \App\Models\User::all();
```
tutaj wyświetla się baza

### Rejestracja i weryfikacja konta
Po rejestracji zobaczysz ekran proszący o weryfikację adresu email.

Domyślnie Laravel wysyła link weryfikacyjny mailem, ale:
Możesz zobaczyć link weryfikacyjny w logach:
Zajrzyj do pliku:

```bash
storage/logs/laravel.log
```
Znajdziesz tam URL typu:

```bash
http://localhost:8000/email/verify/{user_id}/{token}
```
Kliknij go lub wklej do przeglądarki, by aktywować konto.

