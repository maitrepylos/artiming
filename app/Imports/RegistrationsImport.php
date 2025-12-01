<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class RegistrationsImport
{
    protected $event;
    protected $results = [
        'success' => 0,
        'errors' => 0,
        'error_messages' => [],
        'categories_created' => []
    ];

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Importer depuis un fichier Excel
     */
    public function import($filePath): array
    {
        try {
            // Charger le fichier Excel
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            DB::beginTransaction();

            $rowCount = 0;

            // Lire les données (ligne 2 = ignorer les en-têtes)
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getCalculatedValue();
                }

                // Ignorer les lignes vides
                if (empty(array_filter($data))) {
                    continue;
                }

                try {
                    // Mapper les colonnes
                    // A: ID (ignoré), B: Nom, C: Prénom, D: Sexe, E: Épreuve,
                    // F: Date naissance, G: Nationalité, H: Club, I: Dossard, J: Acquité

                    $nom = strtoupper(trim($data[1] ?? ''));
                    $prenom = ucfirst(strtolower(trim($data[2] ?? '')));
                    $sexe = strtoupper(trim($data[3] ?? 'X'));
                    $epreuveName = trim($data[4] ?? '');
                    $dateNaissance = $this->parseDate($data[5] ?? null);
                    $nationalite = strtoupper(trim($data[6] ?? 'BEL'));
                    $club = trim($data[7] ?? null);
                    $bibNumber = !empty($data[8]) ? (int)$data[8] : null;
                    $isPaid = !empty($data[9]) ? (bool)$data[9] : false;

                    // Validation
                    if (empty($nom) || empty($prenom)) {
                        throw new Exception("Nom ou prénom manquant à la ligne " . ($rowCount + 2));
                    }

                    if (!in_array($sexe, ['M', 'F', 'X'])) {
                        throw new Exception("Sexe invalide à la ligne " . ($rowCount + 2) . " (doit être M, F ou X)");
                    }

                    if (!$dateNaissance) {
                        throw new Exception("Date de naissance invalide à la ligne " . ($rowCount + 2));
                    }

                    if (empty($epreuveName)) {
                        throw new Exception("Nom de catégorie manquant à la ligne " . ($rowCount + 2));
                    }

                    // Trouver ou créer la catégorie
                    $category = $this->findOrCreateCategory($epreuveName);

                    // Créer l'inscription
                    Registration::create([
                        'event_id' => $this->event->id,
                        'category_id' => $category->id,
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'sexe' => $sexe,
                        'date_naissance' => $dateNaissance,
                        'nationalite' => $nationalite,
                        'club' => $club,
                        'bib_number' => $bibNumber,
                        'is_paid' => $isPaid,
                        'status' => $isPaid ? 'confirmed' : 'pending'
                    ]);

                    $this->results['success']++;
                    $rowCount++;

                } catch (Exception $e) {
                    $this->results['errors']++;
                    $this->results['error_messages'][] = $e->getMessage();

                    // Limiter à 10 messages d'erreur
                    if (count($this->results['error_messages']) > 10) {
                        $remaining = $this->results['errors'] - 10;
                        $this->results['error_messages'][] = "... et $remaining autres erreurs";
                        break;
                    }
                }
            }

            DB::commit();

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

        } catch (Exception $e) {
            DB::rollBack();
            $this->results['error_messages'][] = "Erreur critique : " . $e->getMessage();
        }

        return $this->results;
    }

    /**
     * Trouver ou créer une catégorie
     */
    protected function findOrCreateCategory(string $categoryName): Category
    {
        // Chercher d'abord si la catégorie existe déjà
        $category = $this->event->categories()
            ->where('name', $categoryName)
            ->first();

        // Si elle existe, la retourner
        if ($category) {
            return $category;
        }

        // Sinon, créer la catégorie
        $code = Str::slug($categoryName, '_');

        // S'assurer que le code est unique
        $baseCode = $code;
        $counter = 1;
        while (Category::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }

        $category = $this->event->categories()->create([
            'name' => $categoryName,
            'code' => $code,
            'is_active' => true,
            'order' => $this->event->categories()->max('order') + 1 ?? 0
        ]);

        // Ajouter à la liste des catégories créées
        $this->results['categories_created'][] = $categoryName;

        return $category;
    }

    /**
     * Parser une date depuis Excel
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Si c'est un nombre (format Excel)
            if (is_numeric($value)) {
                $dateTime = ExcelDate::excelToDateTimeObject($value);
                return $dateTime->format('Y-m-d');
            }

            // Si c'est une chaîne de caractères
            // Essayer d-m-Y (format européen)
            $date = \DateTime::createFromFormat('d-m-Y', $value);
            if ($date) {
                return $date->format('Y-m-d');
            }

            // Essayer Y-m-d (format ISO)
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if ($date) {
                return $date->format('Y-m-d');
            }

            // Essayer d/m/Y
            $date = \DateTime::createFromFormat('d/m/Y', $value);
            if ($date) {
                return $date->format('Y-m-d');
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }
}
