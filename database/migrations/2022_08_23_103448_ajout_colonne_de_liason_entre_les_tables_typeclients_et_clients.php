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
        Schema::table('t_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('r_type_client', false)->default(1)->before('r_creer_par');

            $table->foreign('r_type_client')->on('t_clients')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_clients', function (Blueprint $table) {
            $table->dropColumn('r_type_client');
        });
    }
};
