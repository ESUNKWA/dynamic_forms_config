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
        Schema::create('t_niveaux_formulaires', function (Blueprint $table) {
            $table->id();
            $table->string('r_nom_niveau');
            $table->unsignedBigInteger('r_formulaire', false);
            $table->unsignedBigInteger('r_formulaire_saisi', false)->nullable();
            $table->unsignedBigInteger('r_niveau_validation', false)->nullable();
            $table->timestamps();

            $table->foreign('r_formulaire')->on('t_formulaires')->references('id');
            $table->foreign('r_formulaire_saisi')->on('t_formulaire_saisi')->references('id');
            $table->foreign('r_niveau_validation')->on('t_niveau_validations')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_niveaux_formulaires');
    }
};
