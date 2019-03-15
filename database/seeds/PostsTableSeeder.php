<?php

use Illuminate\Database\Seeder;
use App\Chan;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$chans = Chan::all()->pluck('id')->toArray();
		foreach($chans as $chan) {
			factory(App\Post::class)->create(["chan_id" => $chan]);
		}
    }
}
