# 📄 System Obiegu Dokumentów

> System workflow do zarządzania dokumentami z wielopoziomowym procesem akceptacji

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## ✨ Funkcjonalności

### Zarządzanie Dokumentami
- ✅ Tworzenie wniosków zakupowych z załącznikami
- ✅ Edycja i usuwanie szkiców
- ✅ Przesyłanie do akceptacji
- ✅ Historia zmian z pełnym audit trail
- ✅ Bezpieczne przechowywanie plików

### Proces Akceptacji
- ✅ Automatyczny routing do menedżera
- ✅ Warunkowa akceptacja finansowa (kwota >= 1000 PLN)
- ✅ Możliwość odrzucenia z komentarzem

### System Ról
- **Pracownik** - Tworzenie i edycja własnych wniosków
- **Menedżer** - Akceptacja wniosków podwładnych
- **Finanse** - Akceptacja wniosków wysokowartościowych
- **Administrator** - Pełny dostęp do systemu

### Bezpieczeństwo
- ✅ Policy-based authorization
- ✅ CSRF protection
- ✅ Secure file storage (private disk)
- ✅ Form request validation
- ✅ Mass assignment protection

## 🏗️ Architektura

### Wzorce Projektowe

```
┌─────────────────┐
│   Controllers   │ ← HTTP Layer
└────────┬────────┘
         │
┌────────▼────────┐
│    Services     │ ← Business Logic
└────────┬────────┘
         │
┌────────▼────────┐
│     Models      │ ← Data Layer
└────────┬────────┘
         │
┌────────▼────────┐
│    Database     │
└─────────────────┘
```

### Komponenty

- **Controllers** - Obsługa HTTP requests, delegacja do serwisów
- **Services** - Logika biznesowa (DocumentService, DocumentWorkflowService)
- **Models** - Eloquent models z business logic
- **Policies** - Authorization rules
- **Events & Listeners** - Asynchroniczne powiadomienia i audit
- **Enums** - Type-safe stany i role (PHP 8.1+)
- **Form Requests** - Walidacja danych wejściowych

## 📦 Instalacja

### 1. Klonowanie Repozytorium

```bash
git clone <repository-url>
cd obieg-dokumentow
```

### 2. Instalacja Zależności

```bash
# Instalacja zależności PHP
composer install

# Instalacja zależności JavaScript
npm install
```

### 3. Konfiguracja Środowiska

```bash
# Kopiowanie pliku .env
cp .env.example .env

# Generowanie klucza aplikacji
php artisan key:generate
```

## ⚙️ Konfiguracja

### Plik .env

Edytuj plik `.env` i skonfiguruj następujące parametry:

```env
# Aplikacja
APP_NAME="System Obiegu Dokumentów"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Baza Danych
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=document_workflow
DB_USERNAME=root
DB_PASSWORD=

# Queue (opcjonalnie)
QUEUE_CONNECTION=sync  # lub 'redis' dla produkcji



### Konfiguracja Storage

Dodaj w `config/filesystems.php` w sekcji `'disks'`:

```php
'private_documents' => [
    'driver' => 'local',
    'root' => storage_path('app/private_documents'),
    'visibility' => 'private',
],
```

### Migracje i Seeding

```bash
# Uruchomienie migracji
php artisan migrate

# Seeding bazy danych (użytkownicy testowi)
php artisan db:seed
```

### Utworzenie Katalogu dla Plików

```bash
# Utworzenie linku symbolicznego
php artisan storage:link

# Utworzenie katalogu dla dokumentów
mkdir -p storage/app/private_documents
chmod 755 storage/app/private_documents
```

## 🚀 Uruchomienie

### Tryb Deweloperski

#### Opcja 1: Standardowe Uruchomienie (Bez Dockera)

```bash
# Terminal 1 - Serwer PHP
php artisan serve

# Terminal 2 - Vite (assets)
npm run dev
```

Aplikacja dostępna pod: `http://localhost:8000`

**Uwaga:** Queue worker nie jest wymagany - listenery działają synchronicznie.

#### Opcja 2: Laravel Sail (Docker) - ZALECANE

##### Pierwsza instalacja:

```bash
# 1. Zainstaluj Sail
composer require laravel/sail --dev

# 2. Zainstaluj konfigurację Sail (wybierz: mysql, redis)
php artisan sail:install

# 3. Uruchom kontenery Docker
docker-compose up -d

# 4. Sprawdź czy kontenery działają
docker-compose ps

# 5. Zainstaluj zależności NPM w kontenerze
docker-compose exec laravel.test npm install

# 6. Uruchom migracje i seeding
docker-compose exec laravel.test php artisan migrate --seed

# 7. Wyczyść cache
docker-compose exec laravel.test php artisan config:clear
docker-compose exec laravel.test php artisan event:clear

# 8. Uruchom Vite (w osobnym terminalu)
docker-compose exec laravel.test npm run dev
```


