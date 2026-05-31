<?php
// database/migrations/xxxx_xx_xx_add_tier_to_event_sponsor_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTierToEventSponsorTable extends Migration
{
    public function up()
    {
        Schema::table('event_sponsor', function (Blueprint $table) {
            $table->string('tier')->default('partner')->after('sponsor_id');
            $table->decimal('contribution_amount', 12, 2)->nullable()->change();
            $table->text('benefits')->nullable()->change();
            $table->integer('sort_order')->default(0)->after('benefits');
        });
    }

    public function down()
    {
        Schema::table('event_sponsor', function (Blueprint $table) {
            $table->dropColumn(['tier', 'sort_order']);
            $table->decimal('contribution_amount', 12, 2)->nullable(false)->change();
            $table->text('benefits')->nullable(false)->change();
        });
    }
}