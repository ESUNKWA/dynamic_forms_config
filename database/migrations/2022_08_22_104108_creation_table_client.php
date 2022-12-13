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
        Schema::create('t_clients', function (Blueprint $table) {
            $table->id();
            $table->string('r_nom', 35)->nullable();
            $table->string('r_prenoms')->nullable();
            $table->string('r_telephone',13)->unique();
            $table->string('r_email',255)->unique();
            $table->longText('r_description')->nullable();
            $table->unsignedBigInteger('r_creer_par', false);
            $table->unsignedBigInteger('r_modifier_par', false);
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
        Schema::dropIfExists('t_clients');
    }
};
