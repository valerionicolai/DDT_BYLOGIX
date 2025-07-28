# Sistema di Route Guarding - DTT by Logix

## Panoramica

Il sistema di route guarding implementato fornisce un controllo granulare degli accessi alle diverse sezioni dell'applicazione basato sui ruoli utente e sui permessi specifici.

## Componenti Principali

### 1. Middleware di Routing (`resources/js/router/middleware.js`)

Il middleware gestisce la logica di autenticazione e autorizzazione per le rotte:

- **requiresAuth**: Verifica che l'utente sia autenticato
- **requiresGuest**: Permette l'accesso solo agli utenti non autenticati
- **requiresAdmin**: Richiede privilegi di amministratore
- **requiresPermission**: Verifica permessi specifici
- **requiresAllPermissions**: Richiede tutti i permessi specificati
- **requiresAnyPermission**: Richiede almeno uno dei permessi specificati

### 2. Sistema di Permessi (`resources/js/composables/usePermissions.js`)

Definisce i permessi disponibili nell'applicazione:

```javascript
// Esempi di permessi
const permissions = {
  'view.dashboard': () => authStore.isAuthenticated,
  'create.project': () => authStore.isAuthenticated,
  'edit.project': () => authStore.isAuthenticated,
  'delete.project': () => authStore.isAdmin,
  'manage.users': () => authStore.isAdmin,
  // ... altri permessi
}
```

### 3. Componente CanAccess (`resources/js/components/CanAccess.vue`)

Componente per il controllo condizionale della visualizzazione degli elementi UI:

```vue
<!-- Esempi di utilizzo -->

<!-- Solo per utenti autenticati -->
<CanAccess requires-auth>
  <button>Azione per utenti autenticati</button>
</CanAccess>

<!-- Solo per amministratori -->
<CanAccess requires-admin>
  <button>Azione solo admin</button>
</CanAccess>

<!-- Permesso specifico -->
<CanAccess permission="create.project">
  <button>Crea Progetto</button>
</CanAccess>

<!-- Tutti i permessi richiesti -->
<CanAccess :all-permissions="['edit.project', 'view.project']">
  <button>Modifica Progetto</button>
</CanAccess>

<!-- Almeno uno dei permessi -->
<CanAccess :any-permissions="['view.reports', 'create.reports']">
  <button>Sezione Report</button>
</CanAccess>
```

## Configurazione delle Rotte

### Definizione dei Meta Campi

Nel file `resources/js/router/index.js`, ogni rotta può avere meta campi per definire i requisiti di accesso:

```javascript
{
  path: '/projects',
  name: 'Projects',
  component: Projects,
  meta: {
    requiresAuth: true,
    requiresPermission: 'view.projects',
    title: 'Progetti'
  }
},
{
  path: '/admin/users',
  name: 'AdminUsers',
  component: AdminUsers,
  meta: {
    requiresAuth: true,
    requiresAdmin: true,
    title: 'Gestione Utenti'
  }
}
```

### Meta Campi Disponibili

- `requiresAuth: boolean` - Richiede autenticazione
- `requiresGuest: boolean` - Solo per utenti non autenticati
- `requiresAdmin: boolean` - Solo per amministratori
- `requiresPermission: string` - Permesso specifico richiesto
- `requiresAllPermissions: string[]` - Tutti i permessi richiesti
- `requiresAnyPermission: string[]` - Almeno uno dei permessi richiesti

## Gestione degli Errori

### Pagine di Errore

- **Unauthorized** (`resources/js/pages/Unauthorized.vue`): Mostrata quando l'utente non ha i permessi necessari
- **NotFound** (`resources/js/pages/NotFound.vue`): Pagina 404 personalizzata

### Tipi di Errore di Autorizzazione

- `insufficient_permissions`: Permessi insufficienti
- `session_expired`: Sessione scaduta
- `admin_required`: Privilegi di amministratore richiesti
- `authentication_required`: Autenticazione richiesta

## Store di Autenticazione Migliorato

### Nuove Funzionalità

- **Gestione Sessione**: Timeout automatico e tracking dell'attività
- **Refresh Token**: Rinnovo automatico dei token
- **Interceptor Axios**: Gestione automatica delle risposte non autorizzate
- **Metodi di Utilità**: Controllo permessi e ruoli

