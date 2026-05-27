<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_merchandise', function (Blueprint $table) {
            $table->id();
            
            // Pastikan foreign key merujuk ke tabel yang benar
            $table->foreignId('event_id')
                ->constrained('events')  // explicit table name
                ->onDelete('cascade');
            
            $table->foreignId('merchandise_id')
                ->constrained('merchandise')  // perhatikan: 'merchandise' bukan 'merchandises'
                ->onDelete('cascade');
            
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('event_stock')->nullable();
            $table->boolean('is_available')->default(true);
            
            $table->timestamps();
            
            // Optional: add unique constraint to prevent duplicate
            $table->unique(['event_id', 'merchandise_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_merchandise');
    }
};