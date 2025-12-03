<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attivita extends Model
{
    use HasFactory;

    protected $table = 'attivita';

    protected $fillable = [
        'fase_procedurale_id',
        'titolo',
        'descrizione',
        'is_critica',
        'url_portale',
        'credenziali_note',
        'ordine',
    ];

    protected $casts = [
        'is_critica' => 'boolean',
        'ordine' => 'integer',
    ];

    /**
     * Relazione con la fase procedurale
     */
    public function faseProcedurale()
    {
        return $this->belongsTo(FaseProcedurale::class);
    }

    /**
     * Relazione con i passi dell'attività
     */
    public function passi()
    {
        return $this->hasMany(PassoAttivita::class)->orderBy('numero_passo');
    }

    /**
     * Relazione con i cantieri (pivot)
     */
    public function cantieri()
    {
        return $this->belongsToMany(Cantiere::class, 'cantiere_attivita')
            ->withPivot('stato', 'data_scadenza', 'data_completamento', 'completata_da_user_id', 'note')
            ->withTimestamps();
    }

    /**
     * Relazione con le scadenze ricorrenti
     */
    public function scadenzeRicorrenti()
    {
        return $this->hasMany(ScadenzaRicorrente::class);
    }

    /**
     * Relazione con i documenti
     */
    public function documenti()
    {
        return $this->hasMany(DocumentoCantiere::class);
    }

    /**
     * Scope per attività critiche
     */
    public function scopeCritiche($query)
    {
        return $query->where('is_critica', true);
    }
}

