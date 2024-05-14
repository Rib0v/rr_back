<?php

namespace Tests\Feature\Advert;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function test_route_returns_status_422_when_required_fields_is_empty(): void
    {
        $response = $this->post('/api/adverts', [], ['Accept' => 'application/json']);
        $response->assertStatus(422);
    }

    public function test_route_returns_error_when_required_fields_is_empty(): void
    {
        $response = $this->post('/api/adverts', [], ['Accept' => 'application/json']);
        $data = json_decode($response->content());

        $this->assertNotEmpty($data->errors);
        $this->assertNotEmpty($data->errors->name);
        $this->assertNotEmpty($data->errors->price);
        $this->assertNotEmpty($data->errors->description);
        $this->assertNotEmpty($data->errors->photos);
    }

    public function test_route_returns_error_when_send_name_more_then_200_symbols(): void
    {
        $requestData = ['name' => str_repeat('a', 201)];
        $response = $this->post('/api/adverts', $requestData, ['Accept' => 'application/json']);
        $data = json_decode($response->content());

        $this->assertNotEmpty($data->errors->name);
        $this->assertEquals($data->errors->name[0], "The name field must not be greater than 200 characters.");
    }

    public function test_route_returns_error_when_send_description_more_then_1000_symbols(): void
    {
        $requestData = ['description' => str_repeat('a', 1001)];
        $response = $this->post('/api/adverts', $requestData, ['Accept' => 'application/json']);
        $data = json_decode($response->content());

        $this->assertNotEmpty($data->errors->description);
        $this->assertEquals($data->errors->description[0], "The description field must not be greater than 1000 characters.");
    }

    public function test_route_returns_error_when_send_more_then_3_photos(): void
    {
        $requestData = ['photos' => ['a', 'b', 'c', 'd']];
        $response = $this->post('/api/adverts', $requestData, ['Accept' => 'application/json']);
        $data = json_decode($response->content());

        $this->assertNotEmpty($data->errors->photos);
        $this->assertEquals($data->errors->photos[0], "The photos field must not have more than 3 items.");
    }

    public function test_route_returns_status_201_when_all_fields_is_valid(): void
    {
        $requestData = $this->getFakeData();
        $response = $this->post('/api/adverts', $requestData, ['Accept' => 'application/json']);
        $response->assertStatus(201);
    }

    public function test_advert_with_photos_successfull_added_to_db(): void
    {
        $requestData = $this->getFakeData();
        $response = $this->post('/api/adverts', $requestData, ['Accept' => 'application/json']);
        $id = json_decode($response->content())->id;

        $response = $this->get("/api/adverts/$id?fields=descr,photos");
        $data = $this->getData($response);

        $this->assertEquals($data->name, $requestData['name']);
        $this->assertEquals($data->price, $requestData['price']);
        $this->assertEquals($data->description, $requestData['description']);

        foreach ($data->photos as $key => $photo) {
            $this->assertEquals($photo, $requestData['photos'][$key]);
        }
    }

    private function getFakeData(): array
    {
        return [
            'name' => fake()->text(50),
            'description' => fake()->text(1000),
            'price' => fake()->numberBetween(1500, 200000),
            'photos' => [
                fake()->imageUrl(),
                fake()->imageUrl(),
                fake()->imageUrl()
            ]
        ];
    }
}
