<?php

namespace App\Http\Resources\Advert;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * Поля created_at в задании не было,
         * но оно необходимо, чтобы тестировать
         * корректность сортировки по дате
         */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'photo' =>  $this->getPhoto(),
            'created_at' => $this->created_at,
        ];
    }

    private function getPhoto(): string
    {
        return isset($this->photos[0]) ? $this->photos[0]->url : '';
    }
}
