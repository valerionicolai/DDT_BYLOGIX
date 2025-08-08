# ✅ Checklist Sistemista - DDT by Logix

## 📋 **CHECKLIST PRE-INSTALLAZIONE**

### **Requisiti Server**
- [ ] **OS**: Ubuntu 22.04 LTS / CentOS 8+ / RHEL 8+
- [ ] **RAM**: Minimo 2GB, Raccomandato 4GB+
- [ ] **Storage**: Minimo 20GB SSD disponibili
- [ ] **CPU**: 2 vCPU minimo
- [ ] **Connessione Internet**: Stabile per download dipendenze

### **Accessi e Permessi**
- [ ] **Root/Sudo**: Accesso amministrativo al server
- [ ] **SSH**: Accesso SSH configurato
- [ ] **Dominio**: Dominio configurato e puntato al server
- [ ] **DNS**: Record A/AAAA configurati correttamente

---

## 🔧 **CHECKLIST INSTALLAZIONE SOFTWARE**

### **PHP 8.2+**
- [ ] PHP 8.2+ installato
- [ ] Estensioni PHP richieste:
  - [ ] `php-fpm`
  - [ ] `php-mysql`
  - [ ] `php-zip`
  - [ ] `php-gd`
  - [ ] `php-mbstring`
  - [ ] `php-curl`
  - [ ] `php-xml`
  - [ ] `php-bcmath`
  - [ ] `php-intl`
  - [ ] `php-opcache`
- [ ] Configurazione PHP ottimizzata (memory_limit, upload_max_filesize, etc.)

### **Database MySQL**
- [ ] MySQL 8.0+ / MariaDB 10.6+ installato
- [ ] Servizio MySQL avviato e abilitato
- [ ] Database `dttbylogix` creato
- [ ] Utente dedicato `dttbylogix_user` creato
- [ ] Privilegi assegnati correttamente
- [ ] Test connessione database funzionante

