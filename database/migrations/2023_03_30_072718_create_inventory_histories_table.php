<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('inventory_invoice_id')->nullable();
            $table->string('assign_emp_id')->nullable();
            $table->string('assign_emp_name')->nullable();
            $table->string('cancelled_date')->nullable();
            $table->string('assigned_date')->nullable();
            $table->string('unassigned_date')->nullable();
            $table->string('scraped_date')->nullable();
            $table->string('asset_parent_id')->nullable();
            $table->json('asset_children_id')->nullable();
            $table->string('assigned_status')->nullable();
            $table->string('remarks')->nullable();
            $table->string('created_user_id')->nullable();
            $table->string('updated_user_id')->nullable();
            $table->tinyInteger('is_approved')->default(1)->comment("0=>pending 1=>approved 2=>declined");
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('inventory_histories');
    }
}
