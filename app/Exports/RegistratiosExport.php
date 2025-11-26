<?php

namespace App\Exports;

use App\Models\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationsExport
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Télécharger le fichier Excel
     */
    public function download(): StreamedResponse
    {
        $spreadsheet = $this->generate();
        $filename = $this->event->slug . '_participants_' . date('Y-m-d_H-i-s') . '.xlsx';

        return new StreamedResponse(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Générer le fichier Excel
     */
    protected function generate(): Spreadsheet
    {
        // Récupérer toutes les inscriptions
        $registrations = $this->event->registrations()
            ->with('category')
            ->orderByRaw('bib_number IS NULL')
            ->orderByRaw('CAST(bib_number AS INTEGER) DESC')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        // Créer le spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Participants');

        // En-têtes
        $headers = [
            'ID',
            'Nom',
            'Prénom',
            'Sexe',
            'Épreuve',
            'Date de naissance',
            'Nationalité',
            'Club',
            'Dossard',
            'Acquité'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style des en-têtes
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3498db']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Données
        $row = 2;
        foreach ($registrations as $registration) {
            // Formater la date
            $dateFormatted = $registration->date_naissance
                ? $registration->date_naissance->format('d-m-Y')
                : '';

            $rowData = [
                $registration->id,
                $registration->nom,
                $registration->prenom,
                $registration->sexe,
                $registration->category->name,
                $dateFormatted,
                $registration->nationalite,
                $registration->club ?? '',
                $registration->bib_number ?? '',
                $registration->is_paid ? 1 : 0
            ];

            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        // Auto-ajuster la largeur des colonnes
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Style des données
        $dataRange = 'A2:J' . ($row - 1);
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT
            ]
        ];
        $sheet->getStyle($dataRange)->applyFromArray($dataStyle);

        return $spreadsheet;
    }
}
