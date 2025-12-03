<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cantiere extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cantieri';
    protected $fillable = [
        'codice',
        'nome',
        'indirizzo',
        'committente',
        'data_inizio',
        'data_fine_prevista',
        'importo_lavori',
        'stato',
        'note',
    ];
    protected $casts = [
        'data_inizio' => 'date',
        'data_fine_prevista' => 'date',
        'importo_lavori' => 'decimal:2',
    ];
    /**
     * Relazione con le attività del cantiere (pivot)
     */
    public function attivita()
    {
        return $this->belongsToMany(Attivita::class, 'cantiere_attivita')
            ->withPivot('stato', 'data_scadenza', 'data_completamento', 'completata_da_user_id', 'note')
            ->withTimestamps();
    }
    /**
     * Relazione con i documenti del cantiere
     */
    public function documenti()
    {
        return $this->hasMany(DocumentoCantiere::class);
    }
    /**
     * Relazione con le scadenze del cantiere
     */
    public function scadenze()
    {
        return $this->hasMany(CantiereScadenza::class);
    }
    /**
     * Relazione con le attività pivot
     */
    public function cantiereAttivita()
    {
        return $this->hasMany(CantiereAttivita::class);
    }
}
