<?php

namespace App\Http\Controllers;

use App\Models\Cantiere;
use App\Models\CantiereAttivita;
use App\Models\CantiereScadenza;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiche generali
        $stats = [
            'totale_cantieri' => Cantiere::count(),
            'cantieri_attivi' => Cantiere::where('stato', 'attivo')->count(),
            'cantieri_apertura' => Cantiere::where('stato', 'apertura')->count(),
            'attivita_da_fare' => CantiereAttivita::daFare()->count(),
            'attivita_in_corso' => CantiereAttivita::inCorso()->count(),
            'scadenze_in_arrivo' => CantiereScadenza::inArrivo(30)->count(),
            'scadenze_scadute' => CantiereScadenza::scadute()->count(),
        ];

        // Cantieri recenti
        $cantieriRecenti = Cantiere::latest()
            ->take(5)
            ->get();

        // Attività in scadenza
        $attivitaInScadenza = CantiereAttivita::with(['cantiere', 'attivita'])
            ->inScadenza(7)
            ->orderBy('data_scadenza')
            ->take(10)
            ->get();

        // Scadenze imminenti
        $scadenzeImminenti = CantiereScadenza::with(['cantiere', 'attivita'])
            ->inArrivo(15)
            ->orderBy('data_scadenza')
            ->take(10)
            ->get();

        // Cantieri per stato
        $cantieriPerStato = Cantiere::selectRaw('stato, count(*) as count')
            ->groupBy('stato')
            ->get()
            ->pluck('count', 'stato');

        return view('dashboard', compact(
            'stats',
            'cantieriRecenti',
            'attivitaInScadenza',
            'scadenzeImminenti',
            'cantieriPerStato'
        ));
    }
}
