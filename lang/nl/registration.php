<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Publieke Registratieformulier Vertalingen - Nederlands
    |--------------------------------------------------------------------------
    */

    // Titels en headers
    'registration_title' => 'Inschrijving',
    'registration_subtitle' => 'Vul het onderstaande formulier in om u in te schrijven',
    'event_closed' => 'Inschrijvingen zijn gesloten',
    'event_closed_message' => 'Dit evenement accepteert geen nieuwe inschrijvingen meer.',

    // Formuliervelden
    'fields' => [
        'category' => 'Evenement / Categorie',
        'category_placeholder' => 'Selecteer een evenement',
        'last_name' => 'Achternaam',
        'last_name_placeholder' => 'Uw achternaam',
        'first_name' => 'Voornaam',
        'first_name_placeholder' => 'Uw voornaam',
        'gender' => 'Geslacht',
        'gender_male' => 'Man',
        'gender_female' => 'Vrouw',
        'gender_other' => 'Anders',
        'birth_date' => 'Geboortedatum',
        'nationality' => 'Nationaliteit',
        'nationality_placeholder' => 'Selecteer uw nationaliteit',
        'club' => 'Club / Team / Vereniging',
        'club_placeholder' => 'Naam van uw club (optioneel)',
    ],

    // Knoppen
    'buttons' => [
        'submit' => 'Inschrijving bevestigen',
        'submitting' => 'Bezig met inschrijven...',
        'back' => 'Terug',
        'back_to_events' => 'Terug naar evenementen',
    ],

    // Succesberichten
    'success' => [
        'title' => 'Inschrijving geslaagd!',
        'message' => 'Uw inschrijving is geregistreerd.',
        'confirmation' => 'U ontvangt een bevestiging per e-mail.',
        'summary' => 'Samenvatting van uw inschrijving',
        'participant' => 'Deelnemer',
        'category' => 'Evenement',
        'new_registration' => 'Nieuwe inschrijving',
    ],

    // Foutmeldingen
    'errors' => [
        'title' => 'Validatiefout',
        'generic' => 'Er is een fout opgetreden. Probeer het opnieuw.',
        'required' => 'Dit veld is verplicht',
        'invalid_email' => 'Ongeldig e-mailadres',
        'invalid_date' => 'Ongeldige datum',
        'category_full' => 'Deze categorie is vol',
    ],

    // Validatie
    'validation' => [
        'required' => 'Het veld :attribute is verplicht.',
        'email' => 'Het veld :attribute moet een geldig e-mailadres zijn.',
        'date' => 'Het veld :attribute moet een geldige datum zijn.',
        'min' => [
            'string' => 'Het veld :attribute moet minimaal :min tekens bevatten.',
        ],
        'max' => [
            'string' => 'Het veld :attribute mag niet meer dan :max tekens bevatten.',
        ],
    ],

    // Nationaliteiten (meest voorkomende)
    'nationalities' => [
        'BEL' => 'België',
        'FRA' => 'Frankrijk',
        'NLD' => 'Nederland',
        'DEU' => 'Duitsland',
        'GBR' => 'Verenigd Koninkrijk',
        'LUX' => 'Luxemburg',
        'CHE' => 'Zwitserland',
        'ESP' => 'Spanje',
        'ITA' => 'Italië',
        'PRT' => 'Portugal',
        'USA' => 'Verenigde Staten',
        'CAN' => 'Canada',
        'OTHER' => 'Anders',
    ],

    // Aanvullende informatie
    'info' => [
        'required_fields' => 'Velden gemarkeerd met * zijn verplicht',
        'data_privacy' => 'Uw persoonlijke gegevens worden verwerkt in overeenstemming met ons privacybeleid.',
        'contact' => 'Voor vragen kunt u contact opnemen met de organisator.',
    ],

    // Bevestiging en betaling
    'payment' => [
        'pending' => 'Betaling in afwachting',
        'paid' => 'Betaald',
        'status' => 'Betalingsstatus',
    ],
];
