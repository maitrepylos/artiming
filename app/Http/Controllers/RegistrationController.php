<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Facades\Validator;

// app/Http/Controllers/RegistrationController.php
class RegistrationController extends Controller
{
    /**
     * Afficher le formulaire d'inscription public
     */
    public function show($slug)
    {
        $event = Event::with(['categories' => function($q) {
            $q->where('is_active', true);
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

        // Validation dynamique basée sur les champs de l'événement
        $rules = [
            'nom' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZàâäéèêëîïôöûùüÿæœÀÂÄÉÈÊËÎÏÔÖÛÙÜŸÆŒ\s\-\.]+$/u'],
            'prenom' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-ZàâäéèêëîïôöûùüÿæœÀÂÄÉÈÊËÎÏÔÖÛÙÜŸÆŒ\s\-\.]+$/u'],
            'sexe' => ['required', 'in:M,F,X'],
            'date_naissance' => ['required', 'date', 'before:today', 'after:' . now()->subYears(100)->format('Y-m-d')],
            'category_id' => ['required', 'exists:categories,id'],
            'nationalite' => ['required', 'string', 'size:3'],
            'club' => ['nullable', 'string', 'max:100'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->view('registration.errors', [
                'errors' => $validator->errors()->all()
            ], 422);
        }

        // Vérifier que la catégorie appartient à cet événement
        $category = $event->categories()->findOrFail($request->category_id);

        // Vérifier si la catégorie est pleine
        if ($category->isFull) {
            return response()->view('registration.errors', [
                'errors' => ['Cette catégorie est complète.']
            ], 422);
        }

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

        return response()->view('registration.success', [
            'registration' => $registration,
            'event' => $event
        ]);
    }

    /**
     * Rechercher des participants (HTMX - pour dossart)
     */
    public function search(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
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
    public function update(Request $request, $slug, $id)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $registration = Registration::where('event_id', $event->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bib_number' => ['nullable', 'integer', 'unique:registrations,bib_number,' . $id],
            'is_paid' => ['boolean'],
            'nom' => ['required', 'string'],
            'prenom' => ['required', 'string'],
            'sexe' => ['required', 'in:M,F,X'],
            'date_naissance' => ['required', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->view('registration.errors', [
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $registration->update($request->all());

        return response()->view('admin.partials.update-success', [
            'registration' => $registration
        ]);
    }
}
