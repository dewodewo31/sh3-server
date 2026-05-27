<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing non-member hash_id to NM01, NM02, etc.
        $nonMembers = DB::table('participants')
            ->where('participant_type', 'non_member')
            ->orderBy('id')
            ->get();
        
        $counter = 1;
        foreach ($nonMembers as $nonMember) {
            $newHashId = 'NM' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            DB::table('participants')
                ->where('id', $nonMember->id)
                ->update(['hash_id' => $newHashId]);
            $counter++;
        }
        
        // Hapus unique constraint jika ada (agar NM01, NM02 bisa masuk)
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique('participants_hash_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unique('hash_id');
        });
    }
};