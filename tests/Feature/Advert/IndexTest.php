<?php

namespace Tests\Feature\Advert;

use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_route_is_available(): void
    {
        $response = $this->get('/api/adverts');
        $response->assertStatus(200);
    }

    public function test_data_not_empty(): void
    {
        $response = $this->get('/api/adverts');
        $adverts = $this->getData($response);

        $this->assertNotEmpty($adverts);
    }

    public function test_data_is_valid(): void
    {
        $response = $this->get('/api/adverts');
        $advert = $this->getData($response)[0];

        $this->assertNotEmpty($advert->name);
        $this->assertNotEmpty($advert->price);
        $this->assertNotEmpty($advert->photo);
        $this->assertNotEmpty($advert->created_at);
    }

    public function test_data_returns_with_paginate_limit(): void
    {
        $paginate = 10;

        $response = $this->get('/api/adverts');
        $adverts = $this->getData($response);

        $this->assertLessThanOrEqual($paginate, count($adverts));
    }

    public function test_data_returns_with_paginate_meta(): void
    {
        $response = $this->get('/api/adverts');
        $responseData = json_decode($response->content());

        $this->assertNotEmpty($responseData->meta);
        $this->assertNotEmpty($responseData->meta->current_page);
        $this->assertNotEmpty($responseData->meta->last_page);
        $this->assertNotEmpty($responseData->meta->total);
    }

    public function test_default_sort_is_ok(): void
    {
        $response = $this->get('/api/adverts');
        $adverts = $this->getData($response);

        $previousDate = date_modify(date_create(), '1 month')->format('c');

        foreach ($adverts as $advert) {
            $this->assertLessThanOrEqual($previousDate, $advert->created_at);
            $previousDate = $advert->created_at;
        }
    }

    public function test_newer_sort_is_ok(): void
    {
        $response = $this->get('/api/adverts?sort=newer');
        $adverts = $this->getData($response);

        $previousDate = date_modify(date_create(), '1 month')->format('c');

        foreach ($adverts as $advert) {
            $this->assertLessThanOrEqual($previousDate, $advert->created_at);
            $previousDate = $advert->created_at;
        }
    }

    public function test_older_sort_is_ok(): void
    {
        $response = $this->get('/api/adverts?sort=older');
        $adverts = $this->getData($response);

        $previousDate = date_create('1970-01-01')->format('c');

        foreach ($adverts as $advert) {
            $this->assertGreaterThanOrEqual($previousDate, $advert->created_at);
            $previousDate = $advert->created_at;
        }
    }

    public function test_lowprice_sort_is_ok(): void
    {
        $response = $this->get('/api/adverts?sort=lowprice');
        $adverts = $this->getData($response);

        $previousPrice = 0;

        foreach ($adverts as $advert) {
            $this->assertGreaterThanOrEqual($previousPrice, $advert->price);
            $previousPrice = $advert->price;
        }
    }

    public function test_hiprice_sort_is_ok(): void
    {
        $response = $this->get('/api/adverts?sort=hiprice');
        $adverts = $this->getData($response);

        $previousPrice = 1000000000000;

        foreach ($adverts as $advert) {
            $this->assertLessThanOrEqual($previousPrice, $advert->price);
            $previousPrice = $advert->price;
        }
    }
}
