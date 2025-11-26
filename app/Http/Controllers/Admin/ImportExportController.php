<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Exports\RegistrationsExport;
use App\Imports\RegistrationsImport;
use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    /**
     * Page de gestion import/export
     */
    public function index(Event $event)
    {
        $event->load('categories', 'registrations');

        return view('admin.events.import-export', compact('event'));
    }

    /**
     * Exporter les inscriptions en Excel
     */
    public function export(Event $event)
    {
        if ($event->registrations()->count() === 0) {
            return back()->with('error', 'Aucun participant à exporter !');
        }

        $export = new RegistrationsExport($event);
        return $export->download();
    }

    /**
     * Importer les inscriptions depuis Excel
     */
    public function import(Request $request, Event $event)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240' // Max 10MB
        ]);

        $file = $request->file('excel_file');

        try {
            $import = new RegistrationsImport($event);
            $results = $import->import($file->getRealPath());

            $message = "Import terminé : {$results['success']} participants importés";

            if ($results['errors'] > 0) {
                $message .= ", {$results['errors']} erreurs rencontrées";
            }

            return back()
                ->with('success', $message)
                ->with('import_results', $results);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Vider toutes les inscriptions d'un événement
     */
    public function truncate(Event $event)
    {
        $count = $event->registrations()->count();
        $event->registrations()->delete();

        return back()->with('success', "$count inscriptions supprimées");
    }
}
