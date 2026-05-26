<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_member_participants', function (Blueprint $table) {
            $table->id();
            
            // Identitas Dasar
            $table->string('hash_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate');
            
            // Field Tambahan untuk Non-Member
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('allergy_history')->nullable();
            $table->string('identity_number')->nullable(); // KTP/Passport
            $table->string('identity_photo')->nullable(); // Foto KTP/Passport
            
            // Status dan Aktivitas
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // Catatan
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('hash_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_member_participants');
    }
};