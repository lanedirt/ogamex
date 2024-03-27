<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users_tech', 'armor_technology')) {
            Schema::table('users_tech', function (Blueprint $table) {
                $table->integer('armor_technology')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users_tech', 'armor_technology')) {
            Schema::table('users_tech', function (Blueprint $table) {
                $table->dropColumn('armor_technology');
            });
        }
    }
};