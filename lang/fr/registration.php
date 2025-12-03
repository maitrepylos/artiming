<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Traductions du formulaire d'inscription public - Français
    |--------------------------------------------------------------------------
    */

    // Titres et en-têtes
    'registration_title' => 'Inscription',
    'registration_subtitle' => 'Remplissez le formulaire ci-dessous pour vous inscrire',
    'event_closed' => 'Les inscriptions sont fermées',
    'event_closed_message' => 'Cet événement n\'accepte plus de nouvelles inscriptions.',

    // Champs du formulaire
    'fields' => [
        'category' => 'Épreuve / Catégorie',
        'category_placeholder' => 'Sélectionnez une épreuve',
        'last_name' => 'Nom',
        'last_name_placeholder' => 'Votre nom de famille',
        'first_name' => 'Prénom',
        'first_name_placeholder' => 'Votre prénom',
        'gender' => 'Genre',
        'gender_male' => 'Homme',
        'gender_female' => 'Femme',
        'gender_other' => 'Autre',
        'birth_date' => 'Date de naissance',
        'nationality' => 'Nationalité',
        'nationality_placeholder' => 'Sélectionnez votre nationalité',
        'club' => 'Club / Team / Association',
        'club_placeholder' => 'Nom de votre club (optionnel)',
    ],

    // Boutons
    'buttons' => [
        'submit' => 'Valider l\'inscription',
        'submitting' => 'Inscription en cours...',
        'back' => 'Retour',
        'back_to_events' => 'Retour aux événements',
    ],

    // Messages de succès
    'success' => [
        'title' => 'Inscription réussie !',
        'message' => 'Votre inscription a bien été enregistrée.',
        'confirmation' => 'Vous recevrez une confirmation par email.',
        'summary' => 'Récapitulatif de votre inscription',
        'participant' => 'Participant',
        'category' => 'Épreuve',
        'new_registration' => 'Nouvelle inscription',
    ],

    // Messages d'erreur
    'errors' => [
        'title' => 'Erreur de validation',
        'generic' => 'Une erreur est survenue. Veuillez réessayer.',
        'required' => 'Ce champ est obligatoire',
        'invalid_email' => 'Adresse email invalide',
        'invalid_date' => 'Date invalide',
        'category_full' => 'Cette catégorie est complète',
    ],

    // Validation
    'validation' => [
        'required' => 'Le champ :attribute est obligatoire.',
        'email' => 'Le champ :attribute doit être une adresse email valide.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'min' => [
            'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ],
        'max' => [
            'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        ],
    ],

    // Nationalités (les plus courantes)
    'nationalities' => [
        'BEL' => 'Belgique',
        'FRA' => 'France',
        'NLD' => 'Pays-Bas',
        'DEU' => 'Allemagne',
        'GBR' => 'Royaume-Uni',
        'LUX' => 'Luxembourg',
        'CHE' => 'Suisse',
        'ESP' => 'Espagne',
        'ITA' => 'Italie',
        'PRT' => 'Portugal',
        'USA' => 'États-Unis',
        'CAN' => 'Canada',
        'OTHER' => 'Autre',
    ],

    // Informations complémentaires
    'info' => [
        'required_fields' => 'Les champs marqués d\'un * sont obligatoires',
        'data_privacy' => 'Vos données personnelles sont traitées conformément à notre politique de confidentialité.',
        'contact' => 'Pour toute question, contactez l\'organisateur.',
    ],

    // Confirmation et paiement
    'payment' => [
        'pending' => 'En attente de paiement',
        'paid' => 'Payé',
        'status' => 'Statut du paiement',
    ],
];
