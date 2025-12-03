<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantierePasso extends Model
{
    use HasFactory;

    protected $table = 'cantiere_passo';

    protected $fillable = [
        'cantiere_attivita_id',
        'passo_attivita_id',
        'completato',
        'completato_at',
        'completato_da_user_id',
        'note',
    ];

    protected $casts = [
        'completato' => 'boolean',
        'completato_at' => 'datetime',
    ];

    /**
     * Relazione con cantiere_attivita
     */
    public function cantiereAttivita()
    {
        return $this->belongsTo(CantiereAttivita::class);
    }

    /**
     * Relazione con il passo attività
     */
    public function passoAttivita()
    {
        return $this->belongsTo(PassoAttivita::class);
    }

    /**
     * Relazione con l'utente che ha completato
     */
    public function completatoDa()
    {
        return $this->belongsTo(User::class, 'completato_da_user_id');
    }

    /**
     * Scope per passi completati
     */
    public function scopeCompletati($query)
    {
        return $query->where('completato', true);
    }

    /**
     * Scope per passi da completare
     */
    public function scopeDaCompletare($query)
    {
        return $query->where('completato', false);
    }
}

