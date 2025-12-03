<div x-data="{
    expanded: false,
    stato: '{{ $cantiereAttivita->stato }}'
}" class="border rounded-lg overflow-hidden
    @if($cantiereAttivita->stato === 'completata') bg-green-50 border-green-300
    @elseif($cantiereAttivita->stato === 'in_corso') bg-yellow-50 border-yellow-300
    @elseif($cantiereAttivita->stato === 'non_applicabile') bg-gray-50 border-gray-300
    @else bg-white border-gray-300
    @endif">

    <!-- Header Attività -->
    <div class="p-4">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $cantiereAttivita->attivita->titolo }}
                    </h4>
                    @if($cantiereAttivita->attivita->is_critica)
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
                            <i class="fas fa-exclamation-circle"></i> Critica
                        </span>
                    @endif
                    @if($cantiereAttivita->attivita->url_portale)
                        <a href="{{ $cantiereAttivita->attivita->url_portale }}"
                           target="_blank"
                           class="text-blue-600 hover:text-blue-700"
                           title="Vai al portale">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    @endif
                </div>

                @if($cantiereAttivita->attivita->descrizione)
                    <p class="text-sm text-gray-600 mt-1">{{ $cantiereAttivita->attivita->descrizione }}</p>
                @endif

                @if($cantiereAttivita->attivita->credenziali_note)
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-key"></i> {{ $cantiereAttivita->attivita->credenziali_note }}
                    </p>
                @endif

                <div class="flex items-center space-x-4 mt-3 text-sm">
                    @if($cantiereAttivita->data_scadenza)
                        <span class="text-gray-600">
                            <i class="fas fa-calendar"></i>
                            Scadenza: {{ $cantiereAttivita->data_scadenza->format('d/m/Y') }}
                            @if($cantiereAttivita->data_scadenza->isPast() && $cantiereAttivita->stato !== 'completata')
                                <span class="text-red-600 font-medium">(Scaduta)</span>
                            @endif
                        </span>
                    @endif

                    @if($cantiereAttivita->data_completamento)
                        <span class="text-green-600">
                            <i class="fas fa-check-circle"></i>
                            Completata: {{ $cantiereAttivita->data_completamento->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Stato Badge -->
            <div class="ml-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($cantiereAttivita->stato === 'completata') bg-green-100 text-green-800
                    @elseif($cantiereAttivita->stato === 'in_corso') bg-yellow-100 text-yellow-800
                    @elseif($cantiereAttivita->stato === 'non_applicabile') bg-gray-100 text-gray-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ str_replace('_', ' ', ucfirst($cantiereAttivita->stato)) }}
                </span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-center space-x-2 mt-4">
            <button @click="expanded = !expanded"
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                <i :class="expanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="mr-1"></i>
                <span x-text="expanded ? 'Nascondi passi' : 'Mostra passi (' + {{ $cantiereAttivita->attivita->passi->count() }} + ')'"></span>
            </button>

            <!-- Cambia Stato -->
            <div class="flex-1 flex justify-end space-x-2">
                @if($cantiereAttivita->stato === 'da_fare')
                    <form action="{{ route('attivita.updateStato', [$cantiere, $cantiereAttivita->attivita]) }}"
                          method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="stato" value="in_corso">
                        <button type="submit"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-medium transition">
                            <i class="fas fa-play mr-1"></i> Inizia
                        </button>
                    </form>
                @endif

                @if($cantiereAttivita->stato === 'in_corso')
                    <form action="{{ route('attivita.updateStato', [$cantiere, $cantiereAttivita->attivita]) }}"
                          method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="stato" value="completata">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition">
                            <i class="fas fa-check mr-1"></i> Completa
                        </button>
                    </form>
                @endif

                @if($cantiereAttivita->stato !== 'non_applicabile')
                    <form action="{{ route('attivita.updateStato', [$cantiere, $cantiereAttivita->attivita]) }}"
                          method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="stato" value="non_applicabile">
                        <button type="submit"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium transition"
                                onclick="return confirm('Segnare questa attività come non applicabile?')">
                            <i class="fas fa-ban mr-1"></i> Non Applicabile
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Passi dell'Attività (Espandibile) -->
    <div x-show="expanded"
         x-transition
         class="border-t bg-gray-50 p-4">

        @if($cantiereAttivita->attivita->passi->count() > 0)
            <h5 class="font-semibold text-gray-900 mb-3">
                <i class="fas fa-list-ol mr-2"></i>
                Passi da Completare
            </h5>

            <div class="space-y-2">
                @foreach($cantiereAttivita->attivita->passi as $passo)
                    @php
                        $cantierePasso = $cantiereAttivita->passi->where('passo_attivita_id', $passo->id)->first();
                        $isCompletato = $cantierePasso && $cantierePasso->completato;
                    @endphp

                    <div class="flex items-start space-x-3 p-3 bg-white rounded border {{ $isCompletato ? 'border-green-300' : 'border-gray-200' }}">
                        <div class="flex-shrink-0 mt-1">
                            <form action="{{ route('attivita.completaPasso', [$cantiereAttivita, $passo->id]) }}"
                                  method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="completato" value="{{ $isCompletato ? '0' : '1' }}">
                                <button type="submit"
                                        class="w-6 h-6 rounded border-2 flex items-center justify-center transition
                                            {{ $isCompletato ? 'bg-green-500 border-green-500' : 'border-gray-300 hover:border-blue-500' }}">
                                    @if($isCompletato)
                                        <i class="fas fa-check text-white text-xs"></i>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium {{ $isCompletato ? 'text-green-700 line-through' : 'text-gray-900' }}">
                                    {{ $passo->numero_passo }}. {{ $passo->descrizione }}
                                </span>
                                @if($isCompletato && $cantierePasso->completato_at)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $cantierePasso->completato_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>
                            @if($cantierePasso && $cantierePasso->note)
                                <p class="text-xs text-gray-600 mt-1">{{ $cantierePasso->note }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Progress Bar -->
            @php
                $totalPassi = $cantiereAttivita->attivita->passi->count();
                $passiCompletati = $cantiereAttivita->passi->where('completato', true)->count();
                $percentuale = $totalPassi > 0 ? round(($passiCompletati / $totalPassi) * 100) : 0;
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progresso</span>
                    <span>{{ $passiCompletati }}/{{ $totalPassi }} passi completati ({{ $percentuale }}%)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: {{ $percentuale }}%"></div>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 italic">Nessun passo definito per questa attività</p>
        @endif

        <!-- Note Attività -->
        @if($cantiereAttivita->note)
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-sticky-note mr-1"></i> Note:
                </p>
                <p class="text-sm text-gray-600">{{ $cantiereAttivita->note }}</p>
            </div>
        @endif
    </div>
</div>