### **Web Server Nginx**
- [ ] Nginx installato
- [ ] Configurazione virtual host creata
- [ ] SSL/TLS configurato (Let's Encrypt)
- [ ] Redirect HTTP → HTTPS attivo
- [ ] Test configurazione Nginx (`nginx -t`)
- [ ] Servizio Nginx riavviato

### **Node.js e NPM**
- [ ] Node.js 18+ LTS installato
- [ ] NPM funzionante
- [ ] Verifica versioni (`node --version`, `npm --version`)

### **Composer**
- [ ] Composer 2.6+ installato globalmente
- [ ] Composer accessibile da PATH
- [ ] Test funzionamento (`composer --version`)

---

## 📦 **CHECKLIST DEPLOY APPLICAZIONE**

### **Repository e Codice**
- [ ] Repository clonato in `/var/www/dttbylogix`
- [ ] Branch corretto selezionato (production/main)
- [ ] Permessi directory corretti
- [ ] File `.env` configurato da `.env.production`

### **Dipendenze**
- [ ] `composer install --no-dev --optimize-autoloader` eseguito
- [ ] `npm ci --only=production` eseguito
- [ ] `npm run build` completato con successo
- [ ] Assets compilati presenti in `public/build`

### **Configurazione Laravel**
- [ ] `APP_KEY` generata (`php artisan key:generate`)
- [ ] File `.env` configurato correttamente
- [ ] Database migrations eseguite (`php artisan migrate --force`)
- [ ] Seeder eseguiti se necessari (`php artisan db:seed --force`)

### **Ottimizzazioni**
- [ ] `php artisan config:cache` eseguito
- [ ] `php artisan route:cache` eseguito
- [ ] `php artisan view:cache` eseguito
- [ ] `php artisan event:cache` eseguito
- [ ] `php artisan filament:optimize` eseguito

### **Permessi File System**
- [ ] Directory `storage` con permessi 775
- [ ] Directory `bootstrap/cache` con permessi 775
- [ ] Owner `www-data:www-data` per storage e cache
- [ ] File applicazione con permessi corretti (644/755)

---

## 🔐 **CHECKLIST SICUREZZA**

### **Firewall**
- [ ] UFW/iptables configurato
- [ ] Porte 22, 80, 443 aperte
- [ ] Porta 3306 (MySQL) bloccata dall'esterno
- [ ] Fail2ban installato e configurato

### **SSL/TLS**
- [ ] Certificato SSL valido installato
- [ ] Redirect HTTP → HTTPS funzionante
- [ ] Test SSL (SSLLabs A+ rating)
- [ ] Auto-renewal certificato configurato

### **Configurazioni Sicurezza**
- [ ] Headers sicurezza configurati in Nginx
- [ ] File `.env` non accessibile via web
- [ ] Directory `.git` protetta
- [ ] PHP version nascosta
- [ ] Server tokens disabilitati

---

## 🚀 **CHECKLIST FUNZIONALITÀ**

### **Test Accesso**
- [ ] **URL principale**: `https://your-domain.com` → redirect a `/admin`
- [ ] **Admin Panel**: `https://your-domain.com/admin` accessibile
- [ ] **Login**: Form login funzionante
- [ ] **Dashboard**: Dashboard admin caricata correttamente

### **Test CRUD Filament**
- [ ] **Progetti**: Creazione, modifica, eliminazione
- [ ] **Clienti**: Gestione clienti completa
- [ ] **Documenti**: Upload e gestione documenti
- [ ] **Utenti**: Gestione utenti e ruoli
- [ ] **Materiali**: Gestione materiali e barcode

### **Test Scanner QR**
- [ ] **Scanner pubblico**: Accessibile senza login
- [ ] **Scansione barcode**: Funzionamento corretto
- [ ] **Visualizzazione materiali**: Dati mostrati correttamente

### **Test API (se utilizzate)**
- [ ] **Endpoint API**: Risposte corrette
- [ ] **Autenticazione API**: Sanctum funzionante
- [ ] **Rate limiting**: Configurato correttamente

---

## 📊 **CHECKLIST PERFORMANCE**

### **Ottimizzazioni Server**
- [ ] OPcache PHP abilitato e configurato
- [ ] Gzip compression attiva in Nginx
- [ ] Static assets caching configurato
- [ ] Database query optimization

### **Monitoraggio**
- [ ] Log rotation configurato
- [ ] Disk space monitoring
- [ ] Memory usage monitoring
- [ ] CPU usage monitoring

### **Test Performance**
- [ ] **Page load time**: < 2 secondi
- [ ] **Time to first byte**: < 500ms
- [ ] **Asset loading**: Ottimizzato
- [ ] **Database queries**: Efficienti

---

## 🔄 **CHECKLIST BACKUP E DEPLOY**

### **Backup Automatico**
- [ ] Script backup database configurato
- [ ] Script backup files configurato
- [ ] Cron job backup attivo
- [ ] Test restore backup funzionante
- [ ] Retention policy configurata (7-30 giorni)

### **Deploy Automatico**
- [ ] Script deploy `/usr/local/bin/deploy-dttbylogix.sh` installato
- [ ] Permessi esecuzione script corretti
- [ ] Test deploy manuale funzionante
- [ ] Webhook deploy configurato (opzionale)
- [ ] Rollback procedure testata

---

## 📞 **CHECKLIST POST-INSTALLAZIONE**

### **Documentazione**
- [ ] Credenziali admin documentate
- [ ] URL e accessi documentati
- [ ] Procedure backup documentate
- [ ] Procedure deploy documentate
- [ ] Contatti supporto forniti

### **Training e Handover**
- [ ] Demo funzionalità completata
- [ ] Procedure operative spiegate
- [ ] Accessi consegnati al cliente
- [ ] Documentazione consegnata
- [ ] Supporto post-go-live definito

### **Monitoraggio Iniziale**
- [ ] **Prima settimana**: Monitoraggio intensivo
- [ ] **Log errors**: Verifica giornaliera
- [ ] **Performance**: Baseline stabilita
- [ ] **Backup**: Verifica funzionamento
- [ ] **Uptime**: Monitoraggio attivo

---

## 🆘 **CHECKLIST TROUBLESHOOTING**

### **Problemi Comuni Verificati**
- [ ] **500 Error**: Procedure risoluzione testate
- [ ] **Database connection**: Procedure debug testate
- [ ] **Assets not loading**: Procedure risoluzione testate
- [ ] **Permission issues**: Procedure fix testate
- [ ] **SSL issues**: Procedure risoluzione testate

### **Log Files Configurati**
- [ ] **Laravel logs**: `/var/www/dttbylogix/storage/logs/laravel.log`
- [ ] **Nginx logs**: `/var/log/nginx/error.log`
- [ ] **PHP-FPM logs**: `/var/log/php8.2-fpm.log`
- [ ] **Deploy logs**: `/var/log/dttbylogix-deploy.log`

---

## ✅ **SIGN-OFF FINALE**

### **Verifiche Finali**
- [ ] Tutti i test funzionali passati
- [ ] Performance accettabili
- [ ] Sicurezza implementata
- [ ] Backup funzionanti
- [ ] Deploy automatico testato
- [ ] Documentazione completa

### **Approvazioni**
- [ ] **Sistemista**: Installazione completata ✓
- [ ] **Cliente**: Funzionalità approvate ✓
- [ ] **Project Manager**: Go-live autorizzato ✓

---

**Data Completamento**: _______________

**Sistemista**: _______________

**Firma**: _______________

---

## 📋 **COMANDI RAPIDI RIFERIMENTO**

```bash
# Deploy manuale
sudo /usr/local/bin/deploy-dttbylogix.sh

# Backup manuale
sudo /usr/local/bin/backup-dttbylogix.sh

# Verifica logs
tail -f /var/www/dttbylogix/storage/logs/laravel.log

# Restart servizi
sudo systemctl restart nginx php8.2-fpm mysql

# Test configurazione
nginx -t
php -v
mysql --version

# Verifica spazio disco
df -h

# Verifica processi
ps aux | grep -E "(nginx|php-fpm|mysql)"
```

---

**🎉 INSTALLAZIONE COMPLETATA CON SUCCESSO!**