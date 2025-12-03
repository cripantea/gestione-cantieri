<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantiereAttivita extends Model
{
    use HasFactory;

    protected $table = 'cantiere_attivita';

    protected $fillable = [
        'cantiere_id',
        'attivita_id',
        'stato',
        'data_scadenza',
        'data_completamento',
        'completata_da_user_id',
        'note',
    ];

    protected $casts = [
        'data_scadenza' => 'date',
        'data_completamento' => 'date',
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
     * Relazione con l'utente che ha completato
     */
    public function completataDa()
    {
        return $this->belongsTo(User::class, 'completata_da_user_id');
    }

    /**
     * Relazione con i passi
     */
    public function passi()
    {
        return $this->hasMany(CantierePasso::class);
    }

    /**
     * Scope per attività da fare
     */
    public function scopeDaFare($query)
    {
        return $query->where('stato', 'da_fare');
    }

    /**
     * Scope per attività in corso
     */
    public function scopeInCorso($query)
    {
        return $query->where('stato', 'in_corso');
    }

    /**
     * Scope per attività completate
     */
    public function scopeCompletate($query)
    {
        return $query->where('stato', 'completata');
    }

    /**
     * Scope per attività in scadenza
     */
    public function scopeInScadenza($query, $giorni = 7)
    {
        return $query->whereNotNull('data_scadenza')
            ->where('data_scadenza', '<=', now()->addDays($giorni))
            ->whereIn('stato', ['da_fare', 'in_corso']);
    }
}

