<?php

namespace App\Http\Controllers;

use App\Models\Cantiere;
use App\Models\FaseProcedurale;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CantiereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cantieri = Cantiere::withCount('attivita')
            ->latest()
            ->paginate(15);

        return view('cantieri.index', compact('cantieri'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cantieri.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'indirizzo' => 'nullable|string',
            'committente' => 'required|string|max:255',
            'data_inizio' => 'nullable|date',
            'data_fine_prevista' => 'nullable|date|after_or_equal:data_inizio',
            'importo_lavori' => 'nullable|numeric|min:0',
            'stato' => 'required|in:pianificazione,apertura,attivo,sospeso,completato,chiuso',
            'note' => 'nullable|string',
        ]);

        // Genera codice cantiere automatico
        $anno = date('Y');
        $ultimoCantiere = Cantiere::whereYear('created_at', $anno)->count();
        $validated['codice'] = 'CANT-' . $anno . '-' . str_pad($ultimoCantiere + 1, 3, '0', STR_PAD_LEFT);

        $cantiere = Cantiere::create($validated);

        // Se il cantiere è in fase di apertura, assegna le attività della fase "Apertura"
        if ($validated['stato'] === 'apertura') {
            $faseApertura = FaseProcedurale::where('tipologia', 'apertura')->first();
            if ($faseApertura) {
                foreach ($faseApertura->attivita as $attivita) {
                    $cantiere->attivita()->attach($attivita->id, [
                        'stato' => 'da_fare',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('cantieri.show', $cantiere)
            ->with('success', 'Cantiere creato con successo!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cantiere $cantiere)
    {
        $cantiere->load([
            'attivita.faseProcedurale',
            'attivita.passi',
            'cantiereAttivita.attivita.faseProcedurale',
            'cantiereAttivita.completataDa',
            'documenti',
            'scadenze.attivita'
        ]);

        $fasi = FaseProcedurale::attive()
            ->ordinato()
            ->with('attivita')
            ->get();

        return view('cantieri.show', compact('cantiere', 'fasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cantiere $cantiere)
    {
        return view('cantieri.edit', compact('cantiere'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cantiere $cantiere)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'indirizzo' => 'nullable|string',
            'committente' => 'required|string|max:255',
            'data_inizio' => 'nullable|date',
            'data_fine_prevista' => 'nullable|date|after_or_equal:data_inizio',
            'importo_lavori' => 'nullable|numeric|min:0',
            'stato' => 'required|in:pianificazione,apertura,attivo,sospeso,completato,chiuso',
            'note' => 'nullable|string',
        ]);

        $cantiere->update($validated);

        return redirect()->route('cantieri.show', $cantiere)
            ->with('success', 'Cantiere aggiornato con successo!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cantiere $cantiere)
    {
        $cantiere->delete();

        return redirect()->route('cantieri.index')
            ->with('success', 'Cantiere eliminato con successo!');
    }
}
