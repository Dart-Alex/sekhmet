<?php

use Faker\Generator as Faker;

$factory->define(App\IrcName::class, function (Faker $faker) {
	$users = App\User::all()->pluck('id')->toArray();
    return [
		"name" => $faker->unique()->word,
		"user_id" => $faker->randomElement($users)
    ];
});
