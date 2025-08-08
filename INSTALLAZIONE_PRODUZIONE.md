# 🚀 Guida Installazione Ambiente Produzione
## DDT by Logix - Sistema Gestione Documenti con Filament

---

## 📋 **REQUISITI SISTEMA**

### **Server Requirements**
- **OS**: Ubuntu 22.04 LTS / CentOS 8+ / RHEL 8+
- **RAM**: Minimo 2GB, Raccomandato 4GB+
- **Storage**: Minimo 20GB SSD
- **CPU**: 2 vCPU minimo

### **Software Stack**
- **PHP**: 8.2+ (con estensioni richieste)
- **Web Server**: Nginx 1.20+ o Apache 2.4+
- **Database**: MySQL 8.0+ o MariaDB 10.6+
- **Node.js**: 18+ LTS
- **Composer**: 2.6+
- **SSL**: Certificato valido (Let's Encrypt raccomandato)

---

## 🔧 **FASE 1: CONFIGURAZIONE SERVER**

### **1.1 Aggiornamento Sistema**
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# CentOS/RHEL
sudo dnf update -y
```

### **1.2 Installazione PHP 8.2+**
```bash
# Ubuntu/Debian
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml \
    php8.2-bcmath php8.2-intl php8.2-redis php8.2-opcache -y

# CentOS/RHEL
sudo dnf install epel-release -y
sudo dnf install https://rpms.remirepo.net/enterprise/remi-release-8.rpm -y
sudo dnf module enable php:remi-8.2 -y
sudo dnf install php php-fpm php-cli php-common php-mysqlnd php-zip \
    php-gd php-mbstring php-curl php-xml php-bcmath php-intl \
    php-redis php-opcache -y
```

### **1.3 Configurazione PHP**
```bash
# Modifica /etc/php/8.2/fpm/php.ini (Ubuntu) o /etc/php.ini (CentOS)
sudo nano /etc/php/8.2/fpm/php.ini

# Configurazioni raccomandate:
memory_limit = 512M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_vars = 3000
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
```

### **1.4 Installazione Composer**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### **1.5 Installazione Node.js**
```bash
# Usando NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verifica installazione
node --version
npm --version
```

---

## 🗄️ **FASE 2: CONFIGURAZIONE DATABASE**

### **2.1 Installazione MySQL**
```bash
# Ubuntu/Debian
sudo apt install mysql-server -y

# CentOS/RHEL
sudo dnf install mysql-server -y
```

### **2.2 Configurazione MySQL**
```bash
# Avvio e abilitazione servizio
sudo systemctl start mysql
sudo systemctl enable mysql

# Configurazione sicurezza
sudo mysql_secure_installation

# Accesso MySQL
sudo mysql -u root -p
```

### **2.3 Creazione Database e Utente**
```sql
-- Creazione database
CREATE DATABASE ddtbylogix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Creazione utente dedicato
CREATE USER 'ddtbylogix_user'@'localhost' IDENTIFIED BY 'PASSWORD_SICURA_QUI';

-- Assegnazione privilegi
GRANT ALL PRIVILEGES ON ddtbylogix.* TO 'ddtbylogix_user'@'localhost';
FLUSH PRIVILEGES;

-- Verifica
SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = 'ddtbylogix_user';

EXIT;
```

---

## 🌐 **FASE 3: CONFIGURAZIONE WEB SERVER**

### **3.1 Installazione e Configurazione Nginx**
```bash
# Installazione
sudo apt install nginx -y

# Creazione configurazione sito
sudo nano /etc/nginx/sites-available/ddtbylogix
```

**Configurazione Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/ddtbylogix/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static Assets Caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # File Upload Limits
    client_max_body_size 64M;
}
```

```bash
# Abilitazione sito
sudo ln -s /etc/nginx/sites-available/ddtbylogix /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### **3.2 Configurazione SSL con Let's Encrypt**
```bash
# Installazione Certbot
sudo apt install certbot python3-certbot-nginx -y

# Ottenimento certificato
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Aggiungi: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 📦 **FASE 4: DEPLOY APPLICAZIONE**

### **4.1 Preparazione Directory**
```bash
# Creazione directory applicazione
sudo mkdir -p /var/www/ddtbylogix
sudo chown -R $USER:www-data /var/www/ddtbylogix
sudo chmod -R 755 /var/www/ddtbylogix
```

### **4.2 Clone Repository**
```bash
cd /var/www
git clone https://github.com/your-username/ddtbylogix.git
cd ddtbylogix

# Checkout branch produzione (se esiste)
git checkout production || git checkout main
```

### **4.3 Installazione Dipendenze**
```bash
# Dipendenze PHP
composer install --no-dev --optimize-autoloader

# Dipendenze Node.js
npm ci --only=production

# Build assets
npm run build
```

### **4.4 Configurazione Environment**
```bash
# Copia file environment
cp .env.example .env

# Modifica configurazione
nano .env
```

**Configurazione .env per Produzione:**
```env
APP_NAME="DDT by Logix"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

APP_LOCALE=it
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=it_IT

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ddtbylogix
DB_USERNAME=ddtbylogix_user
DB_PASSWORD=PASSWORD_SICURA_QUI

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error

# Security
BCRYPT_ROUNDS=12
```

### **4.5 Configurazione Laravel**
```bash
# Generazione chiave applicazione
php artisan key:generate

# Esecuzione migrations
php artisan migrate --force

# Seeding database (se necessario)
php artisan db:seed --force

# Cache ottimizzazioni
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Ottimizzazione Filament
php artisan filament:optimize
```

### **4.6 Configurazione Permessi**
```bash
# Permessi directory
sudo chown -R www-data:www-data /var/www/ddtbylogix/storage
sudo chown -R www-data:www-data /var/www/ddtbylogix/bootstrap/cache
sudo chmod -R 775 /var/www/ddtbylogix/storage
sudo chmod -R 775 /var/www/ddtbylogix/bootstrap/cache

# Permessi file
find /var/www/ddtbylogix -type f -exec chmod 644 {} \;
find /var/www/ddtbylogix -type d -exec chmod 755 {} \;
```

---

## 🔄 **FASE 5: SCRIPT DEPLOY AUTOMATICO**

### **5.1 Creazione Script Deploy**
```bash
# Creazione script
sudo nano /usr/local/bin/deploy-ddtbylogix.sh
```

**Script Deploy Completo:**
```bash
#!/bin/bash

# DDT by Logix - Script Deploy Produzione
# Versione: 1.0
# Autore: Sistema Deploy Automatico

set -e

# Configurazioni
APP_DIR="/var/www/ddtbylogix"
BACKUP_DIR="/var/backups/ddtbylogix"
LOG_FILE="/var/log/ddtbylogix-deploy.log"
BRANCH="production"

# Funzioni
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a $LOG_FILE
}

error_exit() {
    log "ERRORE: $1"
    exit 1
}

# Verifica prerequisiti
check_prerequisites() {
    log "Verifica prerequisiti..."
    
    command -v git >/dev/null 2>&1 || error_exit "Git non installato"
    command -v composer >/dev/null 2>&1 || error_exit "Composer non installato"
    command -v npm >/dev/null 2>&1 || error_exit "Node.js/NPM non installato"
    command -v php >/dev/null 2>&1 || error_exit "PHP non installato"
    
    log "Prerequisiti verificati ✓"
}

# Backup database
backup_database() {
    log "Backup database..."
    
    mkdir -p $BACKUP_DIR
    BACKUP_FILE="$BACKUP_DIR/db_backup_$(date +%Y%m%d_%H%M%S).sql"
    
    mysqldump -u ddtbylogix_user -p ddtbylogix > $BACKUP_FILE || error_exit "Backup database fallito"
    
    log "Database backup creato: $BACKUP_FILE ✓"
}

# Backup files
backup_files() {
    log "Backup files applicazione..."
    
    BACKUP_TAR="$BACKUP_DIR/app_backup_$(date +%Y%m%d_%H%M%S).tar.gz"
    tar -czf $BACKUP_TAR -C /var/www ddtbylogix || error_exit "Backup files fallito"
    
    log "Files backup creato: $BACKUP_TAR ✓"
}

# Manutenzione ON
maintenance_on() {
    log "Attivazione modalità manutenzione..."
    cd $APP_DIR
    php artisan down --message="Aggiornamento in corso..." --retry=60
    log "Modalità manutenzione attivata ✓"
}

# Manutenzione OFF
maintenance_off() {
    log "Disattivazione modalità manutenzione..."
    cd $APP_DIR
    php artisan up
    log "Modalità manutenzione disattivata ✓"
}

# Deploy applicazione
deploy_application() {
    log "Deploy applicazione..."
    
    cd $APP_DIR
    
    # Git pull
    log "Aggiornamento codice da repository..."
    git fetch origin
    git reset --hard origin/$BRANCH || error_exit "Git pull fallito"
    
    # Composer install
    log "Installazione dipendenze PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction || error_exit "Composer install fallito"
    
    # NPM install e build
    log "Installazione dipendenze Node.js..."
    npm ci --only=production || error_exit "NPM install fallito"
    
    log "Build assets..."
    npm run build || error_exit "Build assets fallito"
    
    # Laravel optimizations
    log "Ottimizzazioni Laravel..."
    php artisan migrate --force || error_exit "Migrations fallite"
    php artisan config:cache || error_exit "Config cache fallito"
    php artisan route:cache || error_exit "Route cache fallito"
    php artisan view:cache || error_exit "View cache fallito"
    php artisan event:cache || error_exit "Event cache fallito"
    
    # Filament optimizations
    log "Ottimizzazioni Filament..."
    php artisan filament:optimize || error_exit "Filament optimize fallito"
    
    # Permessi
    log "Configurazione permessi..."
    sudo chown -R www-data:www-data storage bootstrap/cache
    sudo chmod -R 775 storage bootstrap/cache
    
    log "Deploy completato ✓"
}

# Verifica deploy
verify_deployment() {
    log "Verifica deploy..."
    
    # Test connessione database
    cd $APP_DIR
    php artisan tinker --execute="DB::connection()->getPdo();" || error_exit "Test database fallito"
    
    # Test HTTP
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://your-domain.com/admin)
    if [ "$HTTP_CODE" != "200" ]; then
        error_exit "Test HTTP fallito (Code: $HTTP_CODE)"
    fi
    
    log "Verifica deploy completata ✓"
}

# Cleanup
cleanup() {
    log "Cleanup files temporanei..."
    
    # Rimozione backup vecchi (>30 giorni)
    find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
    find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
    
    # Cleanup cache
    cd $APP_DIR
    php artisan cache:clear
    
    log "Cleanup completato ✓"
}

# Funzione principale
main() {
    log "=== INIZIO DEPLOY DDT BY LOGIX ==="
    
    check_prerequisites
    backup_database
    backup_files
    maintenance_on
    
    # Deploy con gestione errori
    if deploy_application; then
        verify_deployment
        maintenance_off
        cleanup
        log "=== DEPLOY COMPLETATO CON SUCCESSO ==="
    else
        log "=== DEPLOY FALLITO - RIPRISTINO BACKUP ==="
        maintenance_off
        exit 1
    fi
}

# Esecuzione
main "$@"
```

### **5.2 Configurazione Script**
```bash
# Permessi esecuzione
sudo chmod +x /usr/local/bin/deploy-ddtbylogix.sh

# Test script
sudo /usr/local/bin/deploy-ddtbylogix.sh
```

### **5.3 Automazione Deploy (Opzionale)**
```bash
# Webhook per deploy automatico
sudo nano /var/www/webhook-deploy.php
```

**Webhook Deploy:**
```php
<?php
// Webhook per deploy automatico
// Configurare nel repository GitHub/GitLab

$secret = 'YOUR_WEBHOOK_SECRET';
$payload = file_get_contents('php://input');
$signature = hash_hmac('sha256', $payload, $secret);

if (hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '')) {
    $data = json_decode($payload, true);
    
    if ($data['ref'] === 'refs/heads/production') {
        exec('/usr/local/bin/deploy-ddtbylogix.sh > /dev/null 2>&1 &');
        http_response_code(200);
        echo 'Deploy triggered';
    }
} else {
    http_response_code(403);
    echo 'Forbidden';
}
?>
```

---

## 🔐 **FASE 6: SICUREZZA E MONITORAGGIO**

### **6.1 Configurazione Firewall**
```bash
# UFW (Ubuntu)
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp

# Fail2ban
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
```

### **6.2 Monitoraggio Log**
```bash
# Configurazione logrotate
sudo nano /etc/logrotate.d/ddtbylogix
```

```
/var/www/ddtbylogix/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
```

### **6.3 Backup Automatico**
```bash
# Script backup automatico
sudo nano /usr/local/bin/backup-ddtbylogix.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/ddtbylogix"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u ddtbylogix_user -p ddtbylogix | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www ddtbylogix

# Cleanup old backups (>7 days)
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete
```

```bash
# Crontab per backup automatico
sudo crontab -e
# Aggiungi: 0 2 * * * /usr/local/bin/backup-ddtbylogix.sh
```

---

## 🚀 **FASE 7: PRIMO AVVIO E CONFIGURAZIONE**

### **7.1 Creazione Utente Admin**
```bash
cd /var/www/ddtbylogix
php artisan tinker
```

```php
// Creazione primo utente admin
$user = new App\Models\User();
$user->name = 'Administrator';
$user->email = 'admin@your-domain.com';
$user->password = Hash::make('password_sicura_temporanea');
$user->role = 'admin';
$user->is_active = true;
$user->save();

// Assegnazione ruolo admin (se usa Spatie Permission)
$user->assignRole('admin');
```

### **7.2 Verifica Funzionalità**
1. **Accesso Admin Panel**: `https://your-domain.com/admin`
2. **Test CRUD**: Verifica creazione/modifica progetti, clienti, documenti
3. **Test Scanner QR**: Verifica funzionalità scanner pubblico
4. **Test API**: Verifica endpoint API se utilizzati

---

## 📊 **FASE 8: OTTIMIZZAZIONI PERFORMANCE**

### **8.1 Configurazione OPcache**
```bash
# /etc/php/8.2/fpm/conf.d/10-opcache.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### **8.2 Configurazione Redis (Opzionale)**
```bash
# Installazione Redis
sudo apt install redis-server -y

# Configurazione Laravel per Redis
# Nel file .env:
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 🆘 **TROUBLESHOOTING**

### **Problemi Comuni**

**1. Errore 500 - Internal Server Error**
```bash
# Verifica log
tail -f /var/www/ddtbylogix/storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Verifica permessi
sudo chown -R www-data:www-data /var/www/ddtbylogix/storage
sudo chmod -R 775 /var/www/ddtbylogix/storage
```

**2. Errore Database Connection**
```bash
# Test connessione
mysql -u ddtbylogix_user -p -h localhost ddtbylogix

# Verifica configurazione .env
grep DB_ /var/www/ddtbylogix/.env
```

**3. Assets non caricati**
```bash
# Rebuild assets
cd /var/www/ddtbylogix
npm run build
php artisan view:clear
```

---

## 📞 **SUPPORTO**

### **Contatti Tecnici**
- **Email**: support@your-domain.com
- **Documentazione**: https://docs.your-domain.com
- **Repository**: https://github.com/your-username/ddtbylogix

### **Log Files Importanti**
- **Laravel**: `/var/www/ddtbylogix/storage/logs/laravel.log`
- **Nginx**: `/var/log/nginx/error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **Deploy**: `/var/log/ddtbylogix-deploy.log`

---

## ✅ **CHECKLIST FINALE**

- [ ] Server configurato con tutti i requisiti
- [ ] Database MySQL creato e configurato
- [ ] Nginx configurato con SSL
- [ ] Applicazione deployata e funzionante
- [ ] Script deploy configurato
- [ ] Backup automatico attivo
- [ ] Monitoraggio log configurato
- [ ] Firewall configurato
- [ ] Utente admin creato
- [ ] Test funzionalità completati
- [ ] Documentazione aggiornata

---

**🎉 INSTALLAZIONE COMPLETATA!**

Il sistema DDT by Logix è ora pronto per l'ambiente di produzione con architettura Filament-only ottimizzata per performance e sicurezza.