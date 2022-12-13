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
        Schema::create('t_formulaire_saisi_valeur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_formulaire_saisi', false);
            $table->unsignedBigInteger('r_champs', false);
            $table->string('r_valeur');
            $table->timestamps();

            $table->foreign('r_formulaire_saisi')->on('t_formulaire_saisi')->references('id');
            $table->foreign('r_champs')->on('t_champs')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_formulaire_saisi_valeur');
    }
};
