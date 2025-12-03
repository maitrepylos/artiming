<!DOCTYPE html>
<html lang="{{ $event->locale }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('registration.registration_title') }} - {{ $event->name }}</title>

    {{-- Tailwind + DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- HTMX --}}
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
</head>
<body class="bg-base-200 min-h-screen">

<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- Header avec logo --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body text-center">
            @if($event->logo)
                <img src="{{ Storage::url($event->logo) }}" alt="{{ $event->name }}" class="h-24 mx-auto mb-4">
            @endif
            <h1 class="card-title text-3xl justify-center">{{ $event->name }}</h1>
            <p class="text-base-content/70">{{ $event->description }}</p>
            <div class="badge badge-primary">{{ $event->event_date->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Zone des messages (succès/erreurs) --}}
    <div id="messages" class="mb-6"></div>

    {{-- Formulaire d'inscription --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-2">{{ __('registration.registration_title') }}</h2>
            <p class="text-sm text-base-content/70 mb-4">{{ __('registration.info.required_fields') }}</p>

            <form
                hx-post="{{ route('event.register.store', $event->slug) }}"
                hx-target="#messages"
                hx-swap="innerHTML"
                hx-on::after-request="if(event.detail.successful && !event.detail.xhr.responseText.includes('alert-error')) this.reset();">

                @csrf

                {{-- Nom & Prénom --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('registration.fields.last_name') }} <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="nom"
                            placeholder="{{ __('registration.fields.last_name_placeholder') }}"
                            class="input input-bordered"
                            required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('registration.fields.first_name') }} <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="prenom"
                            placeholder="{{ __('registration.fields.first_name_placeholder') }}"
                            class="input input-bordered"
                            required>
                    </div>
                </div>

                {{-- Genre --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">{{ __('registration.fields.gender') }} <span class="text-error">*</span></span>
                    </label>
                    <div class="flex gap-4">
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="M" class="radio radio-primary" checked>
                            <span class="label-text">{{ __('registration.fields.gender_male') }}</span>
                        </label>
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="F" class="radio radio-primary">
                            <span class="label-text">{{ __('registration.fields.gender_female') }}</span>
                        </label>
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="X" class="radio radio-primary">
                            <span class="label-text">{{ __('registration.fields.gender_other') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Date de naissance & Nationalité --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('registration.fields.birth_date') }} <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="date"
                            name="date_naissance"
                            class="input input-bordered"
                            required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">{{ __('registration.fields.nationality') }} <span class="text-error">*</span></span>
                        </label>
                        <select name="nationalite" class="select select-bordered" required>
                            <option value="">{{ __('registration.fields.nationality_placeholder') }}</option>
                            <option value="BEL" selected>🇧🇪 {{ __('registration.nationalities.BEL') }}</option>
                            <option value="FRA">🇫🇷 {{ __('registration.nationalities.FRA') }}</option>
                            <option value="NLD">🇳🇱 {{ __('registration.nationalities.NLD') }}</option>
                            <option value="DEU">🇩🇪 {{ __('registration.nationalities.DEU') }}</option>
                            <option value="GBR">🇬🇧 {{ __('registration.nationalities.GBR') }}</option>
                            <option value="LUX">🇱🇺 {{ __('registration.nationalities.LUX') }}</option>
                            <option value="CHE">🇨🇭 {{ __('registration.nationalities.CHE') }}</option>
                            <option value="ESP">🇪🇸 {{ __('registration.nationalities.ESP') }}</option>
                            <option value="ITA">🇮🇹 {{ __('registration.nationalities.ITA') }}</option>
                            <option value="PRT">🇵🇹 {{ __('registration.nationalities.PRT') }}</option>
                            <option value="USA">🇺🇸 {{ __('registration.nationalities.USA') }}</option>
                            <option value="CAN">🇨🇦 {{ __('registration.nationalities.CAN') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Catégorie (Course) --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">{{ __('registration.fields.category') }} <span class="text-error">*</span></span>
                    </label>
                    <select name="category_id" class="select select-bordered" required>
                        <option value="">{{ __('registration.fields.category_placeholder') }}</option>
                        @foreach($event->categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Club --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">{{ __('registration.fields.club') }}</span>
                    </label>
                    <input
                        type="text"
                        name="club"
                        placeholder="{{ __('registration.fields.club_placeholder') }}"
                        class="input input-bordered">
                </div>

                {{-- Champs personnalisés --}}
                @if($event->formFields->where('is_visible', true)->count() > 0)
                    <div class="divider"></div>

                    @foreach($event->formFields->where('is_visible', true)->sortBy('order') as $field)
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-error">*</span>
                                    @endif
                                </span>
                            </label>

                            @switch($field->type)
                                @case('text')
                                @case('email')
                                @case('tel')
                                @case('number')
                                    <input
                                        type="{{ $field->type }}"
                                        name="custom_{{ $field->name }}"
                                        placeholder="{{ $field->placeholder }}"
                                        class="input input-bordered"
                                        {{ $field->is_required ? 'required' : '' }}>
                                    @break

                                @case('date')
                                    <input
                                        type="date"
                                        name="custom_{{ $field->name }}"
                                        class="input input-bordered"
                                        {{ $field->is_required ? 'required' : '' }}>
                                    @break

                                @case('textarea')
                                    <textarea
                                        name="custom_{{ $field->name }}"
                                        placeholder="{{ $field->placeholder }}"
                                        class="textarea textarea-bordered"
                                        rows="3"
                                        {{ $field->is_required ? 'required' : '' }}></textarea>
                                    @break

                                @case('select')
                                    <select
                                        name="custom_{{ $field->name }}"
                                        class="select select-bordered"
                                        {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">{{ __('registration.fields.category_placeholder') }}</option>
                                        @foreach($field->options ?? [] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('radio')
                                    <div class="flex flex-wrap gap-4">
                                        @foreach($field->options ?? [] as $option)
                                            <label class="label cursor-pointer gap-2">
                                                <input
                                                    type="radio"
                                                    name="custom_{{ $field->name }}"
                                                    value="{{ $option }}"
                                                    class="radio radio-primary"
                                                    {{ $field->is_required ? 'required' : '' }}>
                                                <span class="label-text">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('checkbox')
                                    <label class="label cursor-pointer justify-start gap-2">
                                        <input
                                            type="checkbox"
                                            name="custom_{{ $field->name }}"
                                            value="1"
                                            class="checkbox checkbox-primary"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        <span class="label-text">{{ $field->placeholder }}</span>
                                    </label>
                                    @break
                            @endswitch

                            @if($field->help_text)
                                <label class="label">
                                    <span class="label-text-alt text-base-content/70">{{ $field->help_text }}</span>
                                </label>
                            @endif
                        </div>
                    @endforeach
                @endif

                {{-- Boutons --}}
                <div class="flex gap-4 justify-end mt-6">
                    <button type="reset" class="btn btn-ghost">{{ __('registration.buttons.back') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('registration.buttons.submit') }}
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Lien discret vers admin --}}
    @guest
        <div class="text-center mt-8 opacity-30 text-xs">
            <a href="{{ route('login') }}" class="link link-hover">Administration</a>
        </div>
    @endguest

</div>

</body>
</html>
