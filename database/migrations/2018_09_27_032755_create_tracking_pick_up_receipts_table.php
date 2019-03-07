<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingPickUpReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_pick_up_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('community_id');
            $table->unsignedInteger('user_id');
            $table->string('customer_no');
            $table->string('pick_up_no');
            $table->string('shipper');
            $table->string('shipper_phone');
            $table->string('shipper_post');
            $table->string('shipper_address');
            $table->string('consignee');
            $table->string('consignee_phone');
            $table->string('consignee_post');
            $table->string('consignee_address');
            $table->string('transport_date')->nullable();
            $table->string('delivery_period')->default(4);
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('tracking_pick_up_receipts');
    }
}
