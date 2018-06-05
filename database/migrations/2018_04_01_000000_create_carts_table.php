<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('quantity');
            $table->string('cart_id');

            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('id')->references('id')->on('specs')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('cart_items');
        Schema::drop('carts');
    }
}
