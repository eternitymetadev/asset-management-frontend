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
            $table->string('asset_type')->nullable(); // hardware/software
            $table->string('undertaking')->nullable(); 
            $table->string('undertaking_image')->nullable();
            $table->string('assign_emp_id')->nullable();
            $table->string('assign_emp_name')->nullable();
            $table->string('cancelled_date')->nullable();
            $table->string('assigned_date')->nullable();
            $table->string('unassigned_date')->nullable();
            $table->string('scraped_date')->nullable();
            $table->string('asset_parent_id')->nullable();
            $table->json('asset_children_id')->nullable();
            $table->string('remarks')->nullable();
            $table->tinyInteger('is_approved')->default(1)->comment("0=>pending 1=>approved 2=>declined");
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
