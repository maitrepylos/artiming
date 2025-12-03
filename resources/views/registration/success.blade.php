<div class="alert alert-success shadow-lg" role="alert">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        <h3 class="font-bold">{{ __('registration.success.title') }}</h3>
        <div class="text-sm mt-2">
            <strong>{{ $registration->full_name }}</strong><br>
            {{ __('registration.success.category') }} : {{ $registration->category->name }}<br>
            {{ __('registration.fields.birth_date') }} : {{ $registration->date_naissance->format('d/m/Y') }}<br>
            {{ __('registration.fields.nationality') }} : {{ $registration->nationalite }}
            @if($registration->club)
                <br>{{ __('registration.fields.club') }} : {{ $registration->club }}
            @endif
        </div>
        <p class="text-sm mt-2 opacity-80">{{ __('registration.success.message') }}</p>
    </div>
</div>
