<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Öffentliches Anmeldeformular Übersetzungen - Deutsch
    |--------------------------------------------------------------------------
    */

    // Titel und Überschriften
    'registration_title' => 'Anmeldung',
    'registration_subtitle' => 'Füllen Sie das untenstehende Formular aus, um sich anzumelden',
    'event_closed' => 'Anmeldungen sind geschlossen',
    'event_closed_message' => 'Diese Veranstaltung nimmt keine neuen Anmeldungen mehr an.',

    // Formularfelder
    'fields' => [
        'category' => 'Veranstaltung / Kategorie',
        'category_placeholder' => 'Wählen Sie eine Veranstaltung',
        'last_name' => 'Nachname',
        'last_name_placeholder' => 'Ihr Nachname',
        'first_name' => 'Vorname',
        'first_name_placeholder' => 'Ihr Vorname',
        'gender' => 'Geschlecht',
        'gender_male' => 'Männlich',
        'gender_female' => 'Weiblich',
        'gender_other' => 'Andere',
        'birth_date' => 'Geburtsdatum',
        'nationality' => 'Nationalität',
        'nationality_placeholder' => 'Wählen Sie Ihre Nationalität',
        'club' => 'Verein / Team / Organisation',
        'club_placeholder' => 'Name Ihres Vereins (optional)',
    ],

    // Schaltflächen
    'buttons' => [
        'submit' => 'Anmeldung absenden',
        'submitting' => 'Wird angemeldet...',
        'back' => 'Zurück',
        'back_to_events' => 'Zurück zu den Veranstaltungen',
    ],

    // Erfolgsmeldungen
    'success' => [
        'title' => 'Anmeldung erfolgreich!',
        'message' => 'Ihre Anmeldung wurde registriert.',
        'confirmation' => 'Sie erhalten eine Bestätigung per E-Mail.',
        'summary' => 'Zusammenfassung Ihrer Anmeldung',
        'participant' => 'Teilnehmer',
        'category' => 'Veranstaltung',
        'new_registration' => 'Neue Anmeldung',
    ],

    // Fehlermeldungen
    'errors' => [
        'title' => 'Validierungsfehler',
        'generic' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
        'required' => 'Dieses Feld ist erforderlich',
        'invalid_email' => 'Ungültige E-Mail-Adresse',
        'invalid_date' => 'Ungültiges Datum',
        'category_full' => 'Diese Kategorie ist voll',
    ],

    // Validierung
    'validation' => [
        'required' => 'Das Feld :attribute ist erforderlich.',
        'email' => 'Das Feld :attribute muss eine gültige E-Mail-Adresse sein.',
        'date' => 'Das Feld :attribute muss ein gültiges Datum sein.',
        'min' => [
            'string' => 'Das Feld :attribute muss mindestens :min Zeichen enthalten.',
        ],
        'max' => [
            'string' => 'Das Feld :attribute darf nicht mehr als :max Zeichen enthalten.',
        ],
    ],

    // Nationalitäten (häufigste)
    'nationalities' => [
        'BEL' => 'Belgien',
        'FRA' => 'Frankreich',
        'NLD' => 'Niederlande',
        'DEU' => 'Deutschland',
        'GBR' => 'Vereinigtes Königreich',
        'LUX' => 'Luxemburg',
        'CHE' => 'Schweiz',
        'ESP' => 'Spanien',
        'ITA' => 'Italien',
        'PRT' => 'Portugal',
        'USA' => 'Vereinigte Staaten',
        'CAN' => 'Kanada',
        'OTHER' => 'Andere',
    ],

    // Zusätzliche Informationen
    'info' => [
        'required_fields' => 'Mit * markierte Felder sind Pflichtfelder',
        'data_privacy' => 'Ihre persönlichen Daten werden gemäß unserer Datenschutzrichtlinie verarbeitet.',
        'contact' => 'Bei Fragen wenden Sie sich bitte an den Veranstalter.',
    ],

    // Bestätigung und Zahlung
    'payment' => [
        'pending' => 'Zahlung ausstehend',
        'paid' => 'Bezahlt',
        'status' => 'Zahlungsstatus',
    ],
];