### Metodi Disponibili

```javascript
// Controllo autenticazione
authStore.isAuthenticated

// Controllo ruoli
authStore.isAdmin
authStore.isUser

// Controllo permessi
authStore.hasPermission('create.project')
authStore.hasAllPermissions(['edit.project', 'view.project'])
authStore.hasAnyPermission(['view.reports', 'create.reports'])

// Gestione sessione
authStore.updateActivity()
authStore.refreshToken()
authStore.checkSessionTimeout()
```

## Esempi Pratici

### 1. Navigazione Condizionale

```vue
<template>
  <nav>
    <!-- Link sempre visibile per utenti autenticati -->
    <CanAccess requires-auth>
      <router-link to="/dashboard">Dashboard</router-link>
    </CanAccess>

    <!-- Link solo per chi può vedere i progetti -->
    <CanAccess permission="view.projects">
      <router-link to="/projects">Progetti</router-link>
    </CanAccess>

    <!-- Sezione admin -->
    <CanAccess requires-admin>
      <div class="admin-section">
        <router-link to="/admin/users">Utenti</router-link>
        <router-link to="/admin/settings">Impostazioni</router-link>
      </div>
    </CanAccess>
  </nav>
</template>
```

### 2. Azioni Condizionali

```vue
<template>
  <div class="project-card">
    <h3>{{ project.name }}</h3>
    
    <div class="actions">
      <!-- Modifica solo per chi ha il permesso -->
      <CanAccess permission="edit.project">
        <button @click="editProject">Modifica</button>
      </CanAccess>

      <!-- Eliminazione solo per admin -->
      <CanAccess requires-admin>
        <button @click="deleteProject" class="danger">Elimina</button>
      </CanAccess>

      <!-- Impostazioni avanzate per admin -->
      <CanAccess requires-admin>
        <button @click="advancedSettings">Avanzate</button>
      </CanAccess>
    </div>
  </div>
</template>
```

### 3. Contenuto Condizionale con Fallback

```vue
<template>
  <div>
    <!-- Contenuto per utenti con permessi -->
    <CanAccess 
      permission="view.reports"
      fallback-message="Non hai i permessi per visualizzare i report."
    >
      <ReportsComponent />
    </CanAccess>

    <!-- Contenuto alternativo per admin -->
    <CanAccess requires-admin fallback-message="Sezione riservata agli amministratori.">
      <AdminPanel />
    </CanAccess>
  </div>
</template>
```

## Best Practices

### 1. Sicurezza a Livelli

- **Frontend**: Controllo UI per migliorare l'esperienza utente
- **Backend**: Validazione effettiva dei permessi (sempre necessaria)

### 2. Granularità dei Permessi

- Definire permessi specifici piuttosto che generici
- Utilizzare convenzioni di naming consistenti (es. `action.resource`)

### 3. Gestione degli Errori

- Fornire messaggi di errore chiari e utili
- Offrire azioni alternative quando possibile

### 4. Performance

- Utilizzare computed properties per controlli complessi
- Evitare controlli ridondanti nei template

## Estensioni Future

### 1. Permessi Dinamici

Possibilità di definire permessi basati su contesto (es. proprietario del progetto):

```javascript
'edit.project': (projectId) => {
  return authStore.isAdmin || 
         authStore.user.id === getProjectOwner(projectId)
}
```

### 2. Ruoli Personalizzati

Sistema di ruoli più flessibile con permessi configurabili:

```javascript
const roles = {
  'project_manager': ['view.projects', 'create.project', 'edit.project'],
  'developer': ['view.projects', 'edit.project'],
  'viewer': ['view.projects']
}
```

### 3. Audit Trail

Logging delle azioni e dei tentativi di accesso non autorizzati.

## Troubleshooting

### Problemi Comuni

1. **Redirect Loop**: Verificare che le rotte di fallback siano corrette
2. **Permessi Non Aggiornati**: Controllare il refresh dei dati utente
3. **Componenti Non Visibili**: Verificare l'importazione di CanAccess

### Debug

Utilizzare i metodi di debug dello store:

```javascript
// In console del browser
console.log('User:', authStore.user)
console.log('Is Admin:', authStore.isAdmin)
console.log('Has Permission:', authStore.hasPermission('create.project'))
```