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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->double('subtotal', 10,2);
            $table->double('shipping', 10,2);
            // $table->string('coupon_code')->nullable();
            // $table->double('discount', 10,2)->nullable();
            $table->double('grand_total', 10,2);

            // user address related

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('mobile');
            // $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->text('address');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('barangay');
            $table->string('zip');
            $table->text('notes')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('orders');

        // DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Schema::dropIfExists('orders');
        // DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop the orders table
        Schema::dropIfExists('orders');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
