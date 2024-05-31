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
        Schema::create('detail_sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->float('price', 8, 2);
            $table->float('total', 8, 2);
            $table->foreignId('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreignId('refund_id')->references('id')->on('refunds')->onDelete('cascade');
            $table->integer('product_id');
            $table->timestamps();

            // Foreign key constraints
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_sales');
    }
};
