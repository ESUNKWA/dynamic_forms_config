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
        Schema::create('t_historique_cnx_cli', function (Blueprint $table) {
            $table->id();
            $table->dateTime('r_heure_cnx')->index();
            $table->dateTime('r_heure_dcnx')->nullable()->index();
            $table->string('r_adresse_ip', 20)->index();
            $table->unsignedBigInteger('r_client', false)->index();
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
        Schema::dropIfExists('t_historique_cnx_cli');
    }
};
