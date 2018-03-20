<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('name')->nullable();
            $table->unsignedInteger('quentity');
            $table->unsignedInteger('spec_id')->nullable();
            $table->unsignedInteger('cart_id');

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('id')->references('id')->on('specs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cart_items');
        Schema::drop('carts');
    }
}
