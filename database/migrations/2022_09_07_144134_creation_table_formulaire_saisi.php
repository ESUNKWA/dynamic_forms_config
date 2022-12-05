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
        Schema::create('t_formulaire_saisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_formulaire', false);
            $table->unsignedBigInteger('r_produit', false);
            $table->unsignedBigInteger('r_client', false);
            $table->longText('r_commentaire')->nullable();

            $table->unsignedBigInteger('r_creer_par', false);
            $table->unsignedBigInteger('r_modifier_par')->nullable();
            $table->timestamps();

            $table->foreign('r_formulaire')->on('t_formulaires')->references('id');
            $table->foreign('r_produit')->on('t_produits')->references('id');
            $table->foreign('r_client')->on('users')->references('id');
            $table->foreign('r_creer_par')->on('users')->references('id');
            $table->foreign('r_modifier_par')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_formulaire_saisi');
    }
};
