<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50000; $i++){
            DB::table('articles')->insert([
                'title' => $faker->sentence,
                'body' => $faker->text,
                'user_id' => $faker->numberBetween(1, 5),
                'created_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeThisYear->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
