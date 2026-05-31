<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('participant_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('warning_level')->default(1); // 1, 2, 3
            $table->string('reason');
            $table->text('description')->nullable();
            $table->timestamp('issued_at');
            $table->timestamp('expires_at')->nullable(); // Untuk warning yang expired
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('participant_warnings');
    }
};