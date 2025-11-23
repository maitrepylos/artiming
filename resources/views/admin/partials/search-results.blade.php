@if($participants->count() > 0)
    <div class="menu bg-base-200 rounded-box shadow-lg">
        @foreach($participants as $participant)
            <li>
                <a onclick="selectParticipant({
                    id: {{ $participant->id }},
                    nom: '{{ $participant->nom }}',
                    prenom: '{{ $participant->prenom }}',
                    sexe: '{{ $participant->sexe }}',
                    date_naissance: '{{ $participant->date_naissance->format('Y-m-d') }}',
                    category_id: {{ $participant->category_id }},
                    category_name: '{{ $participant->category->name }}',
                    club: '{{ $participant->club }}',
                    nationalite: '{{ $participant->nationalite }}',
                    code_uci: '{{ $participant->code_uci }}',
                    bib_number: {{ $participant->bib_number ?? 'null' }},
                    is_paid: {{ $participant->is_paid ? 'true' : 'false' }}
                })">
                    <div class="flex justify-between items-center w-full">
                        <div>
                            <strong>{{ $participant->full_name }}</strong>
                            <br>
                            <span class="text-xs opacity-70">{{ $participant->category->name }}</span>
                        </div>
                        <div class="text-right text-xs">
                            @if($participant->bib_number)
                                <span class="badge badge-sm badge-primary">{{ $participant->bib_number }}</span>
                            @endif
                            @if($participant->is_paid)
                                <span class="badge badge-sm badge-success">✓</span>
                            @endif
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </div>
@else
    <div class="alert alert-warning shadow-lg">
        <span>Aucun participant trouvé</span>
    </div>
@endif
