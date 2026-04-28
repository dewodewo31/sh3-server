<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Bersihkan data lama
        Participant::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $participants = [
            // Active Participants
            [
                'hash_id' => 'SH3ID000001',
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567891',
                'gender' => 'male',
                'birthdate' => '1990-05-15',
                'status' => 'active',
                'notes' => 'Regular participant, always attend events'
            ],
            [
                'hash_id' => 'SH3ID000002',
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'phone' => '081234567892',
                'gender' => 'female',
                'birthdate' => '1992-08-20',
                'status' => 'active',
                'notes' => 'Premium member'
            ],
            [
                'hash_id' => 'SH3ID000003',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'phone' => '081234567893',
                'gender' => 'male',
                'birthdate' => '1988-11-10',
                'status' => 'active',
                'notes' => 'Early bird enthusiast'
            ],
            [
                'hash_id' => 'SH3ID000004',
                'name' => 'Dewi Kartika',
                'email' => 'dewi@example.com',
                'phone' => '081234567894',
                'gender' => 'female',
                'birthdate' => '1995-03-25',
                'status' => 'active',
                'notes' => 'New participant'
            ],
            [
                'hash_id' => 'SH3ID000005',
                'name' => 'Rizki Pratama',
                'email' => 'rizki@example.com',
                'phone' => '081234567895',
                'gender' => 'male',
                'birthdate' => '1991-07-08',
                'status' => 'active',
                'notes' => 'Frequent buyer'
            ],
            [
                'hash_id' => 'SH3ID000006',
                'name' => 'Maya Sari',
                'email' => 'maya@example.com',
                'phone' => '081234567896',
                'gender' => 'female',
                'birthdate' => '1993-12-01',
                'status' => 'active',
                'notes' => 'VIP member'
            ],
            [
                'hash_id' => 'SH3ID000007',
                'name' => 'Andi Wijaya',
                'email' => 'andi@example.com',
                'phone' => '081234567897',
                'gender' => 'male',
                'birthdate' => '1989-09-18',
                'status' => 'active',
                'notes' => 'Community leader'
            ],
            [
                'hash_id' => 'SH3ID000008',
                'name' => 'Linda Susanti',
                'email' => 'linda@example.com',
                'phone' => '081234567898',
                'gender' => 'female',
                'birthdate' => '1994-06-12',
                'status' => 'active',
                'notes' => 'Social media influencer'
            ],
            [
                'hash_id' => 'SH3ID000009',
                'name' => 'Hendra Gunawan',
                'email' => 'hendra@example.com',
                'phone' => '081234567899',
                'gender' => 'male',
                'birthdate' => '1987-04-05',
                'status' => 'active',
                'notes' => 'Corporate participant'
            ],
            [
                'hash_id' => 'SH3ID000010',
                'name' => 'Rina Melati',
                'email' => 'rina@example.com',
                'phone' => '081234567900',
                'gender' => 'female',
                'birthdate' => '1996-10-30',
                'status' => 'active',
                'notes' => 'Student participant'
            ],
            // Inactive Participants
            [
                'hash_id' => 'SH3ID000011',
                'name' => 'Tono Suprapto',
                'email' => 'tono@example.com',
                'phone' => '081234567901',
                'gender' => 'male',
                'birthdate' => '1990-02-14',
                'status' => 'inactive',
                'notes' => 'Haven\'t attended for months'
            ],
            [
                'hash_id' => 'SH3ID000012',
                'name' => 'Ani Lestari',
                'email' => 'ani@example.com',
                'phone' => '081234567902',
                'gender' => 'female',
                'birthdate' => '1995-07-22',
                'status' => 'inactive',
                'notes' => 'Moved to another city'
            ],
        ];

        foreach ($participants as $participant) {
            Participant::create($participant);
        }
        
        // Update last login for some participants
        $activeParticipants = Participant::where('status', 'active')->get();
        foreach ($activeParticipants as $index => $participant) {
            if ($index % 2 == 0) {
                $participant->update([
                    'last_login_at' => now()->subDays(rand(1, 30)),
                    'last_login_ip' => '192.168.1.' . rand(1, 255)
                ]);
            }
        }
        
        $this->command->info('Participants seeded successfully!');
        $this->command->info('Total participants: ' . Participant::count());
        $this->command->info('Active: ' . Participant::where('status', 'active')->count());
        $this->command->info('Inactive: ' . Participant::where('status', 'inactive')->count());
    }
}