<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table){
            $table->enum('payment_status',['paid','not paid'])->default('not paid')->after('grand_total');
            $table->enum('status',['pending','shipped','delivered'])->default('pending')->after('grand_total');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders',function(Blueprint $table){
            $table->dropColumn(['payment_status', 'status']);
        });
    }
};
