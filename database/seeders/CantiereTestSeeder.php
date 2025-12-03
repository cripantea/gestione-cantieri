<?php

namespace Database\Seeders;

use App\Models\Cantiere;
use App\Models\FaseProcedurale;
use Illuminate\Database\Seeder;

class CantiereTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea un cantiere di test
        $cantiere = Cantiere::create([
            'codice' => 'CANT-2025-001',
            'nome' => 'Costruzione Edificio Residenziale Via Roma',
            'indirizzo' => 'Via Roma 123, Milano (MI)',
            'committente' => 'Immobiliare Milano S.p.A.',
            'data_inizio' => now()->subDays(10),
            'data_fine_prevista' => now()->addMonths(12),
            'importo_lavori' => 850000.00,
            'stato' => 'apertura',
            'note' => 'Cantiere per costruzione nuovo edificio residenziale di 4 piani con 12 appartamenti.',
        ]);

        // Assegna le attività della fase di apertura
        $faseApertura = FaseProcedurale::where('tipologia', 'apertura')->first();
        if ($faseApertura) {
            foreach ($faseApertura->attivita as $index => $attivita) {
                // Alcune attività sono già completate, altre in corso
                $stato = match($index) {
                    0, 1 => 'completata',
                    2, 3 => 'in_corso',
                    default => 'da_fare',
                };

                $dataScadenza = now()->addDays(7 + ($index * 3));
                $dataCompletamento = in_array($index, [0, 1]) ? now()->subDays(rand(1, 5)) : null;

                $cantiere->attivita()->attach($attivita->id, [
                    'stato' => $stato,
                    'data_scadenza' => $dataScadenza,
                    'data_completamento' => $dataCompletamento,
                    'completata_da_user_id' => $dataCompletamento ? 1 : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Cantiere di test creato con successo!');
        $this->command->info('📍 Cantiere: ' . $cantiere->nome);
        $this->command->info('🔢 Codice: ' . $cantiere->codice);
    }
}

