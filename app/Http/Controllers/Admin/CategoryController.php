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
         //   'price' => 'nullable|numeric|min:0',
         //   'max_participants' => 'nullable|integer|min:1',
        ]);

        $event->categories()->create($validated);

        return back()->with('success', 'Catégorie ajoutée');
    }

    public function update(Request $request, Event $event, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
         //   'max_participants' => 'nullable|integer|min:1',
         //   'is_active' => 'boolean'
        ]);

        $category->update($validated);

        return back()->with('success', 'Catégorie mise à jour');
    }

    public function destroy(Event $event, Category $category)
    {
        $category->delete();
        return back()->with('success', 'Catégorie supprimée');
    }
}
