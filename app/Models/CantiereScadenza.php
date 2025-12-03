<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantiereScadenza extends Model
{
    use HasFactory;

    protected $table = 'cantiere_scadenze';

    protected $fillable = [
        'cantiere_id',
        'attivita_id',
        'data_scadenza',
        'data_completamento',
        'inviato_alert',
        'note',
    ];

    protected $casts = [
        'data_scadenza' => 'date',
        'data_completamento' => 'date',
        'inviato_alert' => 'boolean',
    ];

    /**
     * Relazione con il cantiere
     */
    public function cantiere()
    {
        return $this->belongsTo(Cantiere::class);
    }

    /**
     * Relazione con l'attività
     */
    public function attivita()
    {
        return $this->belongsTo(Attivita::class);
    }

    /**
     * Scope per scadenze in arrivo
     */
    public function scopeInArrivo($query, $giorni = 30)
    {
        return $query->whereNull('data_completamento')
            ->where('data_scadenza', '<=', now()->addDays($giorni))
            ->where('data_scadenza', '>=', now());
    }

    /**
     * Scope per scadenze scadute
     */
    public function scopeScadute($query)
    {
        return $query->whereNull('data_completamento')
            ->where('data_scadenza', '<', now());
    }

    /**
     * Scope per scadenze senza alert
     */
    public function scopeSenzaAlert($query)
    {
        return $query->where('inviato_alert', false);
    }

    /**
     * Verifica se la scadenza è scaduta
     */
    public function isScaduta()
    {
        return $this->data_scadenza < now() && is_null($this->data_completamento);
    }

    /**
     * Verifica se la scadenza è in arrivo
     */
    public function isInArrivo($giorni = 30)
    {
        return $this->data_scadenza <= now()->addDays($giorni)
            && $this->data_scadenza >= now()
            && is_null($this->data_completamento);
    }
}

