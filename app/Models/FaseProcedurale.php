<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaseProcedurale extends Model
{
    use HasFactory;

    protected $table = 'fasi_procedurali';

    protected $fillable = [
        'nome',
        'descrizione',
        'icona',
        'tipologia',
        'ordine',
        'is_attiva',
    ];

    protected $casts = [
        'is_attiva' => 'boolean',
        'ordine' => 'integer',
    ];

    /**
     * Relazione con le attività
     */
    public function attivita()
    {
        return $this->hasMany(Attivita::class)->orderBy('ordine');
    }

    /**
     * Scope per fasi attive
     */
    public function scopeAttive($query)
    {
        return $query->where('is_attiva', true);
    }

    /**
     * Scope per ordinamento
     */
    public function scopeOrdinato($query)
    {
        return $query->orderBy('ordine');
    }
}

