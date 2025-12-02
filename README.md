# 🏗️ Gestionale Cantieri Edili
Sistema completo per la gestione di cantieri edili con tracciamento attività, scadenze e documentazione.
## 📋 Caratteristiche
### 🎯 Gestione Cantieri
- ✅ Creazione e gestione cantieri con dati completi
- ✅ Stati cantiere (Pianificazione, Apertura, Attivo, Sospeso, Completato, Chiuso)
- ✅ Tracciamento date e importi lavori
- ✅ Soft delete per recupero dati
### 📝 Fasi Procedurali Pre-configurate
#### 1. **Apertura Nuovo Cantiere** (7 attività)
- EdilConnect - Verifica Congruità
- Notifica Preliminare ASL
- Apertura Posizione INAIL
- Apertura Posizione INPS
- Richiesta DURC
- Predisposizione Cartellonistica
- Registro Carico/Scarico Rifiuti
#### 2. **Adempimenti Ricorrenti** (5 attività)
- Rinnovo DURC (ogni 120 giorni)
- Aggiornamento POS (mensile)
- Verifica Idoneità Sanitaria (annuale)
- Manutenzione Attrezzature (trimestrale)
- Riunioni Periodiche di Sicurezza (mensile)
#### 3. **Formazione e Addestramento** (5 attività)
- Formazione Generale Sicurezza (4 ore)
- Formazione Specifica Rischio Alto (12 ore)
- Addestramento DPI Anticaduta
- Corso Primo Soccorso (12 ore)
- Corso Antincendio Rischio Medio (8 ore)
#### 4. **Gestione Ordinaria** (5 attività)
- Gestione Presenze e Timbrature
- Controllo Documentazione Subappaltatori
- Gestione Ordini e Bolle
- Gestione Rifiuti e Formulari
- Reportistica e SAL
### ✅ Sistema di Tracciamento
- **110 passi operativi** dettagliati per ogni attività
- Checkbox interattive per completamento passi
- Stati attività: Da Fare, In Corso, Completata, Non Applicabile
- Progress bar per monitoraggio avanzamento
- Tracciamento date completamento e utenti responsabili
## 🚀 Installazione
### Requisiti
- PHP 8.2 o superiore
- Composer
- Database (SQLite per sviluppo, MySQL/PostgreSQL per produzione)
### Setup Locale
1. **Clona e installa**
```bash
git clone https://github.com/username/gestionale-cantieri.git
cd gestionale-cantieri
composer install
```
2. **Configura**
```bash
cp .env.example .env
php artisan key:generate
```
3. **Database**
```bash
touch database/database.sqlite
php artisan migrate --seed
```
4. **Avvia**
```bash
php artisan serve
```
Accedi a: http://127.0.0.1:8000
## 🎨 Tecnologie
- **Laravel 11** - Framework PHP
- **Tailwind CSS** - Styling
- **Alpine.js** - Interattività
- **Laravel Envoy** - Deploy automation
## 🚢 Deploy
```bash
envoy run setup    # Prima volta
envoy run deploy   # Deploy
envoy run rollback # Rollback
```
📖 Vedi [DEPLOY.md](DEPLOY.md) per dettagli completi.
## 📊 Features
- Dashboard con statistiche
- Gestione completa cantieri
- 22 attività pre-configurate
- 110 passi operativi
- Sistema scadenze ricorrenti
- Gestione documenti
- Progress tracking
## 🔧 Configurazione
Il sistema è pre-configurato per:
- Locale italiana (it_IT)
- Timezone Europe/Rome
- Formato date italiano
## 📝 Licenza
MIT License
---
Sviluppato con ❤️ per la gestione cantieri edili
