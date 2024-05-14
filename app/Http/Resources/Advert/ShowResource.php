<?php

namespace App\Http\Resources\Advert;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fields = $request->has('fields')
            ? explode(',', $request->query('fields'))
            : [];

        $advert = [
            'name' => $this->name,
            'price' => $this->price,
            'photo' => $this->getFirstPhoto(),
        ];

        if (in_array('descr', $fields)) {
            $advert['description'] = $this->description;
        }

        if (in_array('photos', $fields)) {
            $advert['photos'] = $this->getPhotos();
        }

        return $advert;
    }

    private function getFirstPhoto(): string
    {
        return isset($this->photos[0]) ? $this->photos[0]->url : '';
    }

    private function getPhotos(): array
    {
        return array_map(fn ($item) => $item['url'], $this->photos->toArray());
    }
}
