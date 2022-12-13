<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_formulaire_champs', function (Blueprint $table) {
            $table->unsignedBigInteger('r_grp_champs', false)->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_formulaire_champs', function (Blueprint $table) {
            $table->dropColumn('r_grp_champs');
        });
    }
};
