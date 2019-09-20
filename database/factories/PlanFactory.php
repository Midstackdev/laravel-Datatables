<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Plan;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'stripe_id' => Str::random(10),
        'price' => random_int(1, 1000),
        'active' => false
    ];
});
