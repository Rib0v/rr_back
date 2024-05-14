<?php

namespace Tests\Feature\Advert;

use Tests\TestCase;

class ShowTest extends TestCase
{
    public function test_route_is_available(): void
    {
        $response = $this->get('/api/adverts/1');
        $response->assertStatus(200);
    }

    public function test_data_not_empty(): void
    {
        $response = $this->get('/api/adverts/1');
        $data = $this->getData($response);

        $this->assertNotEmpty($data);
    }

    public function test_data_is_valid(): void
    {
        $response = $this->get('/api/adverts/1');
        $data = $this->getData($response);

        $this->assertNotEmpty($data->name);
        $this->assertNotEmpty($data->price);
        $this->assertNotEmpty($data->photo);
    }

    public function test_query_without_params_not_returns_description_and_list_of_photos(): void
    {
        $response = $this->get('/api/adverts/1');
        $data = $this->getData($response);

        $this->assertObjectNotHasProperty('description', $data);
        $this->assertObjectNotHasProperty('photos', $data);
    }

    public function test_query_with_descr_param_returns_description(): void
    {
        $response = $this->get('/api/adverts/1?fields=a,b,descr,c,d');
        $data = $this->getData($response);

        $this->assertNotEmpty($data->description);
    }

    public function test_query_with_photos_param_returns_list_of_photos(): void
    {
        $response = $this->get('/api/adverts/1?fields=a,b,photos,c,d');
        $data = $this->getData($response);

        $this->assertNotEmpty($data->photos);
    }
}
