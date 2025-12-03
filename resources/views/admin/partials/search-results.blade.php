@if($participants->count() > 0)
    <div class="bg-base-200 rounded-lg shadow-lg max-h-64 overflow-y-auto">
        @foreach($participants as $participant)
            <div class="participant-item p-3 hover:bg-primary hover:text-primary-content cursor-pointer border-b border-base-300 last:border-b-0 transition-colors"
                 onclick="selectParticipant(this)"
                 data-id="{{ $participant->id }}"
                 data-nom="{{ $participant->nom }}"
                 data-prenom="{{ $participant->prenom }}"
                 data-sexe="{{ $participant->sexe }}"
                 data-date_naissance="{{ $participant->date_naissance->format('Y-m-d') }}"
                 data-category_id="{{ $participant->category_id }}"
                 data-category_name="{{ $participant->category->name }}"
                 data-club="{{ $participant->club ?? '' }}"
                 data-nationalite="{{ $participant->nationalite ?? 'BEL' }}"
                 data-code_uci="{{ $participant->code_uci ?? '' }}"
                 data-bib_number="{{ $participant->bib_number ?? '' }}"
                 data-is_paid="{{ $participant->is_paid ? '1' : '0' }}">
                <div class="font-semibold">{{ $participant->full_name }}</div>
                <div class="text-sm opacity-70">
                    {{ $participant->category->name }}
                    @if($participant->bib_number)
                        - Dossard: <span class="font-mono">{{ $participant->bib_number }}</span>
                    @endif
                    @if($participant->is_paid)
                        <span class="badge badge-success badge-sm ml-2">Payé</span>
                    @else
                        <span class="badge badge-error badge-sm ml-2">Non payé</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <span>Aucun participant trouvé</span>
    </div>
@endif
