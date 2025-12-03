<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentoCantiere extends Model
{
    use HasFactory;

    protected $table = 'documenti_cantiere';

    protected $fillable = [
        'cantiere_id',
        'attivita_id',
        'nome_file',
        'path',
        'tipo',
        'data_scadenza',
    ];

    protected $casts = [
        'data_scadenza' => 'date',
    ];

    /**
     * Relazione con il cantiere
     */
    public function cantiere()
    {
        return $this->belongsTo(Cantiere::class);
    }

    /**
     * Relazione con l'attività (opzionale)
     */
    public function attivita()
    {
        return $this->belongsTo(Attivita::class);
    }

    /**
     * Ottieni l'URL completo del documento
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Scope per documenti in scadenza
     */
    public function scopeInScadenza($query, $giorni = 30)
    {
        return $query->whereNotNull('data_scadenza')
            ->where('data_scadenza', '<=', now()->addDays($giorni))
            ->where('data_scadenza', '>=', now());
    }

    /**
     * Scope per documenti scaduti
     */
    public function scopeScaduti($query)
    {
        return $query->whereNotNull('data_scadenza')
            ->where('data_scadenza', '<', now());
    }

    /**
     * Verifica se il documento è scaduto
     */
    public function isScaduto()
    {
        return $this->data_scadenza && $this->data_scadenza < now();
    }

    /**
     * Elimina il file fisico quando viene eliminato il record
     */
    protected static function booted()
    {
        static::deleting(function ($documento) {
            if (Storage::exists($documento->path)) {
                Storage::delete($documento->path);
            }
        });
    }
}

