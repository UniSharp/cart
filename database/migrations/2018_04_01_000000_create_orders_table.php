<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedInteger('status')->default(0);
            $table->unsignedInteger('shipping_status')->default(0);
            $table->tinyInteger('payment_status')->default(0);
            $table->string('payment');
            $table->increments('id');
            $table->string('sn')->unique();
            $table->decimal('total_price');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('status');
            $table->string('name')->nullable();
            $table->string('spec')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('spec_id')->nullable();
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('spec_id')->references('id')->on('specs');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('order_items');
        Schema::drop('orders');
    }
}
