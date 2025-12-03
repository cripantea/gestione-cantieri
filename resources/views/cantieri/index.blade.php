@extends('layouts.app')

@section('title', 'Cantieri - Gestionale Cantieri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Cantieri</h1>
            <p class="mt-2 text-sm text-gray-600">Gestisci tutti i cantieri aziendali</p>
        </div>
        <a href="{{ route('cantieri.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition shadow-lg">
            <i class="fas fa-plus mr-2"></i> Nuovo Cantiere
        </a>
    </div>

    <!-- Filtri -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Cerca cantiere..."
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="min-w-[150px]">
                <select name="stato" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tutti gli stati</option>
                    <option value="pianificazione" {{ request('stato') === 'pianificazione' ? 'selected' : '' }}>Pianificazione</option>
                    <option value="apertura" {{ request('stato') === 'apertura' ? 'selected' : '' }}>Apertura</option>
                    <option value="attivo" {{ request('stato') === 'attivo' ? 'selected' : '' }}>Attivo</option>
                    <option value="sospeso" {{ request('stato') === 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                    <option value="completato" {{ request('stato') === 'completato' ? 'selected' : '' }}>Completato</option>
                    <option value="chiuso" {{ request('stato') === 'chiuso' ? 'selected' : '' }}>Chiuso</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition">
                <i class="fas fa-search mr-2"></i> Cerca
            </button>
        </form>
    </div>

    <!-- Lista Cantieri -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cantiere
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Committente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stato
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Attività
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Azioni
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cantieri as $cantiere)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('cantieri.show', $cantiere) }}"
                                       class="text-lg font-semibold text-blue-600 hover:text-blue-700">
                                        {{ $cantiere->nome }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ $cantiere->codice }}</p>
                                    @if($cantiere->indirizzo)
                                        <p class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-map-marker-alt"></i> {{ Str::limit($cantiere->indirizzo, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $cantiere->committente }}</div>
                                @if($cantiere->importo_lavori)
                                    <div class="text-xs text-gray-500 mt-1">
                                        € {{ number_format($cantiere->importo_lavori, 2, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($cantiere->stato === 'attivo') bg-green-100 text-green-800
                                    @elseif($cantiere->stato === 'apertura') bg-blue-100 text-blue-800
                                    @elseif($cantiere->stato === 'sospeso') bg-yellow-100 text-yellow-800
                                    @elseif($cantiere->stato === 'completato') bg-purple-100 text-purple-800
                                    @elseif($cantiere->stato === 'chiuso') bg-gray-100 text-gray-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($cantiere->stato) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($cantiere->data_inizio)
                                    <div>
                                        <i class="fas fa-play-circle text-green-500"></i>
                                        {{ $cantiere->data_inizio->format('d/m/Y') }}
                                    </div>
                                @endif
                                @if($cantiere->data_fine_prevista)
                                    <div class="mt-1">
                                        <i class="fas fa-flag-checkered text-red-500"></i>
                                        {{ $cantiere->data_fine_prevista->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $cantiere->attivita_count }}</span>
                                    <span class="ml-1 text-xs text-gray-500">attività</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                <a href="{{ route('cantieri.show', $cantiere) }}"
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cantieri.edit', $cantiere) }}"
                                   class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cantieri.destroy', $cantiere) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Sei sicuro di voler eliminare questo cantiere?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-folder-open text-6xl mb-4"></i>
                                    <p class="text-lg">Nessun cantiere trovato</p>
                                    <a href="{{ route('cantieri.create') }}"
                                       class="text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                        Crea il tuo primo cantiere
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($cantieri->hasPages())
        <div class="bg-white rounded-lg shadow px-6 py-4">
            {{ $cantieri->links() }}
        </div>
    @endif
</div>
@endsection

