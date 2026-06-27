<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('organization_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('level'); // 1, 2, 3, dst
            $table->string('level_name')->nullable(); // Contoh: "Pengurus Inti", "Bidang", "Seksi"
            $table->string('position_name');
            $table->string('position_code')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('organization_hierarchies')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performance
            $table->index(['year', 'level']);
            $table->index(['year', 'parent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_hierarchies');
    }
};