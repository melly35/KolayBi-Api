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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('userId');
            $table->char('productBarcode', 15)->index();
            $table->text('productImage');
            $table->string('productName');
            $table->text('productDesc')->nullable();
            $table->double('productPrice', 8, 2);
            $table->integer('mainCategoryId');
            $table->integer('subCategoryId');
            $table->timestamps();
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
};
