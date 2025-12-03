<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationData;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    /**
     * Afficher le formulaire d'inscription public
     */
    public function show($slug)
    {
        $event = Event::with(['categories' => function($q) {
            $q->where('is_active', true);
        }, 'formFields' => function($q) {
            $q->where('is_visible', true)->orderBy('order');
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('registration.form', compact('event'));
    }

    /**
     * Traiter l'inscription (HTMX)
     */
    public function store(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Validation des champs de base
        $rules = [
            'nom' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZàâäéèêëîïôöûùüÿæœÀÂÄÉÈÊËÎÏÔÖÛÙÜŸÆŒ\s\-\.]+$/u'],
            'prenom' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZàâäéèêëîïôöûùüÿæœÀÂÄÉÈÊËÎÏÔÖÛÙÜŸÆŒ\s\-\.]+$/u'],
            'sexe' => ['required', 'in:M,F,X'],
            'date_naissance' => ['required', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
            'category_id' => ['required', 'exists:categories,id'],
            'nationalite' => ['required', 'string', 'size:3'],
            'club' => ['nullable', 'string', 'max:100'],
        ];

        // Ajouter les règles de validation pour les champs personnalisés
        $customFields = $event->formFields()->where('is_visible', true)->get();
        foreach ($customFields as $field) {
            $fieldName = "custom_{$field->name}";
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Règles selon le type
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'tel':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:20';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'text':
                case 'textarea':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:1000';
                    break;
                case 'select':
                case 'radio':
                    if (!empty($field->options)) {
                        $fieldRules[] = 'in:' . implode(',', $field->options);
                    }
                    break;
                case 'checkbox':
                    $fieldRules[] = 'boolean';
                    break;
            }

            $rules[$fieldName] = $fieldRules;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->view('registration.errors', [
                'errors' => $validator->errors()->all()
            ], 422);
        }

        // Vérifier que la catégorie appartient à cet événement
        $category = $event->categories()->findOrFail($request->category_id);

/*        // Vérifier si la catégorie est pleine
        if ($category->isFull) {
            return response()->view('registration.errors', [
                'errors' => ['Cette catégorie est complète.']
            ], 422);
        }*/

        // Créer l'inscription
        $registration = Registration::create([
            'event_id' => $event->id,
            'category_id' => $category->id,
            'nom' => strtoupper($request->nom),
            'prenom' => ucfirst(strtolower($request->prenom)),
            'sexe' => $request->sexe,
            'date_naissance' => $request->date_naissance,
            'nationalite' => $request->nationalite,
            'club' => $request->club,
            'status' => 'pending'
        ]);

        // Sauvegarder les données des champs personnalisés
        foreach ($customFields as $field) {
            $fieldName = "custom_{$field->name}";
            if ($request->has($fieldName)) {
                RegistrationData::create([
                    'registration_id' => $registration->id,
                    'form_field_id' => $field->id,
                    'value' => $request->input($fieldName)
                ]);
            }
        }

        return response()->view('registration.success', [
            'registration' => $registration,
            'event' => $event
        ]);
    }

    /**
     * Rechercher des participants (HTMX - pour dossard)
     */
    public function search(Request $request, $eventId)
    {
        // Récupérer l'événement par son ID
        $event = \App\Models\Event::findOrFail($eventId);

        $term = $request->input('search', '');

        if (strlen($term) < 2) {
            return response('', 200);
        }

        $participants = Registration::where('event_id', $event->id)
            ->search($term)
            ->with('category')
            ->limit(10)
            ->get();

        return view('admin.partials.search-results', compact('participants'));
    }

    /**
     * Mettre à jour un participant (dossard, paiement)
     */
    public function update(Request $request, Event $event, Registration $registration)
    {
        $validator = Validator::make($request->all(), [
            'bib_number' => ['nullable', 'integer', 'unique:registrations,bib_number,' . $registration->id],
            'is_paid' => ['boolean'],
            'nom' => ['required', 'string'],
            'prenom' => ['required', 'string'],
            'sexe' => ['required', 'in:M,F,X'],
            'date_naissance' => ['required', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
            'nationalite' => ['required', 'string'],
            'club' => ['nullable', 'string'],
            'code_uci' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->view('registration.errors', [
                'errors' => $validator->errors()->all()
            ], 422);
        }

        // Gérer le checkbox is_paid qui n'est pas envoyé si non coché
        $data = $request->all();
        $data['is_paid'] = $request->has('is_paid');

        $registration->update($data);

        return response()->view('admin.partials.update-success', [
            'registration' => $registration
        ]);
    }
}
