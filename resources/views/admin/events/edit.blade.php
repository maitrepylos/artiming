@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">{{ $event->name }}</h1>
                <p class="text-base-content/70">Configuration de l'événement</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                ← Retour
            </a>
        </div>

        {{-- Tabs --}}
        <div class="tabs tabs-boxed mb-6">
            <a class="tab tab-active" data-tab="info">Informations</a>
            <a class="tab" data-tab="categories">Catégories</a>
            <a class="tab" data-tab="form-fields">Champs personnalisés</a>
            <a class="tab" data-tab="registrations">Inscriptions</a>
            <a class="tab" data-tab="import-export">Import/Export</a>
        </div>

        {{-- Tab: Informations - VISIBLE PAR DÉFAUT --}}
        <div id="tab-info" class="tab-content" style="display: block;">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Informations de l'événement</h2>

                    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Nom de l'événement</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $event->name }}"
                                class="input input-bordered"
                                required>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Description</span>
                            </label>
                            <textarea
                                name="description"
                                class="textarea textarea-bordered"
                                rows="3">{{ $event->description }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Date de l'événement</span>
                                </label>
                                <input
                                    type="date"
                                    name="event_date"
                                    value="{{ $event->event_date->format('Y-m-d') }}"
                                    class="input input-bordered"
                                    required>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Événement actif</span>
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        class="toggle toggle-success"
                                        {{ $event->is_active ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Logo</span>
                            </label>
                            <input
                                type="file"
                                name="logo"
                                class="file-input file-input-bordered"
                                accept="image/*">
                            @if($event->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($event->logo) }}" class="h-20" alt="Logo actuel">
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-4 justify-end">
                            <button type="submit" class="btn btn-primary">
                                Sauvegarder
                            </button>
                        </div>
                    </form>

                    <div class="divider"></div>

                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="font-bold">Zone de danger</h3>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Toutes les inscriptions seront perdues.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error btn-sm mt-2">
                                    Supprimer l'événement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Catégories --}}
        <div id="tab-categories" class="tab-content" style="display: none;">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Catégories de courses</h2>

                    {{-- Liste des catégories --}}
                    <div class="overflow-x-auto mb-6">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Ordre</th>
                                <th>Nom</th>
                                <th>Code</th>
                                <th>Inscriptions</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($event->categories->sortBy('order') as $category)
                                <tr>
                                    <td>{{ $category->order ?? '-' }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td><code>{{ $category->code }}</code></td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->registrations->count() }}</span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-ghost">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="flex gap-2">
                                        {{-- Bouton Éditer --}}
                                        <button type="button" class="btn btn-info btn-xs" onclick="document.getElementById('modal-edit-category-{{ $category->id }}').showModal()">
                                            Éditer
                                        </button>
                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route('admin.events.categories.destroy', [$event, $category]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs" onclick="return confirm('Supprimer cette catégorie ? Les inscriptions associées seront également supprimées.')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Modal d'édition pour cette catégorie --}}
                                <dialog id="modal-edit-category-{{ $category->id }}" class="modal">
                                    <div class="modal-box w-11/12 max-w-lg">
                                        <form method="dialog">
                                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <h3 class="font-bold text-lg mb-4">Éditer la catégorie : {{ $category->name }}</h3>

                                        <form action="{{ route('admin.events.categories.update', [$event, $category]) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-control mb-4">
                                                <label class="label">
                                                    <span class="label-text">Nom de la catégorie <span class="text-error">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="name"
                                                    value="{{ $category->name }}"
                                                    class="input input-bordered"
                                                    required>
                                            </div>

                                            <div class="form-control mb-4">
                                                <label class="label">
                                                    <span class="label-text">Code unique</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    value="{{ $category->code }}"
                                                    class="input input-bordered bg-base-200"
                                                    disabled>
                                                <label class="label">
                                                    <span class="label-text-alt text-warning">Non modifiable (utilisé comme identifiant)</span>
                                                </label>
                                            </div>

                                            <div class="form-control mb-4">
                                                <label class="label">
                                                    <span class="label-text">Ordre d'affichage</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    name="order"
                                                    value="{{ $category->order }}"
                                                    class="input input-bordered"
                                                    min="0">
                                            </div>

                                            <div class="form-control mb-6">
                                                <label class="label cursor-pointer">
                                                    <span class="label-text">Catégorie active</span>
                                                    <input
                                                        type="checkbox"
                                                        name="is_active"
                                                        class="toggle toggle-success"
                                                        {{ $category->is_active ? 'checked' : '' }}>
                                                </label>
                                                <label class="label">
                                                    <span class="label-text-alt">Si désactivée, la catégorie n'apparaîtra plus dans le formulaire d'inscription</span>
                                                </label>
                                            </div>

                                            <div class="modal-action">
                                                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-edit-category-{{ $category->id }}').close()">
                                                    Annuler
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Enregistrer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucune catégorie créée</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divider">Ajouter une catégorie</div>

                    {{-- Formulaire d'ajout --}}
                    <form action="{{ route('admin.events.categories.store', $event) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom de la catégorie <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Ex: Ultra 3000"
                                    class="input input-bordered"
                                    required>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Code unique <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="code"
                                    placeholder="Ex: ultra_3000"
                                    class="input input-bordered"
                                    pattern="[a-zA-Z0-9_]+"
                                    required>
                                <label class="label">
                                    <span class="label-text-alt">Lettres, chiffres et underscores uniquement</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Ordre d'affichage</span>
                                </label>
                                <input
                                    type="number"
                                    name="order"
                                    placeholder="Auto"
                                    class="input input-bordered"
                                    min="0">
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter la catégorie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tab: Champs personnalisés --}}
        <div id="tab-form-fields" class="tab-content" style="display: none;">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Champs personnalisés du formulaire</h2>
                    <p class="text-sm text-base-content/70 mb-4">
                        Ajoutez des champs supplémentaires au formulaire d'inscription (email, téléphone, taille de t-shirt, etc.)
                    </p>

                    {{-- Liste des champs --}}
                    <div class="overflow-x-auto mb-6">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Ordre</th>
                                <th>Label</th>
                                <th>Nom technique</th>
                                <th>Type</th>
                                <th>Requis</th>
                                <th>Visible</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($event->formFields as $field)
                                <tr>
                                    <td>{{ $field->order }}</td>
                                    <td>{{ $field->label }}</td>
                                    <td><code class="text-xs">{{ $field->name }}</code></td>
                                    <td><span class="badge badge-sm">{{ $field->type }}</span></td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge badge-error badge-sm">Oui</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm">Non</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($field->is_visible)
                                            <span class="badge badge-success badge-sm">Oui</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm">Non</span>
                                        @endif
                                    </td>
                                    <td class="flex gap-2">
                                        {{-- Bouton Éditer --}}
                                        <button type="button" class="btn btn-info btn-xs" onclick="document.getElementById('modal-edit-field-{{ $field->id }}').showModal()">
                                            Éditer
                                        </button>
                                        {{-- Bouton Supprimer --}}
                                        <form action="{{ route('admin.events.form-fields.destroy', [$event, $field]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs" onclick="return confirm('Supprimer ce champ ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Modal d'édition pour ce champ --}}
                                <dialog id="modal-edit-field-{{ $field->id }}" class="modal">
                                    <div class="modal-box w-11/12 max-w-2xl">
                                        <form method="dialog">
                                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <h3 class="font-bold text-lg mb-4">Éditer le champ : {{ $field->label }}</h3>

                                        <form action="{{ route('admin.events.form-fields.update', [$event, $field]) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Nom technique</span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value="{{ $field->name }}"
                                                        class="input input-bordered input-sm bg-base-200"
                                                        disabled>
                                                    <label class="label">
                                                        <span class="label-text-alt text-warning">Non modifiable</span>
                                                    </label>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Label affiché <span class="text-error">*</span></span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        name="label"
                                                        value="{{ $field->label }}"
                                                        class="input input-bordered input-sm"
                                                        required>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Type de champ <span class="text-error">*</span></span>
                                                    </label>
                                                    <select name="type" class="select select-bordered select-sm" required onchange="toggleOptionsField(this, {{ $field->id }})">
                                                        <option value="text" {{ $field->type === 'text' ? 'selected' : '' }}>Texte simple</option>
                                                        <option value="email" {{ $field->type === 'email' ? 'selected' : '' }}>Email</option>
                                                        <option value="tel" {{ $field->type === 'tel' ? 'selected' : '' }}>Téléphone</option>
                                                        <option value="number" {{ $field->type === 'number' ? 'selected' : '' }}>Nombre</option>
                                                        <option value="date" {{ $field->type === 'date' ? 'selected' : '' }}>Date</option>
                                                        <option value="textarea" {{ $field->type === 'textarea' ? 'selected' : '' }}>Zone de texte</option>
                                                        <option value="select" {{ $field->type === 'select' ? 'selected' : '' }}>Liste déroulante</option>
                                                        <option value="radio" {{ $field->type === 'radio' ? 'selected' : '' }}>Boutons radio</option>
                                                        <option value="checkbox" {{ $field->type === 'checkbox' ? 'selected' : '' }}>Case à cocher</option>
                                                    </select>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Ordre d'affichage</span>
                                                    </label>
                                                    <input
                                                        type="number"
                                                        name="order"
                                                        value="{{ $field->order }}"
                                                        class="input input-bordered input-sm"
                                                        min="0">
                                                </div>
                                            </div>

                                            <div class="form-control mb-4" id="options-field-{{ $field->id }}" style="{{ in_array($field->type, ['select', 'radio']) ? '' : 'display: none;' }}">
                                                <label class="label">
                                                    <span class="label-text">Options (pour select/radio)</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="options"
                                                    value="{{ is_array($field->options) ? implode(', ', $field->options) : $field->options }}"
                                                    placeholder="Ex: Petit, Moyen, Grand, XL"
                                                    class="input input-bordered input-sm">
                                                <label class="label">
                                                    <span class="label-text-alt">Séparez les options par des virgules</span>
                                                </label>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Placeholder</span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        name="placeholder"
                                                        value="{{ $field->placeholder }}"
                                                        placeholder="Texte d'exemple dans le champ"
                                                        class="input input-bordered input-sm">
                                                </div>

                                                <div class="form-control">
                                                    <label class="label">
                                                        <span class="label-text">Texte d'aide</span>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        name="help_text"
                                                        value="{{ $field->help_text }}"
                                                        placeholder="Information supplémentaire"
                                                        class="input input-bordered input-sm">
                                                </div>
                                            </div>

                                            <div class="flex gap-6 mb-6">
                                                <div class="form-control">
                                                    <label class="label cursor-pointer gap-2">
                                                        <input type="checkbox" name="is_required" class="checkbox checkbox-sm checkbox-error" {{ $field->is_required ? 'checked' : '' }}>
                                                        <span class="label-text">Champ obligatoire</span>
                                                    </label>
                                                </div>

                                                <div class="form-control">
                                                    <label class="label cursor-pointer gap-2">
                                                        <input type="checkbox" name="is_visible" class="checkbox checkbox-sm checkbox-success" {{ $field->is_visible ? 'checked' : '' }}>
                                                        <span class="label-text">Visible dans le formulaire</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="modal-action">
                                                <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-edit-field-{{ $field->id }}').close()">
                                                    Annuler
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Enregistrer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucun champ personnalisé</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divider">Ajouter un champ</div>

                    {{-- Formulaire d'ajout --}}
                    <form action="{{ route('admin.events.form-fields.store', $event) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom technique <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Ex: email, telephone, taille_tshirt"
                                    class="input input-bordered input-sm"
                                    pattern="[a-z0-9_]+"
                                    required>
                                <label class="label">
                                    <span class="label-text-alt">Minuscules, chiffres et underscores uniquement</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Label affiché <span class="text-error">*</span></span>
                                </label>
                                <input
                                    type="text"
                                    name="label"
                                    placeholder="Ex: Adresse email, Numéro de téléphone"
                                    class="input input-bordered input-sm"
                                    required>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Type de champ <span class="text-error">*</span></span>
                                </label>
                                <select name="type" class="select select-bordered select-sm" required id="field-type">
                                    <option value="text">Texte simple</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Téléphone</option>
                                    <option value="number">Nombre</option>
                                    <option value="date">Date</option>
                                    <option value="textarea">Zone de texte</option>
                                    <option value="select">Liste déroulante</option>
                                    <option value="radio">Boutons radio</option>
                                    <option value="checkbox">Case à cocher</option>
                                </select>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Ordre d'affichage</span>
                                </label>
                                <input
                                    type="number"
                                    name="order"
                                    placeholder="Auto"
                                    class="input input-bordered input-sm"
                                    min="0">
                            </div>
                        </div>

                        <div class="form-control mb-4" id="options-field" style="display: none;">
                            <label class="label">
                                <span class="label-text">Options (pour select/radio)</span>
                            </label>
                            <input
                                type="text"
                                name="options"
                                placeholder="Ex: Petit, Moyen, Grand, XL"
                                class="input input-bordered input-sm">
                            <label class="label">
                                <span class="label-text-alt">Séparez les options par des virgules</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Placeholder</span>
                                </label>
                                <input
                                    type="text"
                                    name="placeholder"
                                    placeholder="Texte d'exemple dans le champ"
                                    class="input input-bordered input-sm">
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Texte d'aide</span>
                                </label>
                                <input
                                    type="text"
                                    name="help_text"
                                    placeholder="Information supplémentaire"
                                    class="input input-bordered input-sm">
                            </div>
                        </div>

                        <div class="flex gap-4 mb-4">
                            <div class="form-control">
                                <label class="label cursor-pointer gap-2">
                                    <input type="checkbox" name="is_required" class="checkbox checkbox-sm">
                                    <span class="label-text">Champ obligatoire</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer gap-2">
                                    <input type="checkbox" name="is_visible" class="checkbox checkbox-sm" checked>
                                    <span class="label-text">Visible</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter le champ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tab: Inscriptions --}}
        <div id="tab-registrations" class="tab-content" style="display: none;">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Liste des inscriptions ({{ $event->registrations->count() }})</h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                            <tr>
                                <th>Dossard</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Catégorie</th>
                                <th>Acquité</th>
                                <th>Inscrit le</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($event->registrations as $registration)
                                <tr>
                                    <td>
                                        @if($registration->bib_number)
                                            <span class="badge badge-primary">{{ $registration->bib_number }}</span>
                                        @else
                                            <span class="badge badge-ghost">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $registration->nom }}</td>
                                    <td>{{ $registration->prenom }}</td>
                                    <td>{{ $registration->category->name }}</td>
                                    <td>
                                        @if($registration->is_paid)
                                            <span class="badge badge-success">✓</span>
                                        @else
                                            <span class="badge badge-error">✗</span>
                                        @endif
                                    </td>
                                    <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucune inscription</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Import/Export --}}
        <div id="tab-import-export" class="tab-content" style="display: none;">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-center">
                    <h2 class="card-title justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Gestion Import/Export Excel
                    </h2>
                    <p class="text-base-content/70 mb-6">
                        Importez ou exportez les inscriptions au format Excel
                    </p>

                    <div class="stats shadow mb-6">
                        <div class="stat">
                            <div class="stat-title">Total participants</div>
                            <div class="stat-value text-primary">{{ $event->registrations->count() }}</div>
                        </div>
                    </div>

                    <a href="{{ route('admin.events.import-export', $event) }}" class="btn btn-primary btn-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Accéder à Import/Export
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Gestion des tabs avec persistance dans l'URL
        function activateTab(tabName) {
            // Retirer active de tous les tabs
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('tab-active'));

            // Cacher tous les contenus
            document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');

            // Activer le tab
            const tab = document.querySelector(`.tab[data-tab="${tabName}"]`);
            if (tab) {
                tab.classList.add('tab-active');
            }

            const tabContent = document.getElementById(`tab-${tabName}`);
            if (tabContent) {
                tabContent.style.display = 'block';
            }

            // Sauvegarder dans l'URL
            window.location.hash = tabName;
        }

        // Au chargement, restaurer le tab depuis l'URL
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.replace('#', '');
            if (hash) {
                activateTab(hash);
            }
        });

        // Clic sur les tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const tabName = this.dataset.tab;
                activateTab(tabName);
            });
        });

        // Afficher/masquer le champ options selon le type sélectionné (formulaire d'ajout)
        const fieldTypeSelect = document.getElementById('field-type');
        const optionsField = document.getElementById('options-field');

        if (fieldTypeSelect && optionsField) {
            fieldTypeSelect.addEventListener('change', function() {
                if (['select', 'radio'].includes(this.value)) {
                    optionsField.style.display = 'block';
                } else {
                    optionsField.style.display = 'none';
                }
            });
        }

        // Afficher/masquer le champ options dans les modals d'édition
        function toggleOptionsField(selectElement, fieldId) {
            const optionsDiv = document.getElementById('options-field-' + fieldId);
            if (optionsDiv) {
                if (['select', 'radio'].includes(selectElement.value)) {
                    optionsDiv.style.display = 'block';
                } else {
                    optionsDiv.style.display = 'none';
                }
            }
        }
    </script>

@endsection
