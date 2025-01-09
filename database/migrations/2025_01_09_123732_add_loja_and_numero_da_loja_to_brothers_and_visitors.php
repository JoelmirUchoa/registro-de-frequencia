<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLojaAndNumeroDaLojaToBrothersAndVisitors extends Migration
{
    public function up()
    {
        Schema::table('brothers', function (Blueprint $table) {
            $table->string('loja')->nullable()->after('position');
            $table->string('numero_da_loja')->nullable()->after('loja');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->string('loja')->nullable()->after('position');
            $table->string('numero_da_loja')->nullable()->after('loja');
        });
    }

    public function down()
    {
        Schema::table('brothers', function (Blueprint $table) {
            $table->dropColumn(['loja', 'numero_da_loja']);
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['loja', 'numero_da_loja']);
        });
    }
}

