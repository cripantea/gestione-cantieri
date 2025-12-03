# Deploy con Laravel Envoy

Questo progetto utilizza Laravel Envoy per automatizzare il processo di deploy sul server di produzione.

## 📋 Prerequisiti

### Sul server di produzione:
- PHP 8.2 o superiore
- Composer
- Git
- Nginx o Apache
- Database (MySQL/PostgreSQL/SQLite)
- Accesso SSH

### Sul tuo computer locale:
- Laravel Envoy installato (già incluso in questo progetto)
- Accesso SSH al server

## 🔧 Configurazione Iniziale

### 1. Configura il file Envoy.blade.php

Apri `Envoy.blade.php` e modifica:

```php
$repository = 'git@github.com:username/gestionale-cantieri.git'; // Il tuo repository Git
$branch = 'main'; // Branch da deployare
$appDir = '/var/www/gestionale-cantieri'; // Path sul server
```

E nella sezione `@servers`:
```php
@servers(['production' => 'user@your-server.com'])
```

### 2. Setup iniziale del server

La prima volta, esegui il setup per creare la struttura delle directory:

```bash
envoy run setup
```

Questo creerà:
```
/var/www/gestionale-cantieri/
├── releases/          # Directory con tutte le release
├── current/           # Symlink alla release attiva
└── shared/            # File e directory condivise tra release
    ├── .env           # File di configurazione
    └── storage/       # Storage persistente
```

### 3. Configura il file .env sul server

Dopo il setup, accedi al server e configura:

```bash
ssh user@your-server.com
nano /var/www/gestionale-cantieri/shared/.env
```

Imposta:
- `APP_ENV=production`
- `APP_DEBUG=false`
- Credenziali database
- `APP_KEY` (genera con: `php artisan key:generate`)

## 🚀 Deploy

### Deploy standard

Per deployare l'applicazione:

```bash
envoy run deploy
```

Questo eseguirà automaticamente:
1. ✅ Clone del repository
2. ✅ Installazione dipendenze (Composer)
3. ✅ Link delle directory shared (storage, .env)
4. ✅ Ottimizzazione (cache config, route, view)
5. ✅ Esecuzione migrazioni database
6. ✅ Attivazione della nuova release
7. ✅ Pulizia vecchie release (mantiene le ultime 5)
8. ✅ Restart servizi (PHP-FPM, Nginx)

### Altre task disponibili

#### Rollback

Se qualcosa va storto, torna alla release precedente:

```bash
envoy run rollback
```

#### Status

Verifica lo stato dell'applicazione:

```bash
envoy run status
```

Mostra:
- Release attualmente attiva
- Ultime 5 release disponibili
- Stato PHP-FPM
- Stato Nginx

#### Logs

Visualizza gli ultimi log:

```bash
envoy run logs
```

#### Task singole

Puoi eseguire anche le singole task:

```bash
envoy run git-clone          # Solo clone del repository
envoy run install-dependencies # Solo installazione dipendenze
envoy run migrate             # Solo migrazioni
envoy run optimize            # Solo ottimizzazione
envoy run restart-services    # Solo restart servizi
```

## 📁 Struttura delle Release

Envoy utilizza un sistema di release multiple per permettere rollback rapidi:

```
/var/www/gestionale-cantieri/
├── current -> releases/20251202120000  # Symlink alla release attiva
├── releases/
│   ├── 20251202120000/                 # Release corrente
│   ├── 20251201110000/                 # Release precedente
│   └── 20251130100000/                 # Release ancora più vecchia
└── shared/
    ├── .env
    └── storage/
```

Ogni release ha:
- Il proprio codice
- Symlink a `shared/.env`
- Symlink a `shared/storage`

## 🔒 Configurazione Nginx

Configura Nginx per puntare a `current/public`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/gestionale-cantieri/current/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔑 Configurazione SSH

Per evitare di inserire la password ogni volta:

### 1. Genera chiave SSH (se non l'hai già):
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
```

### 2. Copia la chiave sul server:
```bash
ssh-copy-id user@your-server.com
```

### 3. Per GitHub/GitLab:
Aggiungi la chiave pubblica del server al tuo repository:
```bash
ssh user@your-server.com "cat ~/.ssh/id_ed25519.pub"
```

## 📝 Workflow Consigliato

1. **Sviluppo locale**
   ```bash
   git add .
   git commit -m "Feature: nuova funzionalità"
   git push origin main
   ```

2. **Deploy in produzione**
   ```bash
   envoy run deploy
   ```

3. **Se c'è un problema**
   ```bash
   envoy run rollback
   ```

4. **Verifica stato**
   ```bash
   envoy run status
   ```

## 🛠️ Troubleshooting

### Errore: "Permission denied"
```bash
ssh user@your-server.com
sudo chown -R www-data:www-data /var/www/gestionale-cantieri
sudo chmod -R 775 /var/www/gestionale-cantieri/shared/storage
```

### Errore durante le migrazioni
Accedi al server e verifica il database:
```bash
ssh user@your-server.com
cd /var/www/gestionale-cantieri/current
php artisan migrate:status
```

### Cache problematiche
Pulisci tutte le cache:
```bash
ssh user@your-server.com
cd /var/www/gestionale-cantieri/current
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🎯 Best Practices

1. **Testa sempre in staging** prima di deployare in produzione
2. **Backup del database** prima di ogni deploy con migrazioni
3. **Monitora i log** dopo ogni deploy
4. **Mantieni aggiornate le dipendenze**
5. **Usa release semantico** (es: v1.0.0, v1.1.0)

## 📚 Risorse

- [Laravel Envoy Documentation](https://laravel.com/docs/envoy)
- [Deployment Best Practices](https://laravel.com/docs/deployment)

## 🆘 Supporto

Per problemi o domande:
1. Controlla i log: `envoy run logs`
2. Verifica lo stato: `envoy run status`
3. In caso di emergenza: `envoy run rollback`

