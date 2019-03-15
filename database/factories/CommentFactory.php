<?php

use Faker\Generator as Faker;

$factory->define(App\Comment::class, function (Faker $faker) {
	$posts = App\Post::all()->pluck('id')->toArray();
	$post = $faker->randomElement($posts);
	$comments = App\Comment::where('post_id', $post)->get()->pluck('id')->toArray();
	$users = App\User::all()->pluck('id')->toArray();
    return [
		"name" => $faker->name,
		"content" => $faker->text,
		"user_id" => $faker->randomElement([null, $faker->randomElement($users)]),
		"post_id" => $post,
		"reply_to" => $faker->randomElement([null, $faker->randomElement($comments)])
    ];
});
