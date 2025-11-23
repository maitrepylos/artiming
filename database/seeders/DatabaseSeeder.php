<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    // Créer un utilisateur admin
    User::create([
        'name' => 'Admin',
        'email' => 'admin@formarthur.com',
        'password' => Hash::make('password'),
    ]);

    // Créer un événement exemple (comme ton Ultra ESA)
    $event = Event::create([
        'name' => 'Ultra ESA Namur 2025',
        'slug' => 'ultra-esa-namur-2025',
        'description' => 'Course ultra trail à Namur',
        'event_date' => now()->addMonths(2),
        'is_active' => true,
    ]);

    // Créer les catégories (comme dans categories.json)
    $categories = [
        [
            'name' => 'Course de ESA Namur',
            'code' => 'esa',
            'price' => 25.00,
            'max_participants' => 100,
            'order' => 1
        ],
        [
            'name' => 'Ultra 3000',
            'code' => 'ultra_3000',
            'price' => 35.00,
            'max_participants' => 50,
            'order' => 2
        ],
        [
            'name' => 'Ultra 3000 Duo',
            'code' => 'ultra_3000_duo',
            'price' => 60.00,
            'max_participants' => 25,
            'order' => 3
        ],
        [
            'name' => 'Ultra 4000',
            'code' => 'ultra_4000',
            'price' => 45.00,
            'max_participants' => 40,
            'order' => 4
        ],
        [
            'name' => 'Ultra 6000',
            'code' => 'ultra_6000',
            'price' => 55.00,
            'max_participants' => 30,
            'order' => 5
        ],
    ];

    foreach ($categories as $categoryData) {
        $event->categories()->create($categoryData);
    }

    // Créer quelques inscriptions de test
    $event->registrations()->create([
        'category_id' => $event->categories->first()->id,
        'nom' => 'DUPONT',
        'prenom' => 'Jean',
        'sexe' => 'M',
        'date_naissance' => '1990-05-15',
        'nationalite' => 'BEL',
        'club' => 'Running Club Namur',
        'bib_number' => 101,
        'is_paid' => true,
        'status' => 'confirmed'
    ]);

    $event->registrations()->create([
        'category_id' => $event->categories->skip(1)->first()->id,
        'nom' => 'MARTIN',
        'prenom' => 'Sophie',
        'sexe' => 'F',
        'date_naissance' => '1985-08-22',
        'nationalite' => 'FRA',
        'club' => null,
        'bib_number' => null,
        'is_paid' => false,
        'status' => 'pending'
    ]);

    $this->command->info('✅ Base de données peuplée avec succès !');
    $this->command->info('📧 Email admin: admin@formarthur.com');
    $this->command->info('🔐 Mot de passe: password');
}
}
