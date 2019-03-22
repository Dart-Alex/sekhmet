<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChanUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chan_users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('chan_id');
			$table->unsignedBigInteger('user_id');
			$table->boolean('admin')->default(false);
			$table->timestamps();
			$table->unique(['chan_id', 'user_id']);
			$table->foreign('chan_id')
				->references('id')->on('chans')
				->onDelete('cascade');
			$table->foreign('user_id')
				->references('id')->on('users')
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
        Schema::dropIfExists('chan_users');
    }
}
