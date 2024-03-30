<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            $table->float('0_500g')->after('country');
            $table->float('501_1000g')->after('0_500g');
            $table->float('1001_2000g')->after('501_1000g');
            $table->float('2001_5000g')->after('1001_2000g');
            $table->float('above_5000g')->after('2001_5000g');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_charges', function (Blueprint $table) {
            //
        });
    }
};
