<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->integer('billing_id')->nullable()->after('to_ref_no');
            $table->string('size')->nullable()->after('type'); // e.g., "20", "40"
        });
    }

    public function down()
    {
        Schema::table('transport_orders', function (Blueprint $table) {
            $table->dropColumn(['billing_id', 'size']);
        });
    }
};