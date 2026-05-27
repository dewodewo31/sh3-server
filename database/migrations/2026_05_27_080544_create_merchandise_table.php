<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchandise', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->enum('category', ['clothing', 'accessories', 'collectibles', 'others'])->default('others');
            $table->json('sizes')->nullable(); // S, M, L, XL, XXL
            $table->json('colors')->nullable(); // Red, Blue, Black, White
            $table->boolean('is_active')->default(true);
            $table->integer('sold_count')->default(0);
            $table->timestamps();
            
            $table->index('category');
            $table->index('is_active');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchandise');
    }
};