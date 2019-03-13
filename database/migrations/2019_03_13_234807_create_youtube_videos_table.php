<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYoutubeVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('youtube_videos', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('chan_name')->index();
			$table->string('name');
			$table->string('yid');
			$table->timestamps();
			$table->index(['chan_name', 'name']);
			$table->index(['chan_name', 'yid']);
			$table->unique(['chan_name', 'yid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('youtube_videos');
    }
}
