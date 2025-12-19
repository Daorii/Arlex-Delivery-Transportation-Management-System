<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('maintenance_reason');
        });
    }

    public function down()
    {
        Schema::table('trucks', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};