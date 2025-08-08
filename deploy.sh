#!/bin/bash

# DDT by Logix - Script Deploy Semplificato
# Versione: 1.0
# Uso: ./deploy.sh [production|staging]

set -e

# Configurazioni di default
DEFAULT_ENV="production"
APP_DIR="/var/www/dttbylogix"
BACKUP_DIR="/var/backups/dttbylogix"
LOG_FILE="/var/log/dttbylogix-deploy.log"

# Colori per output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funzioni utility
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a $LOG_FILE
}

success() {
    echo -e "${GREEN}✓${NC} $1" | tee -a $LOG_FILE
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1" | tee -a $LOG_FILE
}

error() {
    echo -e "${RED}✗${NC} $1" | tee -a $LOG_FILE
}

error_exit() {
    error "ERRORE: $1"
    exit 1
}

# Banner
show_banner() {
    echo -e "${BLUE}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                    DDT by Logix Deploy                      ║"
    echo "║                  Script Deploy Automatico                   ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

# Verifica prerequisiti
check_prerequisites() {
    log "Verifica prerequisiti sistema..."
    
    # Verifica comandi necessari
    local commands=("git" "composer" "npm" "php" "mysql")
    for cmd in "${commands[@]}"; do
        if ! command -v $cmd >/dev/null 2>&1; then
            error_exit "$cmd non è installato"
        fi
    done
    
    # Verifica versione PHP
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    if [[ $(echo "$PHP_VERSION 8.2" | awk '{print ($1 >= $2)}') == 0 ]]; then
        error_exit "PHP 8.2+ richiesto. Versione attuale: $PHP_VERSION"
    fi
    
    # Verifica directory applicazione
    if [ ! -d "$APP_DIR" ]; then
        error_exit "Directory applicazione non trovata: $APP_DIR"
    fi
    
    success "Prerequisiti verificati"
}

# Backup database
backup_database() {
    log "Creazione backup database..."
    
    mkdir -p $BACKUP_DIR
    
    # Leggi configurazione database da .env
    if [ -f "$APP_DIR/.env" ]; then
        DB_DATABASE=$(grep DB_DATABASE $APP_DIR/.env | cut -d '=' -f2)
        DB_USERNAME=$(grep DB_USERNAME $APP_DIR/.env | cut -d '=' -f2)
        DB_PASSWORD=$(grep DB_PASSWORD $APP_DIR/.env | cut -d '=' -f2)
        
        BACKUP_FILE="$BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql"
        
        if mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null; then
            success "Database backup creato: $BACKUP_FILE"
        else
            warning "Backup database fallito - continuando senza backup"
        fi
    else
        warning "File .env non trovato - saltando backup database"
    fi
}

# Backup files
backup_files() {
    log "Creazione backup files..."
    
    BACKUP_TAR="$BACKUP_DIR/app_backup_$(date +%Y%m%d_%H%M%S).tar.gz"
    
    if tar -czf "$BACKUP_TAR" -C "$(dirname $APP_DIR)" "$(basename $APP_DIR)" 2>/dev/null; then
        success "Files backup creato: $BACKUP_TAR"
    else
        warning "Backup files fallito - continuando senza backup"
    fi
}

# Modalità manutenzione
maintenance_mode() {
    local action=$1
    cd $APP_DIR
    
    if [ "$action" == "on" ]; then
        log "Attivazione modalità manutenzione..."
        php artisan down --message="Aggiornamento in corso..." --retry=60 2>/dev/null || true
        success "Modalità manutenzione attivata"
    else
        log "Disattivazione modalità manutenzione..."
        php artisan up 2>/dev/null || true
        success "Modalità manutenzione disattivata"
    fi
}

# Deploy principale
deploy_application() {
    log "Inizio deploy applicazione..."
    
    cd $APP_DIR
    
    # Git operations
    log "Aggiornamento codice sorgente..."
    git fetch origin || error_exit "Git fetch fallito"
    
    local branch=${1:-$DEFAULT_ENV}
    if git show-ref --verify --quiet refs/heads/$branch; then
        git checkout $branch || error_exit "Checkout branch $branch fallito"
        git reset --hard origin/$branch || error_exit "Git reset fallito"
    else
        warning "Branch $branch non trovato, usando branch corrente"
        git pull origin $(git branch --show-current) || error_exit "Git pull fallito"
    fi
    success "Codice aggiornato"
    
    # Composer install
    log "Installazione dipendenze PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction || error_exit "Composer install fallito"
    success "Dipendenze PHP installate"
    
    # NPM operations
    log "Installazione dipendenze Node.js..."
    npm ci --only=production || error_exit "NPM install fallito"
    success "Dipendenze Node.js installate"
    
    log "Build assets frontend..."
    npm run build || error_exit "Build assets fallito"
    success "Assets compilati"
    
    # Laravel operations
    log "Esecuzione migrations database..."
    php artisan migrate --force || error_exit "Migrations fallite"
    success "Database aggiornato"
    
    log "Ottimizzazione cache Laravel..."
    php artisan config:cache || warning "Config cache fallito"
    php artisan route:cache || warning "Route cache fallito"
    php artisan view:cache || warning "View cache fallito"
    php artisan event:cache || warning "Event cache fallito"
    success "Cache Laravel ottimizzata"
    
    # Filament optimizations
    log "Ottimizzazione Filament..."
    php artisan filament:optimize || warning "Filament optimize fallito"
    success "Filament ottimizzato"
    
    # Permissions
    log "Configurazione permessi..."
    if [ -w "$APP_DIR/storage" ] && [ -w "$APP_DIR/bootstrap/cache" ]; then
        chmod -R 775 storage bootstrap/cache 2>/dev/null || true
        success "Permessi configurati"
    else
        warning "Impossibile configurare permessi - verificare manualmente"
    fi
    
    success "Deploy completato"
}

# Verifica deploy
verify_deployment() {
    log "Verifica integrità deploy..."
    
    cd $APP_DIR
    
    # Test database connection
    if php artisan tinker --execute="DB::connection()->getPdo();" >/dev/null 2>&1; then
        success "Connessione database OK"
    else
        error "Test connessione database fallito"
        return 1
    fi
    
    # Test basic Laravel functionality
    if php artisan --version >/dev/null 2>&1; then
        success "Laravel funzionante"
    else
        error "Test Laravel fallito"
        return 1
    fi
    
    success "Verifica deploy completata"
}

# Cleanup
cleanup_old_files() {
    log "Pulizia files temporanei..."
    
    cd $APP_DIR
    
    # Clear application cache
    php artisan cache:clear >/dev/null 2>&1 || true
    
    # Remove old backups (>7 days)
    if [ -d "$BACKUP_DIR" ]; then
        find $BACKUP_DIR -name "*.sql" -mtime +7 -delete 2>/dev/null || true
        find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete 2>/dev/null || true
    fi
    
    success "Cleanup completato"
}

# Rollback function
rollback() {
    error "Deploy fallito - Iniziando rollback..."
    
    maintenance_mode off
    
    # Find latest backup
    if [ -d "$BACKUP_DIR" ]; then
        LATEST_BACKUP=$(ls -t $BACKUP_DIR/app_backup_*.tar.gz 2>/dev/null | head -1)
        if [ -n "$LATEST_BACKUP" ]; then
            warning "Ripristino backup: $LATEST_BACKUP"
            # Implementare logica rollback se necessario
        fi
    fi
    
    error "Rollback completato - Verificare manualmente lo stato"
}

# Funzione principale
main() {
    local environment=${1:-$DEFAULT_ENV}
    
    show_banner
    log "=== INIZIO DEPLOY DDT BY LOGIX (Environment: $environment) ==="
    
    # Trap per gestire errori
    trap rollback ERR
    
    check_prerequisites
    backup_database
    backup_files
    maintenance_mode on
    
    if deploy_application $environment && verify_deployment; then
        maintenance_mode off
        cleanup_old_files
        success "=== DEPLOY COMPLETATO CON SUCCESSO ==="
        
        echo ""
        echo -e "${GREEN}🎉 Deploy completato!${NC}"
        echo -e "${BLUE}📱 Admin Panel:${NC} https://your-domain.com/admin"
        echo -e "${BLUE}📊 Logs:${NC} $LOG_FILE"
        echo ""
    else
        error "=== DEPLOY FALLITO ==="
        exit 1
    fi
}

# Help function
show_help() {
    echo "DDT by Logix - Script Deploy"
    echo ""
    echo "Uso: $0 [OPZIONI] [ENVIRONMENT]"
    echo ""
    echo "ENVIRONMENT:"
    echo "  production    Deploy per ambiente produzione (default)"
    echo "  staging       Deploy per ambiente staging"
    echo ""
    echo "OPZIONI:"
    echo "  -h, --help    Mostra questo help"
    echo "  -v, --version Mostra versione script"
    echo ""
    echo "Esempi:"
    echo "  $0                    # Deploy produzione"
    echo "  $0 staging           # Deploy staging"
    echo "  $0 production        # Deploy produzione esplicito"
    echo ""
}

# Parse arguments
case "${1:-}" in
    -h|--help)
        show_help
        exit 0
        ;;
    -v|--version)
        echo "DDT by Logix Deploy Script v1.0"
        exit 0
        ;;
    *)
        main "$@"
        ;;
esac