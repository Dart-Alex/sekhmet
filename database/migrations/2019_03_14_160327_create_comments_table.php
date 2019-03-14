<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id')->nullable()->default(null);
			$table->unsignedBigInteger('post_id');
			$table->unsignedBigInteger('reply_to')->nullable()->default(null);
			$table->string('name');
			$table->text('content');
			$table->timestamps();
			$table->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('set null');
			$table->foreign('post_id')
				->references('id')
				->on('posts')
				->onDelete('cascade');
			$table->foreign('reply_to')
				->references('id')
				->on('comments')
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
        Schema::dropIfExists('comments');
    }
}
