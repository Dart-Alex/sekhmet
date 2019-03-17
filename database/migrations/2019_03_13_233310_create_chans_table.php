<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chans', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name')->unique()->index();
			$table->text('description');
			$table->boolean('hidden')->default(true);
			$table->boolean('config_quiet')->default(false);
			$table->boolean('config_youtube_active')->default(false);
			$table->unsignedInteger('config_youtube_timer')->default(1800);
			$table->boolean('config_event_active')->default(false);
			$table->unsignedInteger('config_event_timer')->default(3600);
			$table->boolean('config_spam_active')->default(false);
			$table->unsignedInteger('config_spam_timer')->default(3600);
			$table->json('config_badwords')->default("[]");
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
        Schema::dropIfExists('chans');
    }
}
