<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassoAttivita extends Model
{
    use HasFactory;

    protected $table = 'passi_attivita';

    protected $fillable = [
        'attivita_id',
        'numero_passo',
        'descrizione',
    ];

    protected $casts = [
        'numero_passo' => 'integer',
    ];

    /**
     * Relazione con l'attività
     */
    public function attivita()
    {
        return $this->belongsTo(Attivita::class);
    }

    /**
     * Relazione con i passi del cantiere
     */
    public function cantierePassi()
    {
        return $this->hasMany(CantierePasso::class);
    }
}

