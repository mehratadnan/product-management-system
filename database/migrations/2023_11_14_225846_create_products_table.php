<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('productID');
            $table->integer('merchantID');
            $table->integer('quantity');
            $table->float('price');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['sale','out','deleted']);
            $table->text('photo_Url')->nullable();
            $table->timestamps();
            $table->index(['productID'], 'productID_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
