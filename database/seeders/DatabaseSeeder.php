<?php

namespace Database\Seeders;

use App\Models\Advert;
use App\Models\Photo;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $advertsQuantity = 50;


        for ($id = 1; $id <= $advertsQuantity; $id++) {

            Advert::factory()->create();

            for ($i = 0; $i < 3; $i++) {
                Photo::factory()->create([
                    'advert_id' => $id,
                    'url' => "https://picsum.photos/640/480?random={$id}{$i}"
                ]);
            }

            /**
             * Задержка нужна, чтобы была возможность
             * тестировать правильность сортировки
             * объявлений по дате. Иначе время создания
             * всех объявлений будет одинаковым.
             */
            usleep(100000);
        }
    }
}
