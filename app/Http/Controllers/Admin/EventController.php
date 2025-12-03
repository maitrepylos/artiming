<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('categories', 'registrations')
            ->orderBy('event_date', 'desc')
            ->get();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'locale' => 'required|string|in:' . implode(',', array_keys(Event::$availableLocales)),
            'logo' => 'nullable|image|max:2048'
        ]);

        // Gérer le checkbox is_active
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $event = Event::create($validated);

        return redirect()->route('admin.events.edit', $event)
            ->with('success', 'Événement créé avec succès !');
    }

    public function edit(Event $event)
    {
        $event->load(['categories', 'formFields', 'registrations']);
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'locale' => 'required|string|in:' . implode(',', array_keys(Event::$availableLocales)),
            'logo' => 'nullable|image|max:2048'
        ]);

        // Gérer le checkbox is_active (qui n'est pas envoyé si non coché)
        $validated['is_active'] = $request->has('is_active');

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si existe
            if ($event->logo && Storage::disk('public')->exists($event->logo)) {
                Storage::disk('public')->delete($event->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // Mettre à jour l'événement
        $event->update($validated);

        return back()->with('success', 'Événement mis à jour avec succès !');
    }

    public function destroy(Event $event)
    {
        // Supprimer le logo si existe
        if ($event->logo && Storage::disk('public')->exists($event->logo)) {
            Storage::disk('public')->delete($event->logo);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé avec succès !');
    }

    /**
     * Page de gestion des dossards
     */
    public function manageBibs(Event $event)
    {
        $registrations = $event->registrations()
            ->with('category')
            ->orderBy('nom')
            ->get();

        return view('admin.events.bibs', compact('event', 'registrations'));
    }

    /**
     * Export Excel
     */
    public function export(Event $event)
    {
        return Excel::download(new RegistrationsExport($event),
            $event->slug . '-registrations.xlsx');
    }
}
