# Script di comandi utili per Envoy

## Comandi Base

# Setup iniziale del server (solo la prima volta)
envoy run setup

# Deploy standard
envoy run deploy

# Rollback alla release precedente
envoy run rollback

# Verifica stato applicazione
envoy run status

# Visualizza log
envoy run logs

## Task Singole

# Solo clone repository
envoy run git-clone

# Solo installazione dipendenze
envoy run install-dependencies

# Solo ottimizzazione
envoy run optimize

# Solo migrazioni
envoy run migrate

# Solo restart servizi
envoy run restart-services

## Comandi SSH Utili

# Accedi al server
ssh user@your-server.com

# Vai alla directory dell'applicazione corrente
ssh user@your-server.com "cd /var/www/gestionale-cantieri/current && bash"

# Pulisci cache manualmente
ssh user@your-server.com "cd /var/www/gestionale-cantieri/current && php artisan cache:clear"

# Verifica permessi storage
ssh user@your-server.com "ls -la /var/www/gestionale-cantieri/shared/storage"

# Tail dei log in tempo reale
ssh user@your-server.com "tail -f /var/www/gestionale-cantieri/shared/storage/logs/laravel.log"

## Manutenzione

# Backup database prima del deploy
ssh user@your-server.com "cd /var/www/gestionale-cantieri/current && php artisan db:backup"

# Controlla spazio disco
ssh user@your-server.com "df -h"

# Controlla processi PHP
ssh user@your-server.com "ps aux | grep php"

# Riavvia PHP-FPM manualmente
ssh user@your-server.com "sudo systemctl restart php8.2-fpm"

# Riavvia Nginx manualmente
ssh user@your-server.com "sudo systemctl reload nginx"

## Debug

# Verifica configurazione Nginx
ssh user@your-server.com "sudo nginx -t"

# Controlla log Nginx errori
ssh user@your-server.com "sudo tail -f /var/log/nginx/error.log"

# Controlla log PHP-FPM
ssh user@your-server.com "sudo tail -f /var/log/php8.2-fpm.log"

# Verifica connessione database
ssh user@your-server.com "cd /var/www/gestionale-cantieri/current && php artisan tinker --execute='DB::connection()->getPdo();'"

## Workflow Completo

# 1. Sviluppa in locale
git add .
git commit -m "Feature: descrizione"
git push origin main

# 2. Deploy in produzione
envoy run deploy

# 3. Se c'è un problema
envoy run rollback

# 4. Verifica che tutto funzioni
envoy run status

