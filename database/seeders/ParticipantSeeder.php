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
        
        // ==================== MEMBER PARTICIPANTS ====================
        $members = [
            [
                'hash_id' => '0001',
                'participant_type' => 'member',
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'phone' => '081234567891',
                'gender' => 'male',
                'birthdate' => '1990-05-15',
                'blood_type' => 'O',
                'emergency_contact' => 'Ibu Budi - Siti Aminah',
                'emergency_phone' => '081234567899',
                'allergy_history' => 'Debu, Udang',
                'identity_number' => '3175012305900001',
                'status' => 'active',
                'notes' => 'Member sejak 2024, frequent buyer'
            ],
            [
                'hash_id' => '0002',
                'participant_type' => 'member',
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@example.com',
                'phone' => '081234567892',
                'gender' => 'female',
                'birthdate' => '1992-08-20',
                'blood_type' => 'A',
                'emergency_contact' => 'Ayah Siti - Slamet Riyadi',
                'emergency_phone' => '081234567898',
                'allergy_history' => 'Kacang, Telur',
                'identity_number' => '3175082092000002',
                'status' => 'active',
                'notes' => 'Premium member, VIP'
            ],
            [
                'hash_id' => '0003',
                'participant_type' => 'member',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'phone' => '081234567893',
                'gender' => 'male',
                'birthdate' => '1988-11-10',
                'blood_type' => 'B',
                'emergency_contact' => 'Istri Ahmad - Dewi Kartika',
                'emergency_phone' => '081234567897',
                'allergy_history' => 'Telur, Makanan laut',
                'identity_number' => '3273111088000003',
                'status' => 'active',
                'notes' => 'Early bird enthusiast'
            ],
            [
                'hash_id' => '0004',
                'participant_type' => 'member',
                'name' => 'Dewi Kartika',
                'email' => 'dewi.kartika@example.com',
                'phone' => '081234567894',
                'gender' => 'female',
                'birthdate' => '1995-03-25',
                'blood_type' => 'AB',
                'emergency_contact' => 'Suami Dewi - Ahmad Fauzi',
                'emergency_phone' => '081234567896',
                'allergy_history' => 'Obat Penisilin, Ibuprofen',
                'identity_number' => '3274250395000004',
                'status' => 'active',
                'notes' => 'New member, active participant'
            ],
            [
                'hash_id' => '0005',
                'participant_type' => 'member',
                'name' => 'Rizki Pratama',
                'email' => 'rizki.pratama@example.com',
                'phone' => '081234567895',
                'gender' => 'male',
                'birthdate' => '1991-07-08',
                'blood_type' => 'O',
                'emergency_contact' => 'Ibu Rizki - Sri Mulyani',
                'emergency_phone' => '081234567895',
                'allergy_history' => 'Debu, Kucing',
                'identity_number' => '3274070891000005',
                'status' => 'active',
                'notes' => 'Runner, join multiple events'
            ],
        ];

        // ==================== NON-MEMBER PARTICIPANTS (isi hash_id manual) ====================
        $nonMembers = [
            [
                'hash_id' => 'NM01',
                'participant_type' => 'non_member',
                'name' => 'Tono Suprapto',
                'email' => 'tono.suprapto@example.com',
                'phone' => '081234567901',
                'gender' => 'male',
                'birthdate' => '1990-02-14',
                'blood_type' => 'B',
                'emergency_contact' => 'Istri Tono - Yuli',
                'emergency_phone' => '081234567889',
                'allergy_history' => 'Udang',
                'identity_number' => '3174021490000011',
                'status' => 'active',
                'notes' => 'First time participant'
            ],
            [
                'hash_id' => 'NM02',
                'participant_type' => 'non_member',
                'name' => 'Ani Lestari',
                'email' => 'ani.lestari@example.com',
                'phone' => '081234567902',
                'gender' => 'female',
                'birthdate' => '1995-07-22',
                'blood_type' => 'O',
                'emergency_contact' => 'Ayah Ani - Sugeng',
                'emergency_phone' => '081234567888',
                'allergy_history' => 'Kacang',
                'identity_number' => '3274072295000012',
                'status' => 'active',
                'notes' => 'Try out event'
            ],
            [
                'hash_id' => 'NM03',
                'participant_type' => 'non_member',
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@example.com',
                'phone' => '081234567903',
                'gender' => 'male',
                'birthdate' => '1985-06-10',
                'blood_type' => 'A',
                'emergency_contact' => 'Istri Joko - Iriana',
                'emergency_phone' => '081234567887',
                'allergy_history' => 'Tidak ada',
                'identity_number' => '3174061085000013',
                'status' => 'active',
                'notes' => 'Casual participant'
            ],
        ];

        // ==================== CREATE MEMBERS ====================
        $this->command->info('Creating member participants...');
        foreach ($members as $member) {
            $participant = Participant::create($member);
            $this->command->info("  - Created member: {$participant->name} (Hash ID: {$participant->hash_id})");
        }

        // ==================== CREATE NON-MEMBERS ====================
        $this->command->info('Creating non-member participants...');
        foreach ($nonMembers as $nonMember) {
            $participant = Participant::create($nonMember);
            $this->command->info("  - Created non-member: {$participant->name} (Hash ID: {$participant->hash_id})");
        }

        // ==================== SUMMARY ====================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('PARTICIPANT SEEDER COMPLETED');
        $this->command->info('========================================');
        $this->command->info('Total participants: ' . Participant::count());
        $this->command->info('');
        $this->command->info('Member participants: ' . Participant::where('participant_type', 'member')->count());
        $this->command->info('Non-member participants: ' . Participant::where('participant_type', 'non_member')->count());
        $this->command->info('');
        $this->command->info('Sample Member Hash IDs:');
        $samples = Participant::where('participant_type', 'member')->take(5)->get();
        foreach ($samples as $sample) {
            $this->command->info("  - {$sample->name}: {$sample->hash_id}");
        }
        $this->command->info('');
        $this->command->info('Non-Member Hash IDs:');
        $nonMemberSamples = Participant::where('participant_type', 'non_member')->get();
        foreach ($nonMemberSamples as $sample) {
            $this->command->info("  - {$sample->name}: {$sample->hash_id}");
        }
    }
}