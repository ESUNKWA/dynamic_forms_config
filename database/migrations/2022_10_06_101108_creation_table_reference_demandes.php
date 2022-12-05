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
        Schema::create('t_ref_demandes', function (Blueprint $table) {
            $table->id();
            $table->string('r_reference',50)->unique()->index();
            $table->unsignedBigInteger('r_client', false)->index();
            $table->timestamps();


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
        Schema::dropIfExists('t_ref_demandes');
    }
};
