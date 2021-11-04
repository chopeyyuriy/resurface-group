<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinician_id');
            $table->unsignedBigInteger('host_id');
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status')->nullable();
            $table->string('subject')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('from');
            $table->timestamp('to');
            $table->unsignedTinyInteger('all_day')->nullable();
            $table->date('date');
            $table->text('commentary');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('clinician_id')
                ->references('id')
                ->on('clinicians')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
