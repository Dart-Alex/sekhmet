<?php
use Faker\Generator as Faker;
use App\ChanUser;

$factory->define(ChanUser::class, function (Faker $faker) {
	$users = App\User::all()->pluck('id')->toArray();
	$chans = App\Chan::all()->pluck('id')->toArray();
	$keys = [];
	foreach($chans as $chan) {
		foreach($users as $user) {
			$keys[] = [$chan, $user];
		}
	}
	$key = $faker->unique()->randomElement($keys);
    return [
		'chan_id' => $key[0],
		'user_id' => $key[1],
		'admin' => $faker->boolean
    ];
});
