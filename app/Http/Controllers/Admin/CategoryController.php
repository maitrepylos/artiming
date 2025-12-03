<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:categories,code',
            'order' => 'nullable|integer|min:0',
        ]);

        // Déterminer l'ordre si non fourni
        if (!isset($validated['order']) || $validated['order'] === null) {
            $maxOrder = $event->categories()->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        // Par défaut, la catégorie est active
        $validated['is_active'] = true;

        $event->categories()->create($validated);

        return redirect()->route('admin.events.edit', $event)->withFragment('categories')->with('success', 'Catégorie ajoutée');
    }

    public function update(Request $request, Event $event, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Gérer le checkbox is_active
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.events.edit', $event)->withFragment('categories')->with('success', 'Catégorie mise à jour');
    }

    public function destroy(Event $event, Category $category)
    {
        $category->delete();
        return redirect()->route('admin.events.edit', $event)->withFragment('categories')->with('success', 'Catégorie supprimée');
    }
}
