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
        Schema::create('t_champs', function (Blueprint $table) {
            $table->id();
            $table->string('field_name',35);
            $table->string('field_label',35);
            $table->unsignedBigInteger('field_type', false);
            $table->string('field_value');
            $table->string('field_placeholder',35);
            $table->json('field_options');
            $table->unsignedBigInteger('product', false);
            $table->unsignedBigInteger('r_creer_par', false);
            $table->unsignedBigInteger('r_modifier_par');
            $table->timestamps();

            $table->foreign('field_type')->on('t_type_champs')->references('id');
            $table->foreign('product')->on('t_produits')->references('id');
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
        Schema::dropIfExists('t_champs');
    }
};
