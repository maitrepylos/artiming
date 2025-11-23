<div class="alert alert-success shadow-lg">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        <h3 class="font-bold">Participant mis à jour avec succès !</h3>
        <div class="text-sm">
            {{ $registration->full_name }}
            @if($registration->bib_number)
                - Dossard: {{ $registration->bib_number }}
            @endif
        </div>
    </div>
</div>
