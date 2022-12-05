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
        Schema::create('t_validations_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_formulaire_saisi', false);
            $table->unsignedBigInteger('r_formulaire', false);
            $table->unsignedBigInteger('r_client', false);
            $table->unsignedBigInteger('r_niveau_validation', false);
            $table->timestamps();

            $table->foreign('r_formulaire_saisi')->on('t_formulaire_saisi')->references('id');
            $table->foreign('r_formulaire')->on('t_formulaires')->references('id');
            $table->foreign('r_client')->on('t_clients')->references('id');
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
        Schema::dropIfExists('t_validations_forms');
    }
};
