<?php

namespace App\Http\Controllers;

use App\Http\Requests\Advert\StoreRequest;
use App\Http\Resources\Advert\IndexResource;
use App\Http\Resources\Advert\ShowResource;
use App\Models\Advert;
use App\Services\AdvertService;
use Illuminate\Http\Request;

class AdvertController extends Controller
{
    /**
     * Это аннотации для генерации Swagger-спецификации.
     * При желании можно вынести в отдельный файл,
     * но оставил здесь для наглядности.
     * 
     * @OA\Get(
     *   tags={"Advert"},
     *   path="/api/adverts",
     *   summary="INDEX - список объявлений",
     *   description="",
     *   @OA\Parameter(name="sort", in="query", description="Варианты: newer/older/hiprice/lowprice",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(name="page", in="query", example="1",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="data", type="array",
     *         @OA\Items(
     *           @OA\Property(property="id", type="integer", example="1"),
     *           @OA\Property(property="name", type="string", example="Quia voluptatem qui ullam ab distinctio qui."),
     *           @OA\Property(property="price", type="integer", example="164108"),
     *           @OA\Property(property="photo", type="string", example="https://picsum.photos/640/480?random=500"),
     *           @OA\Property(property="created_at", type="string", example="2024-05-14T22:40:15.000000Z"),
     *         )
     *       ),
     *       @OA\Property(property="meta", type="object",
     *         @OA\Property(property="current_page", type="integer", example="1"),
     *         @OA\Property(property="last_page", type="integer", example="5"),
     *         @OA\Property(property="total", type="integer", example="50"),
     *       )
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $adverts = Advert::query()->sort($request)->paginate(10);
        return IndexResource::collection($adverts);
    }

    /**
     * @OA\Post(
     *   tags={"Advert"},
     *   path="/api/adverts",
     *   summary="STORE - создание нового объявления",
     *   @OA\RequestBody(required=true,
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="name", type="string", example="Тестовый товар"),
     *       @OA\Property(property="price", type="integer", example=100500),
     *       @OA\Property(property="description", type="string", example="Тестовое описание"),
     *       @OA\Property(property="photos", type="array",
     *         @OA\Items(type="string", example="https://picsum.photos/640/480?random=500"),
     *       )
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="id", type="integer", example="21"),
     *     )
     *   ),
     *   @OA\Response(response=422, description="Валидация не пройдена"),
     * )
     */
    public function store(StoreRequest $request, AdvertService $service)
    {
        $validated = $request->validated();
        $photos = $service->getFormattedPhotos($validated);
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

    /**
     * @OA\Get(
     *   tags={"Advert"},
     *   path="/api/adverts/{id}",
     *   summary="SHOW - показ отдельного объявления",
     *   @OA\Parameter(name="id", in="path", required=true, 
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(name="fields", in="query", description="Слитно, через запятую: descr,photos",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="data", type="object",
     *         @OA\Property(property="name", type="string", example="Quia voluptatem qui ullam ab distinctio."),
     *         @OA\Property(property="price", type="integer", example="164108"),
     *         @OA\Property(property="photo", type="string", example="https://picsum.photos/640/480?random=500"),
     *         @OA\Property(property="description", type="string", example="Consequatur fugiat molestiae..."),
     *         @OA\Property(property="photos", type="array",
     *           @OA\Items(type="string", example="https://picsum.photos/640/480?random=500"),
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id, Request $request, AdvertService $service)
    {
        if (empty($request->query())) {
            return $service->getCachedAdvert($id);
        }

        $advert = Advert::findOrFail($id);

        return new ShowResource($advert);
    }
}
