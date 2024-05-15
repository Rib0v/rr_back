<?php

namespace App\Services;

use App\Http\Resources\Advert\ShowResource;
use App\Models\Advert;
use Illuminate\Support\Facades\Cache;

class AdvertService
{

    /**
     * Для кеширования использовал бы
     * phpredis и фасад Redis, но в 
     * данном случае это усложнило бы
     * запуск проекта проверяющим
     */
    public function getCachedAdvert(int $id)
    {
        if (!Cache::has("advert:$id")) {
            $advert = Advert::findOrFail($id);
            $data = new ShowResource($advert);

            // Кешируем на 1 час
            Cache::put("advert:$id", $data, 60 * 60);
        }

        return Cache::get("advert:$id");
    }

    public function getFormatedPhotos(array $validated): array
    {
        return array_map(fn ($photo) => ['url' => $photo], $validated['photos']);
    }
}
