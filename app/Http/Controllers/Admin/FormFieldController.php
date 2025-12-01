<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FormField;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    /**
     * Ajouter un champ personnalisé
     */
    public function store(Request $request, Event $event)
    {
      //  dd($request);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,email,tel,date,select,radio,checkbox,textarea,number',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
          //  'is_required' => 'boolean',
          //  'is_visible' => 'boolean',
            'order' => 'nullable|integer'
        ]);

        // Convertir les options (séparées par des virgules) en JSON
        if (!empty($validated['options'])) {
            $optionsArray = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $optionsArray;
        }

        // Déterminer l'ordre si non fourni
        if (!isset($validated['order'])) {
            $maxOrder = $event->formFields()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Gérer les checkboxes
        $validated['is_required'] = $request->has('is_required');
        $validated['is_visible'] = $request->has('is_visible');


        $event->formFields()->create($validated);

        return back()->with('success', 'Champ personnalisé ajouté avec succès !');
    }

    /**
     * Mettre à jour un champ personnalisé
     */
    public function update(Request $request, Event $event, FormField $formField)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,email,tel,date,select,radio,checkbox,textarea,number',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
          //  'is_required' => 'boolean',
          //  'is_visible' => 'boolean',
            'order' => 'nullable|integer'
        ]);

        // Convertir les options
        if (!empty($validated['options'])) {
            $optionsArray = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $optionsArray;
        }

        // Gérer les checkboxes
        $validated['is_required'] = $request->has('is_required');
        $validated['is_visible'] = $request->has('is_visible');

        $formField->update($validated);

        return back()->with('success', 'Champ mis à jour avec succès !');
    }

    /**
     * Supprimer un champ personnalisé
     */
    public function destroy(Event $event, FormField $formField)
    {
        $formField->delete();

        return back()->with('success', 'Champ supprimé avec succès !');
    }

    /**
     * Réorganiser l'ordre des champs
     */
    public function reorder(Request $request, Event $event)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:form_fields,id',
            'fields.*.order' => 'required|integer'
        ]);

        foreach ($validated['fields'] as $fieldData) {
            FormField::where('id', $fieldData['id'])
                ->update(['order' => $fieldData['order']]);
        }

        return response()->json(['success' => true]);
    }
}
