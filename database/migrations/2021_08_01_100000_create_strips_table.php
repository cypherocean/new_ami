<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strips', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit')->nullable();
            $table->string('choke')->nullable();
            $table->string('price')->nullable();
            $table->string('amp')->nullable();
            $table->string('inch_price')->nullable();
            $table->text('note')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strips');
    }
}
