<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys - hanya participant_id
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            
            // Order identifiers
            $table->string('invoice_number')->unique();
            $table->string('ticket_code')->unique();
            
            // Order details
            $table->enum('status', ['pending', 'paid', 'free', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            
            // Indexes untuk performa query
            $table->index('participant_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('invoice_number');
            $table->index('ticket_code');
            
            // Unique constraint: satu participant hanya bisa order 1x untuk event yang sama
            $table->unique(['participant_id', 'event_id']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};