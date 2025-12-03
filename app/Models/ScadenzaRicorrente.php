<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScadenzaRicorrente extends Model
{
    use HasFactory;

    protected $table = 'scadenze_ricorrenti';

    protected $fillable = [
        'attivita_id',
        'frequenza',
        'intervallo_giorni',
        'giorni_preavviso',
    ];

    protected $casts = [
        'intervallo_giorni' => 'integer',
        'giorni_preavviso' => 'integer',
    ];

    /**
     * Relazione con l'attività
     */
    public function attivita()
    {
        return $this->belongsTo(Attivita::class);
    }

    /**
     * Calcola la prossima data di scadenza
     */
    public function calcolaProssimaScadenza($dataInizio)
    {
        $giorni = match($this->frequenza) {
            'annuale' => 365,
            'biennale' => 730,
            'mensile' => 30,
            'trimestrale' => 90,
            'custom' => $this->intervallo_giorni,
            default => 365,
        };

        return $dataInizio->addDays($giorni);
    }
}

