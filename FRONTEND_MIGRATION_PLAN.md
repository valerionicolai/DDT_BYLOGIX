# 🚀 Frontend Migration Plan: Vue.js → Laravel Filament + Livewire

## 📊 Implementation Status Summary

### ✅ Completed Sprints
- **Sprint 1: Foundation & Setup** - 100% Complete
- **Sprint 2: Dashboard & Statistics** - 100% Complete  
- **Sprint 3: Project Management** - 100% Complete
- **Sprint 4: Client Management** - 100% Complete
- **Sprint 5: Material Types Management** - 100% Complete
- **Sprint 6: Advanced Features & Polish** - 100% Complete

### 🔄 In Progress
- **Sprint 7: Testing & Deployment** - 90% Complete (Testing phase ongoing)

### 📋 Planned Sprints
- **Sprint 8: Barcode Management Foundation** - 0% Complete (Planned)
- **Sprint 9: Barcode Generation & Scanning** - 0% Complete (Planned)
- **Sprint 10: Material Tracking & Traceability** - 0% Complete (Planned)
- **Sprint 11: Barcode System Integration** - 0% Complete (Planned)

### 📈 Overall Progress: 75% Complete (with new barcode requirements)

---

## Overview

This document outlines the complete migration strategy from the current Vue.js frontend to a Laravel Filament + Livewire + Alpine.js stack. The migration will maintain all existing functionality while improving code maintainability and reducing complexity.

## 📋 Technology Stack

### Frontend Technologies

#### Build Tools
- **Vite 6.3.5** - Modern build tool and dev server
  - Hot module replacement (HMR)
  - Asset bundling and optimization
  - TypeScript support
  - CSS preprocessing

#### CSS Framework
- **Tailwind CSS 3.4.17** - Utility-first CSS framework
  - **@tailwindcss/forms** (^0.5.10) - Form styling utilities
  - **@tailwindcss/typography** (^0.5.16) - Typography utilities
  - **@tailwindcss/vite** (^4.0.0) - Vite integration
  - **PostCSS** (^8.5.6) - CSS processing
  - **postcss-nesting** (^13.0.2) - CSS nesting support
  - **Autoprefixer** (^10.4.21) - Automatic vendor prefixes

#### JavaScript Framework
- **Alpine.js 3.14.9** - Lightweight JavaScript framework
  - Reactive data binding
  - Component-based architecture
  - Minimal learning curve
  - Perfect integration with Laravel/Livewire

#### Backend Integration
- **Laravel Filament** - Admin panel framework
- **Livewire** - Full-stack framework for Laravel
- **Existing API** - No changes to backend API endpoints

## 🎯 Migration Objectives

- ✅ Maintain all existing functionality
- ✅ Improve code maintainability
- ✅ Reduce frontend complexity
- ✅ Keep existing backend API unchanged
- ✅ Enhance developer experience
- ✅ Improve performance and user experience

## 📅 Sprint Planning

### **Sprint 0: Project Setup & Foundation** (3-5 days)

#### **Epic: Environment Setup**

**Task 1.1: Install Laravel Filament** ✅
- [x] Run `composer require filament/filament`
- [x] Run `php artisan filament:install --panels`
- [x] Configure basic panel settings
- [x] Setup admin user access

**Task 1.2: Install and configure Livewire** ✅
- [x] Run `composer require livewire/livewire`
- [x] Publish Livewire assets: `php artisan livewire:publish --config`
- [x] Configure Livewire in `config/livewire.php`
- [x] Setup Livewire middleware

**Task 1.3: Setup Alpine.js integration** ✅
- [x] Install Alpine.js: `npm install alpinejs@3.14.9`
- [x] Configure Alpine.js with Livewire
- [x] Update Vite configuration for Alpine.js
- [x] Test Alpine.js + Livewire integration

**Task 1.4: Configure Tailwind CSS for Filament** ✅
- [x] Update `tailwind.config.js` for Filament compatibility
- [x] Install Tailwind plugins:
  - `npm install @tailwindcss/forms@^0.5.10`
  - `npm install @tailwindcss/typography@^0.5.16`
  - `npm install @tailwindcss/vite@^4.0.0`
- [x] Configure PostCSS: `npm install postcss@^8.5.6 postcss-nesting@^13.0.2 autoprefixer@^10.4.21`
- [x] Test Tailwind compilation

