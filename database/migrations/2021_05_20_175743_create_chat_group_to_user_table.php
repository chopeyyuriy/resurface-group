<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatGroupToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_group_to_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_group_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('last_view_message_id')->nullable();
            $table->timestamps();
            
            //$table->index('chat_group_id');
            //$table->index('user_id');
            $table->unique(['chat_group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_group_to_user');
    }
}
