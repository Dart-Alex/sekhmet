<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
	$chans = App\Chan::all()->pluck('id')->toArray();
    return [
		"name" => $faker->sentence(),
		"content" => $faker->randomHtml(2,3),
		"comments_allowed" => $faker->boolean,
		"chan_id" => $faker->randomElement($chans),
		"date" => $faker->dateTimeBetween($startDate = 'now', $endDate='+1 year')
    ];
});
