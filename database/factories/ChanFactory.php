<?php

use Faker\Generator as Faker;

$factory->define(App\Chan::class, function (Faker $faker) {
    return [
		'name' => $faker->unique()->word,
		'description' => $faker->sentence,
		'hidden' => $faker->boolean
    ];
});