### Użytkownicy Testowi

Po uruchomieniu `php artisan db:seed` dostępni są następujący użytkownicy:

| Email | Hasło | Rola | Opis |
|-------|-------|------|------|
| admin@example.com | password | Admin | Pełny dostęp |
| manager@example.com | password | Manager | Akceptacja wniosków |
| finance@example.com | password | Finance | Akceptacja finansowa |
| user1@example.com | password | User | Pracownik (podwładny managera) |
| user2@example.com | password | User | Pracownik (podwładny managera) |
| user3@example.com | password | User | Pracownik (bez managera) |

## 📁 Struktura Projektu

```
obieg-dokumentow/
├── app/
│   ├── Enums/
│   │   ├── DocumentStatus.php      # Statusy dokumentów
│   │   └── UserRole.php            # Role użytkowników
│   ├── Events/
│   │   └── DocumentStatusChanged.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DocumentController.php
│   │   │   ├── DocumentApprovalController.php
│   │   │   └── DocumentFileController.php
│   │   └── Requests/
│   │       ├── StoreDocumentRequest.php
│   │       ├── UpdateDocumentRequest.php
│   │       └── ApprovalRequest.php
│   ├── Listeners/
│   │   ├── LogDocumentHistory.php
│   │   └── SendApprovalNotifications.php
│   ├── Models/
│   │   ├── Document.php
│   │   ├── DocumentHistory.php
│   │   └── User.php
│   ├── Notifications/
│   │   ├── DocumentPendingApproval.php
│   │   ├── DocumentApproved.php
│   │   └── DocumentRejected.php
│   ├── Policies/
│   │   └── DocumentPolicy.php
│   └── Services/
│       ├── DocumentService.php
│       └── DocumentWorkflowService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       └── documents/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
└── routes/
    └── web.php
```

## 🔄 Workflow Dokumentów

### Diagram Stanów

```
┌─────────┐
│  DRAFT  │
└────┬────┘
     │ submit()
     ▼
┌──────────────────────────┐
│ PENDING_MANAGER_APPROVAL │
└────┬─────────────────────┘
     │
     ├─ approve() [amount < 1000] ──► APPROVED
     │
     ├─ approve() [amount >= 1000] ─┐
     │                               ▼
     │                    ┌──────────────────────────┐
     │                    │ PENDING_FINANCE_APPROVAL │
     │                    └────┬─────────────────────┘
     │                         │
     │                         ├─ approve() ──► APPROVED
     │                         │
     │                         └─ reject() ───► REJECTED
     │
     └─ reject() ──────────────────────────► REJECTED
```

### Reguły Biznesowe

1. **Tworzenie** - Każdy użytkownik może utworzyć dokument w statusie DRAFT
2. **Edycja** - Tylko autor może edytować dokument w statusie DRAFT
3. **Wysyłka** - Użytkownik musi mieć przypisanego menedżera
4. **Akceptacja Menedżera**:
   - Kwota < 1000 PLN → APPROVED
   - Kwota >= 1000 PLN → PENDING_FINANCE_APPROVAL
5. **Akceptacja Finansowa** - Wymagana dla kwot >= 1000 PLN
6. **Odrzucenie** - Wymaga komentarza

## 👥 Role Użytkowników

### Pracownik (USER)

**Uprawnienia:**
- Tworzenie nowych wniosków
- Edycja własnych szkiców
- Usuwanie własnych szkiców
- Wysyłanie do akceptacji
- Przeglądanie własnych dokumentów

### Menedżer (MANAGER)

**Uprawnienia:**
- Wszystkie uprawnienia Pracownika
- Akceptacja wniosków podwładnych
- Odrzucanie wniosków podwładnych
- Przeglądanie wniosków podwładnych

### Finanse (FINANCE)

**Uprawnienia:**
- Akceptacja wniosków >= 1000 PLN
- Odrzucanie wniosków >= 1000 PLN
- Przeglądanie wszystkich wniosków finansowych

### Administrator (ADMIN)

**Uprawnienia:**
- Pełny dostęp do wszystkich dokumentów
- Zarządzanie użytkownikami
- Dostęp do wszystkich funkcji systemu


