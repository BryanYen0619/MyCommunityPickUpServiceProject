<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tracking_pick_up_id');
            $table->string('tracking_number')->nullable();
            $table->string('piece');
            $table->string('carton_size');
            $table->string('receive_date')->nullable();
            $table->string('receive_time')->nullable();
            $table->string('pick_up_no')->nullable();
            $table->string('status')->nullable();
            $table->string('station')->nullable();
            $table->string('message')->nullable();
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
        Schema::dropIfExists('package_infos');
    }
}
