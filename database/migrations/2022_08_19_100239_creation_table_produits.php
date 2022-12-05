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
        Schema::create('t_produits', function (Blueprint $table) {
            $table->id();
            $table->string('r_nom_produit',45);
            $table->mediumText('r_description')->nullable();
            $table->boolean('r_status')->default(0);
            $table->unsignedBigInteger('r_created_by');
            $table->unsignedBigInteger('r_updated_by');
            $table->timestamps();

            $table->foreign('r_created_by')->on('users')->references('id');
            $table->foreign('r_updated_by')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_produits');
    }
};
