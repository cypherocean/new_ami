<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersStripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_strips', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->bigInteger('strip_id')->nullable()->unsigned();
            $table->string('quantity')->nullable();
            $table->enum('unit', ['inch', 'feet', 'meter'])->nullable();
            $table->string('choke')->nullable();
            $table->string('amp')->nullable();
            $table->string('calc')->nullable();
            $table->string('price')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_strips');
    }
}
