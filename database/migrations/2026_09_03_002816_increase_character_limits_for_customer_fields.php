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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->change();
            $table->string('country', 50)->nullable()->change();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('destination_country', 50)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('country', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('address', 255)->nullable()->change();
            $table->string('country', 2)->nullable()->change();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->string('destination_country', 2)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->change();
        });
    }
};
