<?php

namespace Database\Seeders;

use App\Models\FaseProcedurale;
use App\Models\Attivita;
use App\Models\PassoAttivita;
use App\Models\ScadenzaRicorrente;
use Illuminate\Database\Seeder;

class FasiProceduraliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fase 1: Apertura Nuovo Cantiere
        $faseApertura = FaseProcedurale::create([
            'nome' => 'Apertura Nuovo Cantiere',
            'descrizione' => 'Tutte le attività necessarie per aprire un nuovo cantiere',
            'icona' => 'folder-open',
            'tipologia' => 'apertura',
            'ordine' => 1,
            'is_attiva' => true,
        ]);

        // Attività fase apertura
        $attivitaApertura = [
            [
                'titolo' => 'EdilConnect - Verifica Congruità',
                'descrizione' => 'Accesso al portale EdilConnect per verifica congruità della manodopera',
                'url_portale' => 'https://www.edilconnect.it',
                'credenziali_note' => 'Usa credenziali aziendali',
                'is_critica' => true,
                'ordine' => 1,
                'passi' => [
                    'Accedi al portale EdilConnect',
                    'Seleziona "Nuova Verifica Congruità"',
                    'Inserisci i dati del cantiere',
                    'Carica la documentazione richiesta',
                    'Invia la richiesta e salva il numero protocollo',
                ],
            ],
            [
                'titolo' => 'Notifica Preliminare',
                'descrizione' => 'Invio notifica preliminare alla ASL competente',
                'url_portale' => null,
                'is_critica' => true,
                'ordine' => 2,
                'passi' => [
                    'Compila il modulo di notifica preliminare',
                    'Allega il PSC (Piano di Sicurezza e Coordinamento)',
                    'Invia tramite PEC alla ASL territoriale',
                    'Salva la ricevuta di consegna',
                    'Archivia copia nel fascicolo cantiere',
                ],
            ],
            [
                'titolo' => 'Apertura Posizione INAIL',
                'descrizione' => 'Apertura posizione assicurativa presso INAIL',
                'url_portale' => 'https://www.inail.it',
                'is_critica' => true,
                'ordine' => 3,
                'passi' => [
                    'Accedi al portale INAIL con SPID',
                    'Seleziona "Comunicazioni telematiche"',
                    'Compila il modulo di apertura cantiere',
                    'Inserisci dati previsionali (importo lavori, durata, addetti)',
                    'Invia e conserva il numero protocollo',
                ],
            ],
            [
                'titolo' => 'Apertura Posizione INPS',
                'descrizione' => 'Comunicazione apertura cantiere a INPS',
                'url_portale' => 'https://www.inps.it',
                'is_critica' => true,
                'ordine' => 4,
                'passi' => [
                    'Accedi al portale INPS',
                    'Vai alla sezione "Gestione Cantieri Edili"',
                    'Compila il modulo di apertura',
                    'Inserisci i dati del cantiere e delle maestranze',
                    'Salva la ricevuta di presentazione',
                ],
            ],
            [
                'titolo' => 'Richiesta DURC (Documento Unico di Regolarità Contributiva)',
                'descrizione' => 'Richiesta del DURC online',
                'url_portale' => 'https://www.sportellounicoprevidenziale.it',
                'is_critica' => true,
                'ordine' => 5,
                'passi' => [
                    'Accedi allo Sportello Unico Previdenziale',
                    'Seleziona "Richiesta DURC Online"',
                    'Inserisci i dati aziendali',
                    'Specifica la finalità (lavori pubblici/privati)',
                    'Scarica il DURC quando disponibile',
                ],
            ],
            [
                'titolo' => 'Predisposizione Cartellonistica di Cantiere',
                'descrizione' => 'Installazione cartelli obbligatori in cantiere',
                'is_critica' => true,
                'ordine' => 6,
                'passi' => [
                    'Verifica cartelli obbligatori da installare',
                    'Prepara il cartello di cantiere principale',
                    'Installa segnaletica di sicurezza',
                    'Posiziona cartello di divieto di accesso',
                    'Fotografa l\'installazione per documentazione',
                ],
            ],
            [
                'titolo' => 'Registro di Carico e Scarico Rifiuti',
                'descrizione' => 'Predisposizione registro rifiuti se necessario',
                'url_portale' => null,
                'ordine' => 7,
                'passi' => [
                    'Verifica se il cantiere produce rifiuti speciali',
                    'Predisponi il registro di carico/scarico',
                    'Identifica i codici CER dei rifiuti',
                    'Individua smaltitori autorizzati',
                    'Conserva documentazione nel fascicolo',
                ],
            ],
        ];

        foreach ($attivitaApertura as $attData) {
            $passi = $attData['passi'];
            unset($attData['passi']);
            $attData['fase_procedurale_id'] = $faseApertura->id;

            $attivita = Attivita::create($attData);

            foreach ($passi as $index => $descrizione) {
                PassoAttivita::create([
                    'attivita_id' => $attivita->id,
                    'numero_passo' => $index + 1,
                    'descrizione' => $descrizione,
                ]);
            }
        }

        // Fase 2: Adempimenti Ricorrenti
        $faseRicorrente = FaseProcedurale::create([
            'nome' => 'Adempimenti Ricorrenti',
            'descrizione' => 'Attività periodiche da svolgere durante la vita del cantiere',
            'icona' => 'calendar-repeat',
            'tipologia' => 'ricorrente',
            'ordine' => 2,
            'is_attiva' => true,
        ]);

        // Attività ricorrenti
        $attivitaRicorrenti = [
            [
                'titolo' => 'Rinnovo DURC',
                'descrizione' => 'Verifica e rinnovo del DURC ogni 120 giorni',
                'url_portale' => 'https://www.sportellounicoprevidenziale.it',
                'is_critica' => true,
                'ordine' => 1,
                'frequenza' => 'custom',
                'intervallo_giorni' => 120,
                'giorni_preavviso' => 15,
                'passi' => [
                    'Controlla la data di scadenza del DURC in corso',
                    'Accedi allo Sportello Unico Previdenziale',
                    'Richiedi nuovo DURC',
                    'Scarica e archivia nel fascicolo',
                    'Invia copia al committente se richiesto',
                ],
            ],
            [
                'titolo' => 'Aggiornamento POS (Piano Operativo di Sicurezza)',
                'descrizione' => 'Revisione e aggiornamento del POS',
                'is_critica' => true,
                'ordine' => 2,
                'frequenza' => 'mensile',
                'giorni_preavviso' => 7,
                'passi' => [
                    'Verifica modifiche organizzative nel cantiere',
                    'Aggiorna l\'analisi dei rischi se necessario',
                    'Integra nuove lavorazioni o attrezzature',
                    'Fa firmare il POS aggiornato ai lavoratori',
                    'Archivia versione aggiornata',
                ],
            ],
            [
                'titolo' => 'Verifica Idoneità Sanitaria Lavoratori',
                'descrizione' => 'Controllo scadenza visite mediche del personale',
                'is_critica' => true,
                'ordine' => 3,
                'frequenza' => 'annuale',
                'giorni_preavviso' => 30,
                'passi' => [
                    'Controlla le scadenze delle visite mediche',
                    'Prenota visite per i lavoratori in scadenza',
                    'Verifica il giudizio di idoneità',
                    'Aggiorna il registro delle visite',
                    'Archivia i certificati di idoneità',
                ],
            ],
            [
                'titolo' => 'Manutenzione e Verifica Attrezzature',
                'descrizione' => 'Controllo periodico delle attrezzature di cantiere',
                'is_critica' => true,
                'ordine' => 4,
                'frequenza' => 'trimestrale',
                'giorni_preavviso' => 15,
                'passi' => [
                    'Verifica stato ponteggi e opere provvisionali',
                    'Controlla funzionamento DPI e attrezzature',
                    'Programma manutenzione ordinaria',
                    'Documenta le verifiche effettuate',
                    'Sostituisci attrezzature non conformi',
                ],
            ],
            [
                'titolo' => 'Riunione Periodica di Sicurezza',
                'descrizione' => 'Riunione con CSE e squadra per sicurezza',
                'is_critica' => false,
                'ordine' => 5,
                'frequenza' => 'mensile',
                'giorni_preavviso' => 7,
                'passi' => [
                    'Convoca CSE, capocantiere e RLS',
                    'Analizza eventuali incidenti o near miss',
                    'Discuti criticità emerse',
                    'Pianifica azioni correttive',
                    'Redigi verbale della riunione',
                ],
            ],
        ];

        foreach ($attivitaRicorrenti as $attData) {
            $passi = $attData['passi'];
            $frequenza = $attData['frequenza'];
            $intervallo_giorni = $attData['intervallo_giorni'] ?? null;
            $giorni_preavviso = $attData['giorni_preavviso'];

            unset($attData['passi'], $attData['frequenza'], $attData['intervallo_giorni'], $attData['giorni_preavviso']);
            $attData['fase_procedurale_id'] = $faseRicorrente->id;

            $attivita = Attivita::create($attData);

            // Crea scadenza ricorrente
            ScadenzaRicorrente::create([
                'attivita_id' => $attivita->id,
                'frequenza' => $frequenza,
                'intervallo_giorni' => $intervallo_giorni,
                'giorni_preavviso' => $giorni_preavviso,
            ]);

            foreach ($passi as $index => $descrizione) {
                PassoAttivita::create([
                    'attivita_id' => $attivita->id,
                    'numero_passo' => $index + 1,
                    'descrizione' => $descrizione,
                ]);
            }
        }

        // Fase 3: Formazione e Addestramento
        $faseFormazione = FaseProcedurale::create([
            'nome' => 'Formazione e Addestramento',
            'descrizione' => 'Attività di formazione obbligatoria per i lavoratori',
            'icona' => 'graduation-cap',
            'tipologia' => 'formazione',
            'ordine' => 3,
            'is_attiva' => true,
        ]);

        $attivitaFormazione = [
            [
                'titolo' => 'Formazione Generale sulla Sicurezza (4 ore)',
                'descrizione' => 'Corso base obbligatorio per tutti i lavoratori',
                'is_critica' => true,
                'ordine' => 1,
                'passi' => [
                    'Identifica i lavoratori da formare',
                    'Prenota corso presso ente accreditato',
                    'Verifica frequenza e superamento test',
                    'Archivia attestati di formazione',
                    'Aggiorna il registro formazione',
                ],
            ],
            [
                'titolo' => 'Formazione Specifica Rischio Alto (12 ore)',
                'descrizione' => 'Formazione specifica per settore edilizia',
                'is_critica' => true,
                'ordine' => 2,
                'passi' => [
                    'Valuta il livello di rischio delle mansioni',
                    'Organizza corso di 12 ore per rischio alto',
                    'Assicura presenza di tutti i lavoratori',
                    'Verifica superamento test finale',
                    'Conserva attestati nel fascicolo personale',
                ],
            ],
            [
                'titolo' => 'Addestramento Uso DPI Anticaduta',
                'descrizione' => 'Addestramento pratico per lavori in quota',
                'is_critica' => true,
                'ordine' => 3,
                'passi' => [
                    'Seleziona lavoratori che operano in quota',
                    'Organizza sessione pratica con istruttore',
                    'Verifica corretta vestizione imbracatura',
                    'Prova utilizzo dispositivi anticaduta',
                    'Rilascia attestato di addestramento',
                ],
            ],
            [
                'titolo' => 'Corso Primo Soccorso (12 ore)',
                'descrizione' => 'Formazione addetti primo soccorso',
                'is_critica' => true,
                'ordine' => 4,
                'passi' => [
                    'Designa almeno 2 addetti al primo soccorso',
                    'Iscrivili a corso presso ente accreditato',
                    'Verifica rilascio attestato valido',
                    'Programma aggiornamento triennale',
                    'Esponi nominativi addetti in cantiere',
                ],
            ],
            [
                'titolo' => 'Corso Antincendio Rischio Medio (8 ore)',
                'descrizione' => 'Formazione addetti antincendio',
                'is_critica' => true,
                'ordine' => 5,
                'passi' => [
                    'Designa addetti antincendio',
                    'Iscrivili a corso con parte pratica',
                    'Assicura superamento esame presso VVF',
                    'Programma aggiornamento quinquennale',
                    'Esponi nominativi in cantiere',
                ],
            ],
        ];

        foreach ($attivitaFormazione as $attData) {
            $passi = $attData['passi'];
            unset($attData['passi']);
            $attData['fase_procedurale_id'] = $faseFormazione->id;

            $attivita = Attivita::create($attData);

            foreach ($passi as $index => $descrizione) {
                PassoAttivita::create([
                    'attivita_id' => $attivita->id,
                    'numero_passo' => $index + 1,
                    'descrizione' => $descrizione,
                ]);
            }
        }

        // Fase 4: Gestione Ordinaria
        $faseOrdinaria = FaseProcedurale::create([
            'nome' => 'Gestione Ordinaria',
            'descrizione' => 'Attività quotidiane e di routine del cantiere',
            'icona' => 'clipboard-list',
            'tipologia' => 'ordinaria',
            'ordine' => 4,
            'is_attiva' => true,
        ]);

        $attivitaOrdinaria = [
            [
                'titolo' => 'Gestione Presenze e Timbrature',
                'descrizione' => 'Controllo quotidiano presenze cantiere',
                'is_critica' => false,
                'ordine' => 1,
                'passi' => [
                    'Verifica timbrature ingresso/uscita',
                    'Controlla badge di riconoscimento',
                    'Registra presenze su registro di cantiere',
                    'Segnala anomalie o assenze non giustificate',
                    'Invia report presenze settimanale',
                ],
            ],
            [
                'titolo' => 'Controllo Documentazione Subappaltatori',
                'descrizione' => 'Verifica documenti imprese subappaltatrici',
                'is_critica' => true,
                'ordine' => 2,
                'passi' => [
                    'Richiedi DURC aggiornato del subappaltatore',
                    'Verifica POS specifico dell\'impresa',
                    'Controlla validità polizze assicurative',
                    'Verifica iscrizione CCIAA',
                    'Archivia tutta la documentazione',
                ],
            ],
            [
                'titolo' => 'Gestione Ordini e Bolle di Consegna',
                'descrizione' => 'Registrazione materiali in entrata',
                'is_critica' => false,
                'ordine' => 3,
                'passi' => [
                    'Controlla corrispondenza ordine/consegna',
                    'Verifica qualità e quantità materiali',
                    'Firma bolla di consegna',
                    'Archivia documentazione di trasporto',
                    'Aggiorna inventario cantiere',
                ],
            ],
            [
                'titolo' => 'Gestione Rifiuti e Formulari',
                'descrizione' => 'Smaltimento rifiuti di cantiere',
                'is_critica' => true,
                'ordine' => 4,
                'passi' => [
                    'Separa rifiuti per codice CER',
                    'Compila registro carico/scarico',
                    'Prenota ritiro con trasportatore autorizzato',
                    'Firma formulari di trasporto (FIR)',
                    'Conserva quarta copia entro 90 giorni',
                ],
            ],
            [
                'titolo' => 'Reportistica e SAL (Stato Avanzamento Lavori)',
                'descrizione' => 'Redazione periodica dello stato avanzamento',
                'is_critica' => false,
                'ordine' => 5,
                'passi' => [
                    'Fotografa avanzamento lavori',
                    'Compila report SAL con misurazioni',
                    'Verifica rispetto cronoprogramma',
                    'Segnala eventuali ritardi o criticità',
                    'Invia report a committente/DL',
                ],
            ],
        ];

        foreach ($attivitaOrdinaria as $attData) {
            $passi = $attData['passi'];
            unset($attData['passi']);
            $attData['fase_procedurale_id'] = $faseOrdinaria->id;

            $attivita = Attivita::create($attData);

            foreach ($passi as $index => $descrizione) {
                PassoAttivita::create([
                    'attivita_id' => $attivita->id,
                    'numero_passo' => $index + 1,
                    'descrizione' => $descrizione,
                ]);
            }
        }

        $this->command->info('✅ Fasi procedurali, attività e passi creati con successo!');
        $this->command->info('📊 Totale fasi: 4');
        $this->command->info('📋 Totale attività: ' . Attivita::count());
        $this->command->info('✓ Totale passi: ' . PassoAttivita::count());
    }
}

