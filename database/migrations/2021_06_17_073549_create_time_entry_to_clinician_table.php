<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeEntryToClinicianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_entry_to_clinician', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_entry_id');
            $table->unsignedBigInteger('clinician_id');
            $table->timestamps();
            
            $table->index(['time_entry_id', 'clinician_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_entry_to_clinician');
    }
}
