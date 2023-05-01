<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inventory_id')->nullable();
            $table->string('sno')->nullable();
            $table->string('un_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('brand_id')->nullable();
            $table->string('model')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('invc_image')->nullable();
            $table->tinyInteger('status')->default(0)->comment("0=>cancel 1=>unassigned 2=>assigned 3=>scrapped");
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
        Schema::dropIfExists('inventory_invoices');
    }
}
