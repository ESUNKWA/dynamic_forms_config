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
        Schema::table('t_champs', function (Blueprint $table) {
            $table->integer('length',false)->nullable()->after('field_options');
            $table->integer('value_min', false)->nullable();
            $table->integer('value_max', false)->nullable();
            $table->longText('description', false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_champs', function (Blueprint $table) {
            $table->dropColumn(['length', 'value_min', 'value_max']);
        });
    }
};
