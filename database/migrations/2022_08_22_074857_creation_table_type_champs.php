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
        Schema::create('t_type_champs', function (Blueprint $table) {
            $table->id();
            $table->string('r_libelle',20)->unique();
            $table->longText('r_description',20)->nullable();
            $table->unsignedBigInteger('r_creer_par', false);
            $table->unsignedBigInteger('r_modifier_par');
            $table->timestamps();

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
        Schema::dropIfExists('t_type_champs');
    }
};
