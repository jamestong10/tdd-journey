<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Recipe;
use Faker\Generator as Faker;

$factory->define(Recipe::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'procedure' => $faker->realText($faker->numberBetween(100, 200))
    ];
});
