<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('notes');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->decimal('paid_amount', 12, 2)->nullable()->after('payment_proof_uploaded_at');
            $table->string('payment_method')->nullable()->after('paid_amount');
            $table->timestamp('verified_at')->nullable()->after('payment_method');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('verification_notes')->nullable()->after('verified_by');
        });
    }

    public function down()
    {
        Schema::table('merchandise_orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_proof',
                'payment_proof_uploaded_at',
                'paid_amount',
                'payment_method',
                'verified_at',
                'verified_by',
                'verification_notes'
            ]);
        });
    }
};