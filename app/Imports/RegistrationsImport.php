<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Category;
use App\Models\FormField;
use App\Models\RegistrationData;
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
        'categories_created' => [],
        'fields_created' => []
    ];

    // Colonnes standard (en minuscules pour la comparaison)
    // Ces colonnes ne seront PAS ajoutées comme champs supplémentaires
    protected $standardColumns = [
        'id',
        'nom',
        'prenom',
        'prénom',
        'sexe',
        'genre',
        'epreuve',
        'épreuve',
        'categorie',
        'catégorie',
        'course',
        'date_naissance',
        'date de naissance',
        'datenaissance',
        'nationalite',
        'nationalité',
        'pays',
        'club',
        'club / team / association',
        'club/team/association',
        'equipe',
        'équipe',
        'team',
        'association',
        // Dossard (géré par le formulaire dossard)
        'dossard',
        'bib',
        'bib_number',
        'numero',
        'numéro',
        // Acquité/Payé
        'acquite',
        'acquité',
        'paye',
        'payé',
        'is_paid',
        'paid'
    ];

    // Mapping des en-têtes vers les colonnes standard
    protected $headerMapping = [];

    // Champs supplémentaires détectés
    protected $extraFields = [];

    // FormFields créés ou existants (nom => id)
    protected $formFieldsMap = [];

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

            // Lire les en-têtes (première ligne)
            $headers = $this->readHeaders($worksheet);

            if (empty($headers)) {
                throw new Exception("Impossible de lire les en-têtes du fichier Excel");
            }

            // Détecter et créer les champs supplémentaires
            $this->detectAndCreateExtraFields($headers);

            $rowCount = 0;

            // Lire les données (ligne 2 = après les en-têtes)
            foreach ($worksheet->getRowIterator(2) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $data = [];
                $colIndex = 0;
                foreach ($cellIterator as $cell) {
                    $data[$colIndex] = $cell->getCalculatedValue();
                    $colIndex++;
                }

                // Ignorer les lignes vides
                if (empty(array_filter($data))) {
                    continue;
                }

                try {
                    // Créer l'inscription avec les données standard et supplémentaires
                    $this->createRegistration($data, $headers, $rowCount + 2);
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
     * Lire les en-têtes du fichier Excel
     */
    protected function readHeaders($worksheet): array
    {
        $headers = [];
        $headerRow = $worksheet->getRowIterator(1, 1)->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $colIndex = 0;
        foreach ($cellIterator as $cell) {
            $value = trim($cell->getValue() ?? '');
            if (!empty($value)) {
                $headers[$colIndex] = $value;
            }
            $colIndex++;

            // Limiter à 50 colonnes
            if ($colIndex >= 50) break;
        }

        return $headers;
    }

    /**
     * Détecter les colonnes supplémentaires et créer les FormFields
     */
    protected function detectAndCreateExtraFields(array $headers): void
    {
        // Charger les FormFields existants pour cet événement
        $existingFields = $this->event->formFields()->pluck('id', 'name')->toArray();
        $this->formFieldsMap = $existingFields;

        $maxOrder = $this->event->formFields()->max('order') ?? 0;

        foreach ($headers as $colIndex => $header) {
            $normalizedHeader = $this->normalizeHeader($header);

            // Vérifier si c'est une colonne standard
            if ($this->isStandardColumn($normalizedHeader)) {
                $this->headerMapping[$colIndex] = $this->mapToStandardField($normalizedHeader);
                continue;
            }

            // C'est une colonne supplémentaire
            $fieldName = Str::slug($header, '_');

            // Vérifier si le champ existe déjà
            if (!isset($this->formFieldsMap[$fieldName])) {
                // Créer le nouveau FormField
                $maxOrder++;
                $formField = FormField::create([
                    'event_id' => $this->event->id,
                    'name' => $fieldName,
                    'label' => $header,
                    'type' => $this->guessFieldType($header),
                    'is_required' => false,
                    'is_visible' => true,
                    'order' => $maxOrder,
                    'placeholder' => '',
                    'help_text' => 'Importé automatiquement depuis Excel'
                ]);

                $this->formFieldsMap[$fieldName] = $formField->id;
                $this->results['fields_created'][] = $header;
            }

            // Marquer cette colonne comme champ supplémentaire
            $this->extraFields[$colIndex] = $fieldName;
        }
    }

    /**
     * Normaliser un en-tête pour la comparaison
     */
    protected function normalizeHeader(string $header): string
    {
        $normalized = strtolower(trim($header));
        $normalized = str_replace(['é', 'è', 'ê', 'ë'], 'e', $normalized);
        $normalized = str_replace(['à', 'â', 'ä'], 'a', $normalized);
        $normalized = str_replace(['ù', 'û', 'ü'], 'u', $normalized);
        $normalized = str_replace(['î', 'ï'], 'i', $normalized);
        $normalized = str_replace(['ô', 'ö'], 'o', $normalized);
        $normalized = str_replace([' ', '-', '_'], '', $normalized);
        return $normalized;
    }

    /**
     * Vérifier si c'est une colonne standard
     */
    protected function isStandardColumn(string $normalizedHeader): bool
    {
        foreach ($this->standardColumns as $standard) {
            $normalizedStandard = $this->normalizeHeader($standard);
            if ($normalizedHeader === $normalizedStandard) {
                return true;
            }
        }
        return false;
    }

    /**
     * Mapper un en-tête vers le nom de champ standard
     */
    protected function mapToStandardField(string $normalizedHeader): string
    {
        $mapping = [
            'id' => 'id',
            'nom' => 'nom',
            'prenom' => 'prenom',
            'sexe' => 'sexe',
            'genre' => 'sexe',
            'epreuve' => 'epreuve',
            'categorie' => 'epreuve',
            'course' => 'epreuve',
            'datenaissance' => 'date_naissance',
            'datedenaissance' => 'date_naissance',
            'nationalite' => 'nationalite',
            'pays' => 'nationalite',
            'club' => 'club',
            'club/team/association' => 'club',
            'clubteamassociation' => 'club',
            'equipe' => 'club',
            'team' => 'club',
            'association' => 'club',
            // Dossard
            'dossard' => 'bib_number',
            'bib' => 'bib_number',
            'bibnumber' => 'bib_number',
            'numero' => 'bib_number',
            // Acquité
            'acquite' => 'is_paid',
            'paye' => 'is_paid',
            'ispaid' => 'is_paid',
            'paid' => 'is_paid',
        ];

        return $mapping[$normalizedHeader] ?? $normalizedHeader;
    }

    /**
     * Deviner le type de champ selon le nom
     */
    protected function guessFieldType(string $header): string
    {
        $normalized = strtolower($header);

        if (str_contains($normalized, 'email') || str_contains($normalized, 'mail') || str_contains($normalized, 'courriel')) {
            return 'email';
        }

        if (str_contains($normalized, 'tel') || str_contains($normalized, 'phone') || str_contains($normalized, 'mobile') || str_contains($normalized, 'gsm')) {
            return 'tel';
        }

        if (str_contains($normalized, 'date')) {
            return 'date';
        }

        if (str_contains($normalized, 'adresse') || str_contains($normalized, 'address') || str_contains($normalized, 'commentaire') || str_contains($normalized, 'remarque')) {
            return 'textarea';
        }

        if (str_contains($normalized, 'nombre') || str_contains($normalized, 'number') || str_contains($normalized, 'age') || str_contains($normalized, 'taille') || str_contains($normalized, 'poids')) {
            return 'number';
        }

        return 'text';
    }

    /**
     * Créer une inscription avec ses données
     */
    protected function createRegistration(array $data, array $headers, int $lineNumber): void
    {
        // Extraire les données standard
        $standardData = $this->extractStandardData($data, $headers, $lineNumber);

        // Validation des données obligatoires
        if (empty($standardData['nom']) || empty($standardData['prenom'])) {
            throw new Exception("Nom ou prénom manquant à la ligne $lineNumber");
        }

        if (!in_array($standardData['sexe'], ['M', 'F', 'X'])) {
            throw new Exception("Sexe invalide à la ligne $lineNumber (doit être M, F ou X)");
        }

        if (!$standardData['date_naissance']) {
            throw new Exception("Date de naissance invalide à la ligne $lineNumber");
        }

        if (empty($standardData['epreuve'])) {
            throw new Exception("Nom de catégorie manquant à la ligne $lineNumber");
        }

        // Trouver ou créer la catégorie
        $category = $this->findOrCreateCategory($standardData['epreuve']);

        // Créer l'inscription
        $registration = Registration::create([
            'event_id' => $this->event->id,
            'category_id' => $category->id,
            'nom' => $standardData['nom'],
            'prenom' => $standardData['prenom'],
            'sexe' => $standardData['sexe'],
            'date_naissance' => $standardData['date_naissance'],
            'nationalite' => $standardData['nationalite'],
            'club' => $standardData['club'],
            'bib_number' => $standardData['bib_number'],
            'is_paid' => $standardData['is_paid'],
            'status' => $standardData['is_paid'] ? 'confirmed' : 'pending'
        ]);

        // Sauvegarder les données des champs supplémentaires
        foreach ($this->extraFields as $colIndex => $fieldName) {
            $value = $data[$colIndex] ?? null;

            if ($value !== null && $value !== '') {
                // Convertir les dates Excel si nécessaire
                $formFieldId = $this->formFieldsMap[$fieldName];
                $formField = FormField::find($formFieldId);

                if ($formField && $formField->type === 'date' && is_numeric($value)) {
                    $value = $this->parseDate($value);
                }

                RegistrationData::create([
                    'registration_id' => $registration->id,
                    'form_field_id' => $formFieldId,
                    'value' => (string) $value
                ]);
            }
        }
    }

    /**
     * Extraire les données standard depuis une ligne
     */
    protected function extractStandardData(array $data, array $headers, int $lineNumber): array
    {
        $result = [
            'nom' => '',
            'prenom' => '',
            'sexe' => 'X',
            'epreuve' => '',
            'date_naissance' => null,
            'nationalite' => 'BEL',
            'club' => null,
            'bib_number' => null,
            'is_paid' => false
        ];

        foreach ($headers as $colIndex => $header) {
            $normalizedHeader = $this->normalizeHeader($header);

            if (!$this->isStandardColumn($normalizedHeader)) {
                continue;
            }

            $field = $this->mapToStandardField($normalizedHeader);
            $value = $data[$colIndex] ?? null;

            switch ($field) {
                case 'nom':
                    $result['nom'] = strtoupper(trim($value ?? ''));
                    break;

                case 'prenom':
                    $result['prenom'] = ucfirst(strtolower(trim($value ?? '')));
                    break;

                case 'sexe':
                    $sexe = strtoupper(trim($value ?? 'X'));
                    // Gérer les variations
                    if (in_array($sexe, ['H', 'HOMME', 'MALE', 'MASCULIN'])) {
                        $sexe = 'M';
                    } elseif (in_array($sexe, ['FEMME', 'FEMALE', 'FEMININ', 'FÉMININ'])) {
                        $sexe = 'F';
                    } elseif (!in_array($sexe, ['M', 'F', 'X'])) {
                        $sexe = 'X';
                    }
                    $result['sexe'] = $sexe;
                    break;

                case 'epreuve':
                    $result['epreuve'] = trim($value ?? '');
                    break;

                case 'date_naissance':
                    $result['date_naissance'] = $this->parseDate($value);
                    break;

                case 'nationalite':
                    $nat = strtoupper(trim($value ?? 'BEL'));
                    $result['nationalite'] = strlen($nat) === 3 ? $nat : 'BEL';
                    break;

                case 'club':
                    $result['club'] = !empty($value) ? trim($value) : null;
                    break;

                case 'bib_number':
                    $result['bib_number'] = !empty($value) ? (int) $value : null;
                    break;

                case 'is_paid':
                    $result['is_paid'] = $this->parseBoolean($value);
                    break;
            }
        }

        return $result;
    }

    /**
     * Parser une valeur booléenne
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['1', 'true', 'oui', 'yes', 'o', 'y', 'vrai', 'x']);
        }

        return false;
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
            if ($date && $date->format('d-m-Y') === $value) {
                return $date->format('Y-m-d');
            }

            // Essayer Y-m-d (format ISO)
            $date = \DateTime::createFromFormat('Y-m-d', $value);
            if ($date && $date->format('Y-m-d') === $value) {
                return $date->format('Y-m-d');
            }

            // Essayer d/m/Y
            $date = \DateTime::createFromFormat('d/m/Y', $value);
            if ($date && $date->format('d/m/Y') === $value) {
                return $date->format('Y-m-d');
            }

            // Essayer m/d/Y (format US)
            $date = \DateTime::createFromFormat('m/d/Y', $value);
            if ($date && $date->format('m/d/Y') === $value) {
                return $date->format('Y-m-d');
            }

            return null;

        } catch (Exception $e) {
            return null;
        }
    }
}
