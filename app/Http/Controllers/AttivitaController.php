<?php

namespace App\Http\Controllers;

use App\Models\Cantiere;
use App\Models\Attivita;
use App\Models\CantiereAttivita;
use App\Models\CantierePasso;
use Illuminate\Http\Request;

class AttivitaController extends Controller
{
    /**
     * Assegna un'attività a un cantiere
     */
    public function assegna(Request $request, Cantiere $cantiere)
    {
        $validated = $request->validate([
            'attivita_id' => 'required|exists:attivita,id',
            'data_scadenza' => 'nullable|date',
        ]);

        // Verifica se l'attività è già assegnata
        if ($cantiere->attivita()->where('attivita_id', $validated['attivita_id'])->exists()) {
            return back()->with('error', 'Attività già assegnata a questo cantiere');
        }

        $cantiere->attivita()->attach($validated['attivita_id'], [
            'stato' => 'da_fare',
            'data_scadenza' => $validated['data_scadenza'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Attività assegnata con successo');
    }

    /**
     * Aggiorna lo stato di un'attività
     */
    public function updateStato(Request $request, Cantiere $cantiere, Attivita $attivita)
    {
        $validated = $request->validate([
            'stato' => 'required|in:da_fare,in_corso,completata,non_applicabile',
            'note' => 'nullable|string',
        ]);

        $cantiereAttivita = CantiereAttivita::where('cantiere_id', $cantiere->id)
            ->where('attivita_id', $attivita->id)
            ->firstOrFail();

        $data = [
            'stato' => $validated['stato'],
            'note' => $validated['note'] ?? null,
        ];

        // Se completata, registra data e utente
        if ($validated['stato'] === 'completata') {
            $data['data_completamento'] = now();
            $data['completata_da_user_id'] = auth()->id();
        } else {
            $data['data_completamento'] = null;
            $data['completata_da_user_id'] = null;
        }

        $cantiereAttivita->update($data);

        return back()->with('success', 'Stato attività aggiornato con successo');
    }

    /**
     * Completa un passo dell'attività
     */
    public function completaPasso(Request $request, CantiereAttivita $cantiereAttivita, $passoId)
    {
        $validated = $request->validate([
            'completato' => 'required|boolean',
            'note' => 'nullable|string',
        ]);

        $cantierePasso = CantierePasso::firstOrCreate(
            [
                'cantiere_attivita_id' => $cantiereAttivita->id,
                'passo_attivita_id' => $passoId,
            ],
            [
                'completato' => false,
            ]
        );

        $data = [
            'completato' => $validated['completato'],
            'note' => $validated['note'] ?? null,
        ];

        if ($validated['completato']) {
            $data['completato_at'] = now();
            $data['completato_da_user_id'] = auth()->id();
        } else {
            $data['completato_at'] = null;
            $data['completato_da_user_id'] = null;
        }

        $cantierePasso->update($data);

        // Se tutti i passi sono completati, suggerisci di completare l'attività
        $totalPassi = $cantiereAttivita->attivita->passi->count();
        $passiCompletati = $cantiereAttivita->passi()->completati()->count();

        $message = 'Passo aggiornato con successo';
        if ($totalPassi > 0 && $passiCompletati === $totalPassi) {
            $message .= '. Tutti i passi sono completati! Puoi segnare l\'attività come completata.';
        }

        return back()->with('success', $message);
    }

    /**
     * Rimuovi un'attività da un cantiere
     */
    public function rimuovi(Cantiere $cantiere, Attivita $attivita)
    {
        $cantiere->attivita()->detach($attivita->id);

        return back()->with('success', 'Attività rimossa dal cantiere');
    }
}