**Task 1.5: Update Vite configuration** ✅
- [x] Configure Vite 6.3.5 for Filament/Livewire
- [x] Setup HMR for Livewire components
- [x] Configure asset bundling for new stack
- [x] Test build process

**Deliverables:**
- ✅ Working Filament admin panel
- ✅ Livewire components rendering
- ✅ Alpine.js integration functional
- ✅ Tailwind CSS properly configured
- ✅ Vite build process working

---

### **Sprint 1: Authentication & User Management** (5-7 days)

#### **Epic: User Authentication System**

**Task 2.1: Configure Filament authentication** ✅
- [x] Setup Filament admin panel authentication
- [x] Configure user roles and permissions using Spatie Permission
- [x] Create custom login/logout flows
- [x] Implement session management

**Task 2.2: Create user management interface** ✅
- [x] Build user listing page with Filament table
- [x] Create user creation/editing forms
- [x] Implement role assignment interface
- [x] Add user status management (active/inactive)

**Task 2.3: API integration for authentication** ✅
- [x] Create Livewire components for API authentication
- [x] Implement token-based authentication flow
- [x] Handle authentication state management
- [x] Create API service for user operations

**Task 2.4: User profile management** ✅
- [x] Create user profile editing interface
- [x] Implement password change functionality
- [x] Add user preferences management
- [x] Create user avatar upload system

**Deliverables:**
- ✅ Complete user authentication system
- ✅ User management interface
- ✅ Role-based access control
- ✅ User profile management

---

### **Sprint 2: Dashboard & Analytics** (5-7 days)

#### **Epic: Main Dashboard**

**Task 3.1: Create dashboard layout** ✅
- [x] Build main dashboard Livewire component
- [x] Implement responsive grid layout with Tailwind
- [x] Add navigation sidebar with Alpine.js interactions
- [x] Create breadcrumb navigation

**Task 3.2: Dashboard statistics widgets** ✅
- [x] Create statistics cards (total projects, clients, materials)
- [x] Implement real-time data updates with Livewire
- [x] Add loading states and error handling
- [x] Create data refresh mechanisms

**Task 3.3: Charts and visualizations** ✅
- [x] Integrate Chart.js with Alpine.js
- [x] Create project status distribution charts
- [x] Add activity timeline component
- [x] Implement data filtering for charts

**Task 3.4: Quick actions panel** ✅
- [x] Create quick action buttons
- [x] Implement modal dialogs with Alpine.js
- [x] Add keyboard shortcuts support
- [x] Create notification system

**Deliverables:**
- ✅ Responsive dashboard layout
- ✅ Real-time statistics widgets
- ✅ Interactive charts and visualizations
- ✅ Quick actions and shortcuts

---

### **Sprint 3: Project Management** (7-10 days)

#### **Epic: Project CRUD Operations**

**Task 4.1: Project listing interface** ✅
- [x] Create Filament resource for projects
- [x] Implement advanced filtering and search
- [x] Add bulk actions support
- [x] Create project status indicators

**Task 4.2: Project creation/editing** ✅
- [x] Build comprehensive project forms
- [x] Implement file upload functionality
- [x] Add form validation with Livewire
- [x] Create project template system

**Task 4.3: Project details view** ✅
- [x] Create detailed project view component
- [x] Add project timeline and activity log
- [x] Implement project status management
- [x] Create project collaboration features

**Task 4.4: Project API integration** ✅
- [x] Connect project forms to backend API
- [x] Implement real-time project updates
- [x] Add offline support with Alpine.js
- [x] Create project data synchronization

**Deliverables:**
- ✅ Complete project management system
- ✅ Project CRUD operations
- ✅ File upload and management
- ✅ Real-time project updates

---

### **Sprint 4: Client Management** (5-7 days)

#### **Epic: Client Management System**

**Task 5.1: Client listing and search** ✅
- [x] Create Filament resource for clients
- [x] Implement client search and filtering
- [x] Add client import/export functionality
- [x] Create client categorization system

**Task 5.2: Client forms and validation** ✅
- [x] Build client creation/editing forms
- [x] Implement client contact management
- [x] Add client document attachments
- [x] Create client communication history

**Task 5.3: Client-project relationships** ✅
- [x] Create client-project association interface
- [x] Implement project assignment to clients
- [x] Add client project history view
- [x] Create client billing integration

