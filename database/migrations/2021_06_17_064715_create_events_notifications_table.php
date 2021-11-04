<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events_notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->integer('user_id');
            $table->integer('event_id');
            $table->enum('status', ['new','read'])->default('new');
            $table->string('title')->nullable();
            $table->string('message');
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
        Schema::dropIfExists('events_notifications');
    }
}
