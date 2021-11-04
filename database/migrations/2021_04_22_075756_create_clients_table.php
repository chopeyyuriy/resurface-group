<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_id');
            $table->unsignedBigInteger('status')->default(0);
            $table->string('photo');
            $table->string('first_name', 64);
            $table->string('last_name', 64);
            $table->string('middle_name', 64);
            $table->tinyInteger('relationship_status')->nullable();
            $table->date('date_birth');
            $table->date('admission_date');
            $table->string('gender', 6);
            $table->tinyInteger('race');
            $table->string('referred_name', 64)->nullable();
            $table->string('referred_company', 128)->nullable();
            $table->string('referred_phone')->nullable();
            $table->string('referred_email')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('state', 4)->nullable();
            $table->string('zipcode', 16);
            $table->string('email')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
