<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, update existing hash_id from SH3ID000001 format to 0001
        $participants = DB::table('participants')->get();
        
        foreach ($participants as $participant) {
            if (str_starts_with($participant->hash_id, 'SH3ID')) {
                // Extract number from SH3ID000001 -> 1
                $number = (int) substr($participant->hash_id, 5);
                // Convert to 4 digits -> 0001
                $newHashId = str_pad($number, 4, '0', STR_PAD_LEFT);
            } else {
                // Already just numbers, ensure 4 digits
                $number = (int) $participant->hash_id;
                $newHashId = str_pad($number, 4, '0', STR_PAD_LEFT);
            }
            
            DB::table('participants')
                ->where('id', $participant->id)
                ->update(['hash_id' => $newHashId]);
        }
    }

    public function down(): void
    {
        // Rollback to add prefix back if needed
        $participants = DB::table('participants')->get();
        
        foreach ($participants as $participant) {
            $number = (int) $participant->hash_id;
            $newHashId = 'SH3ID' . str_pad($number, 6, '0', STR_PAD_LEFT);
            
            DB::table('participants')
                ->where('id', $participant->id)
                ->update(['hash_id' => $newHashId]);
        }
    }
};