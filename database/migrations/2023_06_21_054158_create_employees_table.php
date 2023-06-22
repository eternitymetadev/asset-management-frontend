<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('emp_code')->nullable();
            $table->string('name')->nullable();
            $table->string('office_email')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('office_phone_ext1')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('permanent_add1')->nullable();
            $table->string('permanent_add2')->nullable();
            $table->string('permanent_add3')->nullable();
            $table->string('permanent_add4')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('dateof_birth')->nullable();
            $table->string('dateof_joining')->nullable();
            $table->string('salutation')->nullable();
            $table->string('gender')->nullable();
            $table->string('wed_anniversary')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('location')->nullable();
            $table->string('group_code')->nullable();
            $table->string('employee_status')->nullable();
            $table->string('grade')->nullable();
            $table->string('designation')->nullable();
            // $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('employees');
    }
}
