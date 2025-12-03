@extends('layouts.app')

@section('title', 'Nuovo Cantiere - Gestionale Cantieri')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('cantieri.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Torna ai cantieri
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Nuovo Cantiere</h1>
        <p class="mt-2 text-sm text-gray-600">Compila i dati per creare un nuovo cantiere</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('cantieri.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nome Cantiere -->
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome Cantiere <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nome"
                       id="nome"
                       value="{{ old('nome') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Committente -->
            <div>
                <label for="committente" class="block text-sm font-medium text-gray-700 mb-2">
                    Committente <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="committente"
                       id="committente"
                       value="{{ old('committente') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('committente') border-red-500 @enderror">
                @error('committente')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Indirizzo -->
            <div>
                <label for="indirizzo" class="block text-sm font-medium text-gray-700 mb-2">
                    Indirizzo
                </label>
                <textarea name="indirizzo"
                          id="indirizzo"
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('indirizzo') border-red-500 @enderror">{{ old('indirizzo') }}</textarea>
                @error('indirizzo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="data_inizio" class="block text-sm font-medium text-gray-700 mb-2">
                        Data Inizio
                    </label>
                    <input type="date"
                           name="data_inizio"
                           id="data_inizio"
                           value="{{ old('data_inizio') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('data_inizio') border-red-500 @enderror">
                    @error('data_inizio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="data_fine_prevista" class="block text-sm font-medium text-gray-700 mb-2">
                        Data Fine Prevista
                    </label>
                    <input type="date"
                           name="data_fine_prevista"
                           id="data_fine_prevista"
                           value="{{ old('data_fine_prevista') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('data_fine_prevista') border-red-500 @enderror">
                    @error('data_fine_prevista')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Importo Lavori -->
            <div>
                <label for="importo_lavori" class="block text-sm font-medium text-gray-700 mb-2">
                    Importo Lavori (€)
                </label>
                <input type="number"
                       name="importo_lavori"
                       id="importo_lavori"
                       value="{{ old('importo_lavori') }}"
                       step="0.01"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('importo_lavori') border-red-500 @enderror">
                @error('importo_lavori')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stato -->
            <div>
                <label for="stato" class="block text-sm font-medium text-gray-700 mb-2">
                    Stato <span class="text-red-500">*</span>
                </label>
                <select name="stato"
                        id="stato"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('stato') border-red-500 @enderror">
                    <option value="pianificazione" {{ old('stato') === 'pianificazione' ? 'selected' : '' }}>Pianificazione</option>
                    <option value="apertura" {{ old('stato', 'apertura') === 'apertura' ? 'selected' : '' }}>Apertura</option>
                    <option value="attivo" {{ old('stato') === 'attivo' ? 'selected' : '' }}>Attivo</option>
                    <option value="sospeso" {{ old('stato') === 'sospeso' ? 'selected' : '' }}>Sospeso</option>
                    <option value="completato" {{ old('stato') === 'completato' ? 'selected' : '' }}>Completato</option>
                    <option value="chiuso" {{ old('stato') === 'chiuso' ? 'selected' : '' }}>Chiuso</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    Se selezioni "Apertura", verranno automaticamente assegnate le attività di apertura cantiere
                </p>
                @error('stato')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Note -->
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                    Note
                </label>
                <textarea name="note"
                          id="note"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('note') border-red-500 @enderror">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('cantieri.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Annulla
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition shadow-lg">
                    <i class="fas fa-save mr-2"></i> Crea Cantiere
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

