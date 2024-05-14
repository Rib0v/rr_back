<?php

namespace App\Http\Controllers;

use App\Http\Requests\Advert\StoreRequest;
use App\Http\Resources\Advert\IndexResource;
use App\Http\Resources\Advert\ShowResource;
use App\Models\Advert;
use App\Models\Photo;
use Illuminate\Http\Request;

class AdvertController extends Controller
{
    public function index(Request $request)
    {
        $adverts = Advert::query()->sort($request)->paginate(10);
        return IndexResource::collection($adverts);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $photos = array_map(fn ($photo) => ['url' => $photo], $validated['photos']);
        unset($validated['photos']);

        $advert = Advert::create($validated);
        $advert->photos()->createMany($photos);

        /**
         * Если валидация не пройдена - автоматом выдаётся код 422
         * Если ошибка добавление в БД - возвращается код 500
         * Если успешно создано - возвращаем код 201 (created)
         */
        return response(['id' => $advert->id], 201);
    }

    public function show(int $id)
    {
        $advert = Advert::findOrFail($id);

        return new ShowResource($advert);
    }
}
