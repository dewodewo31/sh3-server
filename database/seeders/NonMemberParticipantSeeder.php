<?php

namespace Database\Seeders;

use App\Models\NonMemberParticipant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NonMemberParticipantSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        NonMemberParticipant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $nonMembers = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'phone' => '081234567893',
                'gender' => 'male',
                'birthdate' => '1988-11-10',
                'blood_type' => 'B',
                'emergency_contact' => 'Istri Ahmad',
                'emergency_phone' => '081234567897',
                'allergy_history' => 'Telur',
                'identity_number' => '3273111088000003',
                'status' => 'active',
                'notes' => 'Non-member participant'
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'dewi@example.com',
                'phone' => '081234567894',
                'gender' => 'female',
                'birthdate' => '1995-03-25',
                'blood_type' => 'AB',
                'emergency_contact' => 'Suami Dewi',
                'emergency_phone' => '081234567896',
                'allergy_history' => 'Obat Penisilin',
                'identity_number' => '3274250395000004',
                'status' => 'active',
                'notes' => 'Non-member participant'
            ],
        ];

        foreach ($nonMembers as $nonMember) {
            NonMemberParticipant::create($nonMember);
        }
        
        $this->command->info('Non-Member Participants seeded successfully!');
        $this->command->info('Total non-member participants: ' . NonMemberParticipant::count());
    }
}