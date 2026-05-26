<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Tambahan field untuk member participant
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable()->after('gender');
            $table->string('emergency_contact')->nullable()->after('blood_type');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            $table->text('allergy_history')->nullable()->after('emergency_phone');
            $table->string('identity_number')->nullable()->after('allergy_history'); // KTP/Passport
            $table->string('identity_photo')->nullable()->after('identity_number'); // Foto KTP/Passport
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'blood_type',
                'emergency_contact',
                'emergency_phone',
                'allergy_history',
                'identity_number',
                'identity_photo'
            ]);
        });
    }
};