**Task 5.4: Client API integration** ✅
- [x] Connect client management to backend API
- [x] Implement client data synchronization
- [x] Add client activity tracking
- [x] Create client reporting system

**Deliverables:**
- ✅ Complete client management system
- ✅ Client-project relationships
- ✅ Client communication tracking
- ✅ Client data synchronization

---

### **Sprint 5: Material Types Management** (5-7 days)

#### **Epic: Material Types System**

**Task 6.1: Material types listing** ✅
- [x] Create Filament resource for material types
- [x] Implement category-based organization
- [x] Add material type search and filtering
- [x] Create material inventory tracking

**Task 6.2: Material type forms** ✅
- [x] Build material type creation/editing forms
- [x] Implement pricing and unit management
- [x] Add material specifications interface
- [x] Create material supplier management

**Task 6.3: Material type categories** ✅
- [x] Create category management system
- [x] Implement hierarchical category structure
- [x] Add category-based filtering
- [x] Create category reporting

**Task 6.4: Material type API integration** ✅
- [x] Connect material management to backend API
- [x] Implement material data synchronization
- [x] Add material usage analytics
- [x] Create material cost tracking

**Deliverables:**
- ✅ Complete material types management
- ✅ Category organization system
- ✅ Pricing and inventory tracking
- ✅ Material analytics and reporting

---

### **Sprint 6: Advanced Features & Polish** (7-10 days)

#### **Epic: Advanced Functionality**

**Task 7.1: Document management** ✅
- [x] Create document upload/management interface
- [x] Implement document categorization
- [x] Add document preview functionality
- [x] Create document version control

**Task 7.2: Notifications system** ✅
- [x] Implement real-time notifications with Livewire
- [x] Create notification preferences interface
- [x] Add email notification integration
- [x] Create notification history

**Task 7.3: Search and filtering** ✅
- [x] Implement global search functionality
- [x] Add advanced filtering options
- [x] Create saved search functionality
- [x] Add search analytics

**Task 7.4: Performance optimization** ✅
- [x] Optimize Livewire component loading
- [x] Implement lazy loading for large datasets
- [x] Add caching strategies
- [x] Optimize database queries

**Deliverables:**
- ✅ Document management system
- ✅ Real-time notifications
- ✅ Advanced search capabilities
- ✅ Performance optimizations

---

### **Sprint 7: Testing & Deployment** (5-7 days)

#### **Epic: Quality Assurance**

**Task 8.1: Unit testing** 🔄
- [x] Write tests for Livewire components
- [x] Test API integration points
- [x] Add form validation tests
- [x] Create component interaction tests

**Task 8.2: Integration testing** 🔄
- [x] Test complete user workflows
- [x] Verify API connectivity
- [x] Test authentication flows
- [x] Create end-to-end test scenarios

**Task 8.3: UI/UX testing** 🔄
- [x] Test responsive design across devices
- [x] Verify accessibility compliance
- [x] Test Alpine.js interactions
- [x] Validate user experience flows

**Task 8.4: Performance testing** 🔄
- [x] Load test Livewire components
- [x] Test API response times
- [x] Optimize asset loading
- [x] Monitor memory usage

**Deliverables:**
- ✅ Comprehensive test suite
- ✅ Performance benchmarks
- ✅ Accessibility compliance
- ✅ Production-ready application

---

## 🛠️ Technical Implementation Guidelines

### **Vite Configuration** (`vite.config.js`)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css'
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: ['alpinejs']
        }
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

### **Tailwind Configuration** (`tailwind.config.js`)

```javascript
import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './vendor/filament/**/*.blade.php',
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
    theme: {
        extend: {
            colors: {
                // Custom color palette
            }
        }
    }
}
```

### **Alpine.js Integration** (`resources/js/app.js`)

```javascript
import Alpine from 'alpinejs';
import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Alpine.js plugins
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

Alpine.plugin(focus);
Alpine.plugin(collapse);

// Start Livewire
Livewire.start();

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();
```

### **PostCSS Configuration** (`postcss.config.js`)

```javascript
export default {
    plugins: {
        'tailwindcss/nesting': {},
        tailwindcss: {},
        autoprefixer: {},
    },
}
```

---

### **Sprint 8: Barcode Management Foundation** (7-10 giorni)

