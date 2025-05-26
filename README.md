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
---

Zainstaluj zależności PHP

```bash
composer install
```

---

Zainstaluj zależności JS
```bash
npm install
```

---

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

---

wygeneruj klucz aplikacji:
```bash
php artisan key:generate
```

---

Migracje + Seedery
Wyczyść i stwórz bazę danych od nowa + załaduj dane startowe:
```bash
php artisan migrate:fresh --seed
```
albo osobno:
```bash
php artisan migrate:fresh
php artisan db:seed --class=SprzetSeeder
```

---

sprawdzenie czy migracja i seedery działają
```bash
php artisan tinker
> \App\Models\User::all();
```
tutaj wyświetla się baza

---

Uruchomienie środowiska developerskiego
Backend (Laravel):
```bash
php artisan serve
```

Frontend (Vite + CSS/JS):
```bash
npm run dev
```

---

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

## Automatyczne czyszczenie zakończonych promocji
Skrypt sprawdza wszystkie produkty (Equipment), które mają przypisaną promocję
(promotion_type = 'kategoria'), i usuwa dane promocji, jeśli termin zakończenia 
(end_datetime) już minął.

możesz ten skrypt uruchomić ręcznie
```bash
php artisan promotions:clear-expired
```

### Instrukcja jak dodać go żeby działał automatycznie
ChatGPT powiedział:
Świetnie — widzę Twoje zrzuty ekranu z konfiguracji Harmonogramu zadań Windows. Poniżej masz jasną i gotową instrukcję krok po kroku w .md, na podstawie Twoich ekranów — idealna do dokumentacji lub własnego użytku 👇

markdown
Kopiuj
Edytuj
# 🕒 Laravel Scheduler na Windows — Konfiguracja przez Harmonogram zadań

Ta instrukcja pokazuje, jak skonfigurować automatyczne uruchamianie `php artisan schedule:run` na systemie Windows za pomocą Harmonogramu zadań.

---

## ✅ Co to robi?

Scheduler Laravel uruchamia zaplanowane komendy (np. `promotions:clear-expired`) w określonych odstępach czasu. Harmonogram zadań Windows będzie uruchamiać `php artisan schedule:run` co 1 minutę — Laravel wewnętrznie sprawdzi, czy coś zaplanowano na ten moment.

---

## 🔧 KROK PO KROKU

### 1. Otwórz Harmonogram zadań
- Start → Wpisz: `Harmonogram zadań` → Enter

### 2. Kliknij: **Utwórz zadanie**
- **Nazwa**: `Laravel Scheduler`
- **Opis**: `Uruchamia artisan schedule:run co minutę`

### 3. Zakładka „Opcje Zabezpieczeń”
- Ustaw: `Uruchom tylko wtedy, gdy użytkownik jest zalogowany`
- ✅ Zaznacz „Uruchom z najwyższymi uprawnieniami”

---

### 4. Zakładka „Wyzwalacze”
- Kliknij **Nowy**
- **Rozpocznij zadanie**: „Zgodnie z harmonogramem”
- **Ustaw: Codziennie**
- Ustaw godzinę rozpoczęcia (np. teraz)
- Zaznacz: ✅ `Powtarzaj zadanie co: 5 minut`, `Czas trwania: 1 dzień`

---

### 5. Zakładka „Akcje”
- Kliknij **Nowa**
- **Akcja**: „Uruchom program”
- **Program/skrypt**: `C:\xampp\php\php.exe`
- **Dodaj argumenty**: `artisan schedule:run`
- **Rozpocznij w**: `{ścieżka projektu}`

---

### 6. Zakładka „Warunki”
- ✅ Odznacz „Uruchom zadanie tylko wtedy, gdy komputer jest bezczynny”
- ✅ Odznacz „Uruchom tylko wtedy, gdy komputer jest na zasilaniu sieciowym” *(jeśli chcesz, żeby działało też na baterii)*

---

### 7. Zakładka „Ustawienia”
- ✅ Zaznacz „Zezwalaj na uruchamianie zadania na żądanie”
- ✅ Zaznacz „Jeśli zadanie nie zakończy się na żądanie – wymuś zatrzymanie”
- Dla bezpieczeństwa: „Nie uruchamiaj nowego wystąpienia”
