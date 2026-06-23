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
        // ==================== ADMIN ROLES ====================
        
        // 1. ADMIN FULL ACCESS - Full akses semua menu
        User::create([
            'name' => 'Admin Full Access',
            'email' => 'admin.full@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'admin_full_access',
        ]);

        // 2. ADMIN LAMAN - Bisa mengelola semua menu website
        User::create([
            'name' => 'Admin Laman',
            'email' => 'admin.laman@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'admin_laman',
        ]);

        // 3. ADMIN MEMBER - Hanya bisa mengelola member/participant
        User::create([
            'name' => 'Admin Member',
            'email' => 'admin.member@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'admin_member',
        ]);

        // 4. ADMIN BNH - Bisa mengelola gallery dan konten BNH
        User::create([
            'name' => 'Admin BNH',
            'email' => 'admin.bnh@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'admin_bnh',
        ]);

        // 5. ORGANIZER - Bisa mengelola event dan orders
        User::create([
            'name' => 'Organizer',
            'email' => 'organizer@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
        ]);

        // 6. BENDAHARA - Bisa mengelola dashboard, orders, dan payments
        User::create([
            'name' => 'Bendahara',
            'email' => 'bendahara@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);

        // 7. SPONSOR - Hanya bisa mengelola menu sponsor
        User::create([
            'name' => 'Sponsor',
            'email' => 'sponsor@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'sponsor',
        ]);

        // 8. MERCHANDISE - Hanya bisa mengelola merchandise
        User::create([
            'name' => 'Merchandise',
            'email' => 'merchandise@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'merchandise',
        ]);

        // 9. PARTICIPANT (Default role)
        User::create([
            'name' => 'Participant',
            'email' => 'participant@sh3.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
        ]);

        // ==================== CALL SEEDERS ====================
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