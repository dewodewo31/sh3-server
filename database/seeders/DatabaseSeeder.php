<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ADMIN
        User::create([
            'name' => 'Admin',
            'email' => 'admin@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ORGANIZER
        User::create([
            'name' => 'Organizer',
            'email' => 'organizer@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
        ]);

        $this->call([ 
            CategorySeeder::class,     
            EventSeeder::class,         
            ParticipantSeeder::class,    
            OrderSeeder::class,          
            PaymentSeeder::class,         
            MerchandiseSeeder::class,         
            MerchandiseOrderSeeder::class,         
        ]);
    }
}
