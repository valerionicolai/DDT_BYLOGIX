# DTT by Logix - Material Management System

Applicazione web per la gestione del materiale in ingresso sviluppata con Laravel 12.

## Requisiti di Sistema

- PHP 8.2+
- Composer
- SQLite (per sviluppo locale) / MySQL 8.0+ (per produzione)
- Node.js 18+ (per il frontend)

## Setup del Progetto

### 1. Installazione Dipendenze
```bash
composer install
npm install
```

### 2. Configurazione Database
**Sviluppo Locale (SQLite):**
Il database SQLite è già configurato e pronto all'uso.

**Produzione (MySQL):**
1. Creare un database MySQL chiamato `dttbylogix_db`
2. Configurare le credenziali nel file `.env`
3. Cambiare `DB_CONNECTION=sqlite` in `DB_CONNECTION=mysql`

Eseguire le migrazioni:
```bash
php artisan migrate
```

### 3. Avvio del Server di Sviluppo
```bash
php artisan serve
```

## Struttura del Progetto

Il progetto è organizzato secondo la metodologia Agile in Sprint:

- **Sprint 0**: Setup e Fondamenta ✅
- **Sprint 1**: Core Backend e Autenticazione
- **Sprint 2**: Gestione Documenti e Materiali  
- **Sprint 3**: Barcode, Scansione e Funzionalità Avanzate
- **Sprint 4**: Refactoring, Test e Deploy

## Tecnologie Utilizzate

- **Backend**: Laravel 12
- **Database**: SQLite (sviluppo) / MySQL 8.0 (produzione)
- **Frontend**: Blade Templates + Vite
- **Autenticazione**: Laravel Sanctum
- **Testing**: PHPUnit

## Team di Sviluppo

Progetto sviluppato per DTT by Logix seguendo le specifiche del documento di progettazione esecutiva.
