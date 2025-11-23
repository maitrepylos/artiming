<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

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
            'logo' => 'nullable|image|max:2048'
        ]);

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

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé');
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
