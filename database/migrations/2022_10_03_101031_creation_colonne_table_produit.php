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
        Schema::create('t_conventions', function (Blueprint $table) {
            $table->id();
            $table->string('r_mnt_min',15);
            $table->string('r_mnt_max',15);
            $table->string('r_taux',5);
            $table->unsignedBigInteger('r_produit',false);
            $table->timestamps();

            $table->foreign('r_produit')->on('t_produits')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_conventions');
    }
};
