<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->integer('sipa_id')->nullable()->after('client_id');
            $table->string('sipa_ref_no')->nullable()->after('sipa_id');
        });
    }

    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn(['sipa_id', 'sipa_ref_no']);
        });
    }
};