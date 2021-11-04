<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCliniciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clinicians', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type');
            //$table->unsignedInteger('location');
            $table->string('photo')->nullable();
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('middle_name', 64);
            $table->string('directory')->nullable();
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
        Schema::dropIfExists('clinicians');
    }
}
