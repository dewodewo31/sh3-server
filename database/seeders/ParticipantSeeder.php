<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Participant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $participants = [
            [
                'hash_id' => '0001', // ← Tambahkan hash_id manual
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567891',
                'gender' => 'male',
                'birthdate' => '1990-05-15',
                'status' => 'active',
                'notes' => 'Regular participant'
            ],
            [
                'hash_id' => '0002',
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'phone' => '081234567892',
                'gender' => 'female',
                'birthdate' => '1992-08-20',
                'status' => 'active',
                'notes' => 'Premium member'
            ],
            [
                'hash_id' => '0003',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'phone' => '081234567893',
                'gender' => 'male',
                'birthdate' => '1988-11-10',
                'status' => 'active',
                'notes' => 'Early bird enthusiast'
            ],
            [
                'hash_id' => '0004',
                'name' => 'Dewi Kartika',
                'email' => 'dewi@example.com',
                'phone' => '081234567894',
                'gender' => 'female',
                'birthdate' => '1995-03-25',
                'status' => 'active',
                'notes' => 'New participant'
            ],
        ];

        foreach ($participants as $participant) {
            Participant::create($participant);
        }
        
        $this->command->info('Participants seeded successfully!');
        $this->command->info('Total participants: ' . Participant::count());
    }
}
