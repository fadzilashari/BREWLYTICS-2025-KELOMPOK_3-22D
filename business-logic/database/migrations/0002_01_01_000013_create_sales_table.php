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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // User yang melakukan transaksi
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Tanggal dan waktu transaksi
            $table->dateTime('sale_date')->nullable();

            // Total nilai transaksi (jumlah semua items)
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->timestamps();

            // $table->id();
            // $table->integer('quantity');
            // $table->decimal('price', 10, 2);
            // $table->decimal('total', 10, 2)->nullable();
            // $table->date('sale_date');
            // $table->timestamps();

            // $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->unsignedBigInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
