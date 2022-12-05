<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreationTableClientEntp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_client_entreprises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_entreprise', false);
            $table->unsignedBigInteger('r_client', false);
            $table->timestamps();


            $table->foreign('r_entreprise')->on('t_entreprises')->references('id');
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
        Schema::dropIfExists('t_client_entreprises');
    }
}
