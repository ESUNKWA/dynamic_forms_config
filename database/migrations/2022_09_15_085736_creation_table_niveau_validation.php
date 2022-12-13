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
        Schema::create('t_niveau_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_task');
            $table->unsignedBigInteger('r_utilisateur');
            $table->timestamps();

            $table->foreign('r_task')->on('tasks')->references('id');
            $table->foreign('r_utilisateur')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_niveau_validations');
    }
};
