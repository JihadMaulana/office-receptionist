<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuestRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guest_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requested_user_id')->nullable();
            $table->string('guest_name');
            $table->string('guest_need')->nullable();
            $table->text('servings')->nullable();
            $table->timestamps();

            $table->foreign('requested_user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guest_requests');
    }
}
