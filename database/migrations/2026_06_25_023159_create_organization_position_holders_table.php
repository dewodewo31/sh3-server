<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('organization_position_holders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hierarchy_id')->constrained('organization_hierarchies')->cascadeOnDelete();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('member_since')->nullable(); // Tahun menjadi anggota
            $table->text('bio')->nullable();
            $table->text('achievements')->nullable();
            $table->json('social_media')->nullable();
            $table->integer('period_start')->nullable();
            $table->integer('period_end')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_position_holders');
    }
};