# Strategia di Branching - GitFlow

## Panoramica
Il progetto DTT by Logix utilizza la strategia **GitFlow** per la gestione dei branch e del ciclo di vita del codice.

## Struttura dei Branch

### Branch Principali

#### `main` (Production)
- **Scopo**: Contiene il codice di produzione stabile
- **Protezione**: Branch protetto, merge solo tramite Pull Request
- **Deploy**: Automatico in produzione
- **Naming**: `main`

#### `develop` (Development)
- **Scopo**: Branch di integrazione per lo sviluppo
- **Contenuto**: Ultime funzionalità completate per il prossimo rilascio
- **Merge da**: Feature branches, hotfix branches
- **Naming**: `develop`

### Branch di Supporto

#### Feature Branches
- **Scopo**: Sviluppo di nuove funzionalità
- **Origine**: `develop`
- **Destinazione**: `develop`
- **Durata**: Fino al completamento della feature
- **Naming**: `feature/[sprint-id]-[task-id]-[descrizione]`
- **Esempio**: `feature/s1-b03-user-authentication`

#### Release Branches
- **Scopo**: Preparazione di un nuovo rilascio
- **Origine**: `develop`
- **Destinazione**: `main` e `develop`
- **Naming**: `release/v[versione]`
- **Esempio**: `release/v1.0.0`

#### Hotfix Branches
- **Scopo**: Correzioni urgenti in produzione
- **Origine**: `main`
- **Destinazione**: `main` e `develop`
- **Naming**: `hotfix/[descrizione]`
- **Esempio**: `hotfix/critical-security-fix`

## Workflow per Sprint

### 1. Inizio Sprint
```bash
# Creare branch feature dal develop
git checkout develop
git pull origin develop
git checkout -b feature/s[sprint-number]-[task-id]-[description]
```

### 2. Sviluppo Feature
```bash
# Commit frequenti con messaggi descrittivi
git add .
git commit -m "feat(s1-b03): implement user authentication middleware"
git push origin feature/s1-b03-user-authentication
```

### 3. Completamento Feature
```bash
# Merge in develop tramite Pull Request
# 1. Creare PR da feature branch a develop
# 2. Code review del team
# 3. Merge dopo approvazione
# 4. Eliminare feature branch
```

### 4. Release Sprint
```bash
# Creare release branch
git checkout develop
git checkout -b release/v1.0.0

# Testing e bug fixes nel release branch
# Merge in main e develop
```

## Convenzioni di Commit

### Formato
```
<type>(<scope>): <description>

[optional body]

[optional footer]
```

### Tipi di Commit
- `feat`: Nuova funzionalità
- `fix`: Correzione di bug
- `docs`: Documentazione
- `style`: Formattazione codice
- `refactor`: Refactoring
- `test`: Test
- `chore`: Manutenzione

### Esempi
```bash
feat(auth): add user login functionality
fix(database): resolve connection timeout issue
docs(readme): update installation instructions
test(user): add unit tests for user model
```

## Branch Protection Rules

### Main Branch
- Richiede Pull Request
- Richiede review di almeno 1 sviluppatore
- Richiede status check (CI/CD)
- Non permette force push
- Non permette eliminazione

### Develop Branch
- Richiede Pull Request
- Richiede status check (CI/CD)
- Permette merge da admin

## Sprint Mapping

### Sprint 0: Setup e Fondamenta
- Branch: `feature/s0-*`
- Focus: Configurazione iniziale

### Sprint 1: Core Backend e Autenticazione
- Branch: `feature/s1-*`
- Focus: API e autenticazione

### Sprint 2: Gestione Documenti e Materiali
- Branch: `feature/s2-*`
- Focus: CRUD documenti

### Sprint 3: Barcode e Funzionalità Avanzate
- Branch: `feature/s3-*`
- Focus: Scansione e export

### Sprint 4: Refactoring e Deploy
- Branch: `feature/s4-*`
- Focus: Ottimizzazione e rilascio

## Comandi Utili

```bash
# Inizializzare GitFlow (se si usa git-flow tool)
git flow init

# Creare feature branch
git flow feature start s1-b03-user-auth

# Finire feature branch
git flow feature finish s1-b03-user-auth

# Creare release
git flow release start v1.0.0

# Finire release
git flow release finish v1.0.0

# Creare hotfix
git flow hotfix start critical-fix

# Finire hotfix
git flow hotfix finish critical-fix
```

## Note per il Team

1. **Mai committare direttamente su main o develop**
2. **Sempre creare Pull Request per il merge**
3. **Testare localmente prima del push**
4. **Usare commit message descrittivi**
5. **Mantenere i branch feature piccoli e focalizzati**
6. **Eliminare i branch feature dopo il merge**
7. **Sincronizzare regolarmente con develop**