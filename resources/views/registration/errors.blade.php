{{-- resources/views/registration/errors.blade.php --}}
<div class="alert alert-error shadow-lg" role="alert">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <div>
        <h3 class="font-bold">Erreurs de validation</h3>
        <ul class="text-sm mt-2 list-disc list-inside">
            @foreach($errors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
