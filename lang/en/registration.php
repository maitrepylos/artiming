<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Public Registration Form Translations - English
    |--------------------------------------------------------------------------
    */

    // Titles and headers
    'registration_title' => 'Registration',
    'registration_subtitle' => 'Fill out the form below to register',
    'event_closed' => 'Registration is closed',
    'event_closed_message' => 'This event is no longer accepting new registrations.',

    // Form fields
    'fields' => [
        'category' => 'Event / Category',
        'category_placeholder' => 'Select an event',
        'last_name' => 'Last Name',
        'last_name_placeholder' => 'Your last name',
        'first_name' => 'First Name',
        'first_name_placeholder' => 'Your first name',
        'gender' => 'Gender',
        'gender_male' => 'Male',
        'gender_female' => 'Female',
        'gender_other' => 'Other',
        'birth_date' => 'Date of Birth',
        'nationality' => 'Nationality',
        'nationality_placeholder' => 'Select your nationality',
        'club' => 'Club / Team / Association',
        'club_placeholder' => 'Your club name (optional)',
    ],

    // Buttons
    'buttons' => [
        'submit' => 'Submit Registration',
        'submitting' => 'Registering...',
        'back' => 'Back',
        'back_to_events' => 'Back to events',
    ],

    // Success messages
    'success' => [
        'title' => 'Registration Successful!',
        'message' => 'Your registration has been recorded.',
        'confirmation' => 'You will receive a confirmation by email.',
        'summary' => 'Registration Summary',
        'participant' => 'Participant',
        'category' => 'Event',
        'new_registration' => 'New Registration',
    ],

    // Error messages
    'errors' => [
        'title' => 'Validation Error',
        'generic' => 'An error occurred. Please try again.',
        'required' => 'This field is required',
        'invalid_email' => 'Invalid email address',
        'invalid_date' => 'Invalid date',
        'category_full' => 'This category is full',
    ],

    // Validation
    'validation' => [
        'required' => 'The :attribute field is required.',
        'email' => 'The :attribute field must be a valid email address.',
        'date' => 'The :attribute field must be a valid date.',
        'min' => [
            'string' => 'The :attribute field must be at least :min characters.',
        ],
        'max' => [
            'string' => 'The :attribute field may not be greater than :max characters.',
        ],
    ],

    // Nationalities (most common)
    'nationalities' => [
        'BEL' => 'Belgium',
        'FRA' => 'France',
        'NLD' => 'Netherlands',
        'DEU' => 'Germany',
        'GBR' => 'United Kingdom',
        'LUX' => 'Luxembourg',
        'CHE' => 'Switzerland',
        'ESP' => 'Spain',
        'ITA' => 'Italy',
        'PRT' => 'Portugal',
        'USA' => 'United States',
        'CAN' => 'Canada',
        'OTHER' => 'Other',
    ],

    // Additional information
    'info' => [
        'required_fields' => 'Fields marked with * are required',
        'data_privacy' => 'Your personal data is processed in accordance with our privacy policy.',
        'contact' => 'For any questions, please contact the organizer.',
    ],

    // Confirmation and payment
    'payment' => [
        'pending' => 'Payment pending',
        'paid' => 'Paid',
        'status' => 'Payment status',
    ],
];
