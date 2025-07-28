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

**Backend (Laravel):**
```bash
php artisan serve
```

**Frontend (Vue.js + Vite):**
```bash
npm run dev
```

**Build per Produzione:**
```bash
npm run build
```

## Architettura Frontend

Il frontend è sviluppato con **Vue.js 3** e utilizza le seguenti tecnologie:

### Stack Tecnologico Frontend
- **Vue.js 3**: Framework JavaScript reattivo
- **Tailwind CSS**: Framework CSS utility-first
- **Axios**: Client HTTP per API calls
- **Vite**: Build tool e dev server
- **Laravel Vite Plugin**: Integrazione con Laravel

### Struttura Componenti
```
resources/js/
├── app.js              # Entry point Vue.js
├── bootstrap.js        # Configurazione Axios
└── components/
    ├── App.vue         # Componente principale
    └── FeatureCard.vue # Componente riutilizzabile
```

### Configurazione Sviluppo
- **Hot Reload**: Attivo durante lo sviluppo
- **CSS Preprocessing**: Tailwind CSS integrato
- **Asset Bundling**: Gestito da Vite
- **API Integration**: Axios preconfigurato per Laravel

## Struttura del Progetto

Il progetto è organizzato secondo la metodologia Agile in Sprint:

- **Sprint 0**: Setup e Fondamenta ✅
- **Sprint 1**: Core Backend e Autenticazione
- **Sprint 2**: Gestione Documenti e Materiali  
- **Sprint 3**: Barcode, Scansione e Funzionalità Avanzate
- **Sprint 4**: Refactoring, Test e Deploy

## Git e Strategia di Branching

Il progetto utilizza **GitFlow** come strategia di branching. Per dettagli completi, consultare [GITFLOW.md](GITFLOW.md).

### Branch Principali
- `main`: Codice di produzione stabile
- `develop`: Branch di integrazione per lo sviluppo

### Workflow Rapido
```bash
# Creare feature branch
git checkout develop
git checkout -b feature/s[sprint]-[task-id]-[description]

# Sviluppo e commit
git add .
git commit -m "feat(scope): description"

# Push e Pull Request
git push origin feature/s[sprint]-[task-id]-[description]
```

### Convenzioni Commit
- `feat`: Nuova funzionalità
- `fix`: Correzione bug
- `docs`: Documentazione
- `test`: Test
- `refactor`: Refactoring

## Tecnologie Utilizzate

- **Backend**: Laravel 12
- **Database**: SQLite (sviluppo) / MySQL 8.0 (produzione)
- **Frontend**: Vue.js 3 + Tailwind CSS
- **Build Tool**: Vite
- **HTTP Client**: Axios
- **Autenticazione**: Laravel Sanctum
- **Testing**: PHPUnit
- **Version Control**: Git con GitFlow

## Team di Sviluppo

Progetto sviluppato per DTT by Logix seguendo le specifiche del documento di progettazione esecutiva.
