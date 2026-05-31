<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->integer('warning_count')->default(0);
            $table->integer('current_warning_level')->default(0);
            $table->timestamp('suspended_until')->nullable();
            $table->boolean('is_suspended')->default(false);
            $table->text('suspension_reason')->nullable();
        });
    }

    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'warning_count',
                'current_warning_level',
                'suspended_until',
                'is_suspended',
                'suspension_reason'
            ]);
        });
    }
};