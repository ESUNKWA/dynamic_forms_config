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
        Schema::table('t_info_cli_entp', function (Blueprint $table) {
            $table->string('r_sigle', 15)->nullable();
            $table->string('r_raison_social', 45)->nullable();
            $table->string('r_numero_contribuable', 35)->nullable();
            $table->string('r_numero_social', 35)->nullable();
            $table->string('r_email_entp', 225)->nullable();
            $table->string('r_contact_entp', 16)->nullable();
            $table->string('r_adresse_postale', 225)->nullable();
            $table->text('r_adresse_geo', 225)->nullable();
            $table->string('r_indicatif_pays', 5)->nullable();
            $table->string('r_procuration_url', 225)->nullable();
            $table->string('r_status', 225)->nullable();
            $table->text('r_description', 225)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_info_cli_entp', function (Blueprint $table) {
            //
        });
    }
};
