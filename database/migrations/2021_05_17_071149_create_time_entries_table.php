<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->smallInteger('spent');
            $table->unsignedBigInteger('clinician_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedTinyInteger('activity_type');
            $table->string('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('clinician_id')
                ->references('id')
                ->on('clinicians')
                ->onDelete('cascade');

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
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
        Schema::dropIfExists('time_entries');
    }
}
