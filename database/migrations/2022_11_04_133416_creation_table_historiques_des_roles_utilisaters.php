<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreationTableHistoriquesDesRolesUtilisaters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_historiques_users_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_utilisateur', false);
            $table->unsignedBigInteger('r_role', false);
            $table->dateTime('r_date_debut')->nullable();
            $table->dateTime('r_date_fin')->nullable();
            $table->longText('r_description')->nullable();
            $table->unsignedBigInteger('r_creer_par');
            $table->timestamps();

            $table->foreign('r_utilisateur')->on('users')->references('id');
            $table->foreign('r_creer_par')->on('users')->references('id');
            $table->foreign('r_role')->on('roles')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_historiques_users_roles');
    }
}
