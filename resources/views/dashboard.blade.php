@extends('layouts.app')

@section('title', 'Dashboard - Gestionale Cantieri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">Panoramica generale dei cantieri e delle attività</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Totale Cantieri -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Totale Cantieri</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['totale_cantieri'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Cantieri Attivi -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Cantieri Attivi</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['cantieri_attivi'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Attività in Corso -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Attività in Corso</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['attivita_in_corso'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-tasks text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Scadenze Imminenti -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Scadenze Imminenti</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['scadenze_in_arrivo'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cantieri Recenti -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                    Cantieri Recenti
                </h2>
            </div>
            <div class="p-6">
                @forelse($cantieriRecenti as $cantiere)
                    <a href="{{ route('cantieri.show', $cantiere) }}"
                       class="block p-4 hover:bg-gray-50 rounded-lg transition mb-3 border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $cantiere->nome }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $cantiere->committente }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Codice: {{ $cantiere->codice }}
                                </p>
                            </div>
                            <div class="ml-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($cantiere->stato === 'attivo') bg-green-100 text-green-800
                                    @elseif($cantiere->stato === 'apertura') bg-blue-100 text-blue-800
                                    @elseif($cantiere->stato === 'sospeso') bg-yellow-100 text-yellow-800
                                    @elseif($cantiere->stato === 'completato') bg-gray-100 text-gray-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($cantiere->stato) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500 text-center py-8">Nessun cantiere presente</p>
                @endforelse

                <div class="mt-4 text-center">
                    <a href="{{ route('cantieri.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Vedi tutti i cantieri <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Attività in Scadenza -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clock mr-2 text-orange-600"></i>
                    Attività in Scadenza
                </h2>
            </div>
            <div class="p-6">
                @forelse($attivitaInScadenza as $item)
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg mb-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $item->attivita->titolo }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    <i class="fas fa-building mr-1"></i>
                                    <a href="{{ route('cantieri.show', $item->cantiere) }}" class="hover:text-blue-600">
                                        {{ $item->cantiere->nome }}
                                    </a>
                                </p>
                                @if($item->data_scadenza)
                                    <p class="text-xs text-orange-700 mt-2">
                                        <i class="fas fa-calendar mr-1"></i>
                                        Scadenza: {{ $item->data_scadenza->format('d/m/Y') }}
                                        ({{ $item->data_scadenza->diffForHumans() }})
                                    </p>
                                @endif
                            </div>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($item->stato === 'da_fare') bg-red-100 text-red-800
                                @elseif($item->stato === 'in_corso') bg-yellow-100 text-yellow-800
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($item->stato)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">Nessuna attività in scadenza</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Grafico Cantieri per Stato -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-pie mr-2 text-purple-600"></i>
            Cantieri per Stato
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @foreach(['pianificazione', 'apertura', 'attivo', 'sospeso', 'completato', 'chiuso'] as $stato)
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-900">{{ $cantieriPerStato[$stato] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ ucfirst($stato) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