#### **Epic: Sistema Base per Gestione Barcode**

**Task 8.1: Modello dati per barcode** 
- [ ] Creare migrazione per tabella `material_entries` (documenti di ingresso)
- [ ] Implementare relazioni con Client, Project, MaterialType
- [ ] Aggiungere campi per codice univoco, data ingresso, note
- [ ] Creare factory e seeder per dati di test

**Task 8.2: Filament Resource per documenti di ingresso**
- [ ] Creare `MaterialEntryResource` con CRUD completo
- [ ] Implementare form per registrazione materiale in ingresso
- [ ] Aggiungere validazione per campi obbligatori
- [ ] Creare relazioni con Client, Project, MaterialType

**Task 8.3: Generazione codice univoco**
- [ ] Implementare sistema di generazione codici univoci
- [ ] Aggiungere controllo unicità nel database
- [ ] Creare observer per generazione automatica
- [ ] Implementare algoritmo per codici leggibili

**Task 8.4: API endpoints per barcode**
- [ ] Creare controller per gestione material entries
- [ ] Implementare endpoint per ricerca tramite codice
- [ ] Aggiungere validazione API per codici barcode
- [ ] Creare response standardizzate

**Deliverables:**
- [ ] Modello dati completo per material entries
- [ ] Interface Filament per gestione documenti
- [ ] Sistema di generazione codici univoci
- [ ] API endpoints per integrazione barcode

---

### **Sprint 9: Barcode Generation & Scanning** (10-12 giorni)

#### **Epic: Generazione e Lettura Codici a Barre**

**Task 9.1: Libreria per generazione barcode**
- [ ] Installare e configurare libreria PHP per barcode (es. picqer/php-barcode-generator)
- [ ] Implementare supporto per formati Code128 e QR Code
- [ ] Creare service per generazione barcode in diversi formati
- [ ] Aggiungere configurazione per dimensioni e qualità

**Task 9.2: Interface per generazione barcode**
- [ ] Creare componente Livewire per visualizzazione barcode
- [ ] Implementare download PDF/PNG del barcode
- [ ] Aggiungere preview del barcode nel form
- [ ] Creare template per stampa etichette

**Task 9.3: Scanner barcode web-based**
- [ ] Integrare libreria JavaScript per camera access (es. QuaggaJS o ZXing)
- [ ] Creare componente Alpine.js per scanning
- [ ] Implementare fallback per input manuale codice
- [ ] Aggiungere feedback visivo durante scanning

**Task 9.4: Mobile-responsive scanner**
- [ ] Ottimizzare scanner per dispositivi mobile
- [ ] Implementare auto-focus e zoom camera
- [ ] Aggiungere supporto per orientamento device
- [ ] Creare PWA manifest per installazione mobile

**Task 9.5: Rigenerazione barcode**
- [ ] Implementare funzionalità di rigenerazione codice
- [ ] Mantenere storico dei codici generati
- [ ] Aggiungere motivo per rigenerazione
- [ ] Creare log delle operazioni di rigenerazione

**Deliverables:**
- [ ] Sistema completo di generazione barcode
- [ ] Scanner web-based funzionante
- [ ] Interface mobile-responsive
- [ ] Funzionalità di rigenerazione codici
- [ ] Template per stampa etichette

---

### **Sprint 10: Material Tracking & Traceability** (8-10 giorni)

#### **Epic: Tracciabilità e Ricerca Materiale**

**Task 10.1: Ricerca tramite barcode**
- [ ] Implementare endpoint API per ricerca tramite codice
- [ ] Creare interface di ricerca con scanner integrato
- [ ] Aggiungere ricerca testuale per codici
- [ ] Implementare cache per ricerche frequenti

**Task 10.2: Dettaglio documento da barcode**
- [ ] Creare pagina di dettaglio material entry
- [ ] Visualizzare informazioni complete (cliente, progetto, materiale)
- [ ] Aggiungere timeline delle operazioni
- [ ] Implementare export dati in PDF/CSV

**Task 10.3: Storico e tracking**
- [ ] Creare tabella per tracking operazioni
- [ ] Implementare log di accessi tramite barcode
- [ ] Aggiungere timestamp e user tracking
- [ ] Creare report di utilizzo barcode

