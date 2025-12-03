@extends('layouts.app')

@section('title', $cantiere->nome . ' - Gestionale Cantieri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <a href="{{ route('cantieri.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Torna ai cantieri
        </a>
        <div class="mt-4 flex justify-between items-start">
            <div class="flex-1">
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $cantiere->nome }}</h1>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($cantiere->stato === 'attivo') bg-green-100 text-green-800
                        @elseif($cantiere->stato === 'apertura') bg-blue-100 text-blue-800
                        @elseif($cantiere->stato === 'sospeso') bg-yellow-100 text-yellow-800
                        @elseif($cantiere->stato === 'completato') bg-purple-100 text-purple-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($cantiere->stato) }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-600">Codice: {{ $cantiere->codice }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('cantieri.edit', $cantiere) }}"
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-edit mr-2"></i> Modifica
                </a>
            </div>
        </div>
    </div>

    <!-- Info Cantiere -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Committente</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cantiere->committente }}</p>
            </div>
            @if($cantiere->indirizzo)
                <div>
                    <p class="text-sm font-medium text-gray-500">Indirizzo</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $cantiere->indirizzo }}</p>
                </div>
            @endif
            @if($cantiere->data_inizio)
                <div>
                    <p class="text-sm font-medium text-gray-500">Data Inizio</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $cantiere->data_inizio->format('d/m/Y') }}</p>
                </div>
            @endif
            @if($cantiere->importo_lavori)
                <div>
                    <p class="text-sm font-medium text-gray-500">Importo Lavori</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">€ {{ number_format($cantiere->importo_lavori, 2, ',', '.') }}</p>
                </div>
            @endif
        </div>
        @if($cantiere->note)
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm font-medium text-gray-500 mb-2">Note</p>
                <p class="text-sm text-gray-700">{{ $cantiere->note }}</p>
            </div>
        @endif
    </div>

    <!-- Tabs per Fasi -->
    <div x-data="{ activeTab: 'tutte' }" class="bg-white rounded-lg shadow-lg">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'tutte'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'tutte', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tutte' }"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-list mr-2"></i> Tutte le Attività
                </button>
                @foreach($fasi as $fase)
                    <button @click="activeTab = 'fase-{{ $fase->id }}'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'fase-{{ $fase->id }}', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'fase-{{ $fase->id }}' }"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition">
                        @if($fase->icona)
                            <i class="fas fa-{{ $fase->icona }} mr-2"></i>
                        @endif
                        {{ $fase->nome }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Tutte le Attività -->
            <div x-show="activeTab === 'tutte'" class="space-y-6">
                @forelse($cantiere->cantiereAttivita as $cantiereAttivita)
                    @include('cantieri.partials.attivita-card', ['cantiereAttivita' => $cantiereAttivita])
                @empty
                    <div class="text-center py-12">
                        <i class="fas fa-tasks text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Nessuna attività assegnata</p>
                        <p class="text-gray-400 text-sm mt-2">Seleziona una fase per aggiungere attività al cantiere</p>
                    </div>
                @endforelse
            </div>

            <!-- Attività per Fase -->
            @foreach($fasi as $fase)
                <div x-show="activeTab === 'fase-{{ $fase->id }}'" class="space-y-4">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $fase->nome }}</h3>
                            @if($fase->descrizione)
                                <p class="text-sm text-gray-600 mt-1">{{ $fase->descrizione }}</p>
                            @endif
                        </div>
                    </div>

                    @foreach($fase->attivita as $attivita)
                        @php
                            $assigned = $cantiere->cantiereAttivita->where('attivita_id', $attivita->id)->first();
                        @endphp

                        @if($assigned)
                            @include('cantieri.partials.attivita-card', ['cantiereAttivita' => $assigned])
                        @else
                            <!-- Attività Non Assegnata -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-700">{{ $attivita->titolo }}</h4>
                                        @if($attivita->descrizione)
                                            <p class="text-sm text-gray-600 mt-1">{{ $attivita->descrizione }}</p>
                                        @endif
                                        @if($attivita->is_critica)
                                            <span class="inline-block mt-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                                                <i class="fas fa-exclamation-circle"></i> Critica
                                            </span>
                                        @endif
                                    </div>
                                    <form action="{{ route('attivita.assegna', $cantiere) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="attivita_id" value="{{ $attivita->id }}">
                                        <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                            <i class="fas fa-plus mr-1"></i> Assegna
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

