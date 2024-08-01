<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\PetService;

class PetServiceTest extends TestCase
{
    protected $petService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petService = new PetService();
    }

    public function testGetAllPets()
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/findByStatus?status=available' => Http::response([['id' => 1, 'name' => 'Dog1']]),
            'https://petstore.swagger.io/v2/pet/findByStatus?status=pending' => Http::response([['id' => 2, 'name' => 'Dog2']]),
            'https://petstore.swagger.io/v2/pet/findByStatus?status=sold' => Http::response([['id' => 3, 'name' => 'Dog3']]),
        ]);

        Cache::shouldReceive('remember')->once()->andReturnUsing(function ($key, $minutes, $callback) {
            return $callback();
        });

        $pets = $this->petService->getAllPets();

        $this->assertCount(3, $pets);
        $this->assertEquals(1, $pets[0]->id);
        $this->assertEquals(2, $pets[1]->id);
        $this->assertEquals(3, $pets[2]->id);
    }

    public function testCreatePet()
    {
        $data = ['name' => 'NewPet'];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response(['id' => 1, 'name' => 'NewPet'], 200)
        ]);

        Cache::shouldReceive('forget')->once()->with('pets');

        $response = $this->petService->createPet($data);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response['id']);
        $this->assertEquals('NewPet', $response['name']);
    }

    public function testGetPetById()
    {
        $id = 1;

        Http::fake([
            "https://petstore.swagger.io/v2/pet/{$id}" => Http::response(['id' => 1, 'name' => 'Dog1'], 200)
        ]);

        Cache::shouldReceive('remember')->once()->andReturnUsing(function ($key, $minutes, $callback) {
            return $callback();
        });

        $pet = $this->petService->getPetById($id);

        $this->assertNotNull($pet);
        $this->assertEquals(1, $pet->id);
        $this->assertEquals('Dog1', $pet->name);
    }

    public function testUpdatePet()
    {
        $id = 1;
        $data = ['name' => 'UpdatedPet'];

        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response(['id' => 1, 'name' => 'UpdatedPet'], 200)
        ]);

        Cache::shouldReceive('forget')->once()->with("pet_{$id}");
        Cache::shouldReceive('forget')->once()->with('pets');

        $response = $this->petService->updatePet($id, $data);

        $this->assertNotNull($response);
        $this->assertEquals(1, $response['id']);
        $this->assertEquals('UpdatedPet', $response['name']);
    }

    public function testDeletePet()
    {
        $id = 1;

        Http::fake([
            "https://petstore.swagger.io/v2/pet/{$id}" => Http::response(null, 200)
        ]);

        Cache::shouldReceive('forget')->once()->with("pet_{$id}");
        Cache::shouldReceive('forget')->once()->with('pets');

        $response = $this->petService->deletePet($id);

        $this->assertTrue($response);
    }
}