**Task 10.4: Dashboard tracciabilità**
- [ ] Creare widget per statistiche barcode
- [ ] Implementare grafici di utilizzo
- [ ] Aggiungere alert per codici non utilizzati
- [ ] Creare report di tracciabilità

**Task 10.5: Notifiche e alert**
- [ ] Implementare notifiche per nuovi scan
- [ ] Aggiungere alert per codici duplicati
- [ ] Creare sistema di notifiche real-time
- [ ] Implementare email notifications

**Deliverables:**
- [ ] Sistema completo di ricerca barcode
- [ ] Interface dettaglio documenti
- [ ] Tracking completo delle operazioni
- [ ] Dashboard per monitoraggio
- [ ] Sistema di notifiche

---

### **Sprint 11: Barcode System Integration** (5-7 giorni)

#### **Epic: Integrazione e Ottimizzazione Sistema**

**Task 11.1: Integrazione con sistema esistente**
- [ ] Integrare barcode con DocumentResource esistente
- [ ] Aggiungere barcode ai documenti già presenti
- [ ] Creare migrazione per dati esistenti
- [ ] Implementare backward compatibility

**Task 11.2: Export e reporting**
- [ ] Implementare export massivo di barcode
- [ ] Creare template per stampa batch
- [ ] Aggiungere report di utilizzo
- [ ] Implementare export dati per analisi

**Task 11.3: Sicurezza e permessi**
- [ ] Implementare controllo accessi per barcode
- [ ] Aggiungere permessi per generazione/rigenerazione
- [ ] Creare audit log per operazioni sensibili
- [ ] Implementare rate limiting per API

**Task 11.4: Performance optimization**
- [ ] Ottimizzare query per ricerca barcode
- [ ] Implementare caching per codici frequenti
- [ ] Aggiungere indici database appropriati
- [ ] Ottimizzare generazione immagini barcode

**Task 11.5: Testing e documentazione**
- [ ] Creare test automatizzati per barcode system
- [ ] Implementare test di integrazione
- [ ] Creare documentazione utente
- [ ] Aggiungere guide per troubleshooting

**Deliverables:**
- [ ] Sistema barcode completamente integrato
- [ ] Funzionalità di export e reporting
- [ ] Sicurezza e controllo accessi
- [ ] Performance ottimizzate
- [ ] Documentazione completa

---

## 📋 Requisiti Tecnici Sistema Barcode

### **Specifiche Funzionali**

#### **Gestione Documenti di Ingresso**
- **Campi Obbligatori**: Cliente, Progetto, Tipologia Materiale
- **Campi Opzionali**: Data ingresso, Note, Descrizioni
- **Validazione**: Controllo esistenza relazioni, unicità codici
- **Audit**: Log completo delle operazioni

#### **Generazione Codici a Barre**
- **Formati Supportati**: Code128, QR Code
- **Unicità**: Algoritmo per garantire codici univoci
- **Rigenerazione**: Possibilità di rigenerare mantenendo storico
- **Export**: PDF, PNG per stampa etichette

#### **Scanner e Lettura**
- **Web-based**: Accesso camera tramite browser
- **Mobile-responsive**: Ottimizzato per dispositivi mobile
- **Fallback**: Input manuale per codici non leggibili
- **Real-time**: Ricerca immediata nel database

#### **Tracciabilità**
- **Ricerca**: Tramite codice barcode o ricerca testuale
- **Storico**: Log completo di tutti gli accessi
- **Timeline**: Cronologia operazioni per ogni documento
- **Reporting**: Statistiche utilizzo e tracciabilità

### **Stack Tecnologico Barcode**

#### **Backend (PHP/Laravel)**
```php
// Librerie richieste
"picqer/php-barcode-generator": "^2.4"  // Generazione barcode
"endroid/qr-code": "^5.0"               // Generazione QR Code
"intervention/image": "^3.0"            // Manipolazione immagini
```

#### **Frontend (JavaScript/Alpine.js)**
```javascript
// Librerie per scanning
"quagga": "^0.12.1"          // Barcode scanner
"@zxing/library": "^0.21.0"  // QR Code scanner
"html5-qrcode": "^2.3.8"     // Scanner alternativo
```

