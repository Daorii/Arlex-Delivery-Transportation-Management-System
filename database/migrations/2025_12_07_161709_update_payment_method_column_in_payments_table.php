<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method', 50)->change(); // Increase to 50 characters
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method', 20)->change();
        });
    }
};