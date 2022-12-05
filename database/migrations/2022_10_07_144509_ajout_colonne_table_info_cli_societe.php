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
        Schema::table('t_info_cli_entp', function (Blueprint $table) {
            $table->unsignedBigInteger('r_forme_juridique', false)->nullable();

            $table->foreign('r_forme_juridique')->on('t_forme_juridiques')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_info_cli_entp', function (Blueprint $table) {
            //
        });
    }
};