#### **Database Schema**
```sql
-- Tabella material_entries
CREATE TABLE material_entries (
    id BIGINT PRIMARY KEY,
    unique_code VARCHAR(255) UNIQUE NOT NULL,
    client_id BIGINT NOT NULL,
    project_id BIGINT NOT NULL,
    material_type_id BIGINT NOT NULL,
    entry_date DATE NOT NULL,
    notes TEXT,
    barcode_path VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (material_type_id) REFERENCES material_types(id)
);

-- Tabella barcode_scans (tracking)
CREATE TABLE barcode_scans (
    id BIGINT PRIMARY KEY,
    material_entry_id BIGINT NOT NULL,
    user_id BIGINT,
    scanned_at TIMESTAMP NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (material_entry_id) REFERENCES material_entries(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabella barcode_regenerations (storico)
CREATE TABLE barcode_regenerations (
    id BIGINT PRIMARY KEY,
    material_entry_id BIGINT NOT NULL,
    old_code VARCHAR(255) NOT NULL,
    new_code VARCHAR(255) NOT NULL,
    reason TEXT,
    user_id BIGINT NOT NULL,
    regenerated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (material_entry_id) REFERENCES material_entries(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### **Vincoli e Limitazioni**

#### **Sicurezza**
- **Autenticazione**: Accesso solo per utenti autenticati
- **Autorizzazione**: Permessi granulari per operazioni
- **Rate Limiting**: Limitazione richieste API per prevenire abusi
- **Audit Log**: Tracciamento completo delle operazioni

#### **Performance**
- **Caching**: Cache per ricerche frequenti
- **Indici Database**: Ottimizzazione query di ricerca
- **Lazy Loading**: Caricamento progressivo per liste grandi
- **Image Optimization**: Compressione immagini barcode

#### **Compatibilità**
- **Browser**: Supporto Chrome, Firefox, Safari, Edge
- **Mobile**: iOS Safari, Android Chrome
- **Lettori**: Compatibilità con lettori barcode standard
- **Formati**: Code128 per compatibilità industriale, QR per mobile

### **Metriche di Successo**

#### **Performance**
- **Generazione Barcode**: < 500ms per codice
- **Ricerca**: < 200ms per lookup tramite codice
- **Scanner**: < 2s per riconoscimento codice
- **Mobile**: Funzionamento su dispositivi con camera 2MP+

#### **Usabilità**
- **Tasso Successo Scan**: > 95% per codici in buone condizioni
- **Tempo Medio Scan**: < 3 secondi
- **Errori Utente**: < 5% per operazioni comuni
- **Soddisfazione**: Rating > 4/5 nei test utente

---

## 🔗 API Integration Strategy

### **HTTP Client Service**

```php
<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService
{
    protected string $baseUrl;
    protected array $defaultHeaders;

    public function __construct()
    {
        $this->baseUrl = config('app.api_url', config('app.url') . '/api');
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function get(string $endpoint, array $params = []): Response
    {
        return Http::withHeaders($this->defaultHeaders)
            ->get($this->baseUrl . $endpoint, $params);
    }

    public function post(string $endpoint, array $data = []): Response
    {
        return Http::withHeaders($this->defaultHeaders)
            ->post($this->baseUrl . $endpoint, $data);
    }

    // Additional HTTP methods...
}
```

### **Livewire Component Example**

```php
<?php

namespace App\Livewire;

use App\Services\ApiService;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use WithPagination;

    public string $search = '';
    public array $filters = [];

    protected ApiService $apiService;

    public function boot(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function render()
    {
        $projects = $this->getProjects();
        
        return view('livewire.projects-list', [
            'projects' => $projects
        ]);
    }

    protected function getProjects()
    {
        $response = $this->apiService->get('/projects', [
            'search' => $this->search,
            'filters' => $this->filters,
            'page' => $this->getPage(),
        ]);

        return $response->json();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
```

## 📊 Performance Considerations

### **Livewire Optimization**
- Use `lazy` loading for heavy components
- Implement `defer` for non-critical updates
- Utilize `wire:key` for dynamic lists
- Cache API responses where appropriate

### **Alpine.js Best Practices**
- Use `x-data` sparingly for large datasets
- Implement `x-show` vs `x-if` appropriately
- Utilize `x-cloak` to prevent flash of unstyled content
- Optimize directive usage for performance

### **Asset Optimization**
- Enable Vite's build optimization
- Use code splitting for large components
- Implement lazy loading for images and heavy content
- Minimize CSS and JavaScript bundles

## 🔄 Migration Strategy

### **Phase 1: Parallel Development**
- Develop new Filament/Livewire components alongside existing Vue.js
- Use feature flags to control which interface is shown
- Maintain both systems during transition period

### **Phase 2: Gradual Migration**
- Migrate one module at a time (start with least complex)
- Test each module thoroughly before proceeding
- Gather user feedback and iterate

### **Phase 3: Complete Transition**
- Remove Vue.js dependencies and components
- Clean up unused assets and configurations
- Optimize final build for production

### **Rollback Plan**
- Maintain Vue.js codebase until migration is complete
- Use Git branches for easy rollback if needed
- Keep database migrations backward compatible

## 📋 Definition of Done

Each sprint task is considered complete when:

### **Criteri Generali**
- [ ] **Functionality**: All specified features work as expected
- [ ] **Testing**: Unit and integration tests pass
- [ ] **Code Review**: Code has been reviewed and approved
- [ ] **Documentation**: Implementation is documented
- [ ] **Performance**: Meets performance benchmarks
- [ ] **Accessibility**: Passes accessibility standards
- [ ] **Responsive**: Works on all target devices
- [ ] **API Integration**: Successfully connects to backend API

### **Criteri Specifici Sistema Barcode**
- [ ] **Barcode Generation**: Codici generati correttamente in formato Code128/QR
- [ ] **Scanner Functionality**: Scanner web funziona su desktop e mobile
- [ ] **Database Integrity**: Unicità codici garantita, relazioni corrette
- [ ] **Real-time Search**: Ricerca tramite codice < 200ms
- [ ] **Mobile Compatibility**: Funziona su iOS Safari e Android Chrome
- [ ] **Print Templates**: Template stampa etichette funzionanti
- [ ] **Security**: Controllo accessi e audit log implementati
- [ ] **Error Handling**: Gestione errori per scanner e generazione
- [ ] **User Experience**: Interface intuitiva per operatori
- [ ] **Data Export**: Export PDF/CSV funzionante

## 🎯 Success Metrics

### **Metriche Generali**
- **Performance**: Page load times < 2 seconds
- **Accessibility**: WCAG 2.1 AA compliance
- **Code Quality**: 90%+ test coverage
- **User Experience**: Positive user feedback
- **Maintainability**: Reduced complexity metrics
- **Development Speed**: Faster feature development

### **Metriche Sistema Barcode**
- **Generazione Barcode**: < 500ms per generazione codice
- **Ricerca Database**: < 200ms per lookup tramite codice
- **Scanner Performance**: < 2s per riconoscimento barcode
- **Tasso Successo Scan**: > 95% per codici in buone condizioni
- **Mobile Compatibility**: Funziona su 95%+ dispositivi target
- **Uptime Sistema**: 99.9% disponibilità per funzioni critiche
- **Accuratezza Dati**: 0% errori nella generazione codici univoci
- **User Adoption**: 90%+ utilizzo da parte degli operatori
- **Error Rate**: < 1% errori nelle operazioni barcode
- **Training Time**: < 30 minuti per formazione nuovo utente

## 📞 Support and Resources

### **Documentation**
- [Laravel Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev/start-here)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### **Community Resources**
- Laravel Filament Discord
- Livewire GitHub Discussions
- Alpine.js Community Forum
- Tailwind CSS Discord

---

## 📊 Timeline Complessivo

### **Fase 1: Migrazione Base** (Completata)
- Sprint 1-7: Sistema base Filament + Livewire ✅
- Durata: ~8 settimane
- Status: 100% Completato

### **Fase 2: Sistema Barcode** (Pianificata)
- Sprint 8-11: Implementazione completa gestione barcode
- Durata stimata: ~6-8 settimane
- Status: 0% - In pianificazione

### **Totale Progetto**
- **Durata Complessiva**: ~14-16 settimane
- **Sprint Totali**: 11 sprint
- **Effort Stimato**: ~280-320 ore sviluppo
- **Team Consigliato**: 2-3 sviluppatori + 1 PM

---

**Last Updated**: Gennaio 2025  
**Version**: 2.0 (con Sistema Barcode)  
**Status**: Fase 1 Completata - Fase 2 Ready for Implementation  
**Next Sprint**: Sprint 8 - Barcode Management Foundation