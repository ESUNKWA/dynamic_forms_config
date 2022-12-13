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
        Schema::create('t_info_cli_entp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_client');
            $table->string('r_nom_entp');
            $table->string('r_rccm');
            $table->timestamps();

            $table->foreign('r_client')->on('t_clients')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_info_cli_entp');
    }
};
