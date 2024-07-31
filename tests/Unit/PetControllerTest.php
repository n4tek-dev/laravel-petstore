<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PetController;
use Illuminate\Http\Request;

class PetControllerTest extends TestCase
{
    protected $randomId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->randomId = random_int(1, PHP_INT_MAX);
    }

    public function testIndex()
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet/findByStatus?status=available' => Http::response([
                ['id' => $this->randomId, 'name' => 'Doggie', 'status' => 'available']
            ], 200),
            'https://petstore.swagger.io/v2/pet/findByStatus?status=pending' => Http::response([], 200),
            'https://petstore.swagger.io/v2/pet/findByStatus?status=sold' => Http::response([], 200),
        ]);

        $controller = new PetController();
        $response = $controller->index();

        $viewData = $response->getData();
        $this->assertArrayHasKey('pets', $viewData);
        $this->assertCount(1, $viewData['pets']);
        $this->assertEquals($this->randomId, $viewData['pets'][0]->id);
    }

    public function testCreate()
    {
        $controller = new PetController();
        $response = $controller->create();

        $this->assertEquals('pets.create', $response->name());
    }

    public function testStore()
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response([], 200)
        ]);

        $controller = new PetController();
        $request = Request::create('/pets', 'POST', [
            'name' => 'Doggie',
            'status' => 'available'
        ]);

        $response = $controller->store($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('pets.index'), $response->headers->get('Location'));
    }

    public function testEdit()
    {
        Http::fake([
            "https://petstore.swagger.io/v2/pet/{$this->randomId}" => Http::response([
                'id' => $this->randomId,
                'name' => 'Doggie',
                'status' => 'available'
            ], 200)
        ]);

        $controller = new PetController();
        $response = $controller->edit($this->randomId);

        $viewData = $response->getData();
        $this->assertArrayHasKey('pet', $viewData);
        $this->assertEquals($this->randomId, $viewData['pet']->id);
    }

    public function testDestroy()
    {
        Http::fake([
            "https://petstore.swagger.io/v2/pet/{$this->randomId}" => Http::response([], 200)
        ]);

        $controller = new PetController();
        $response = $controller->destroy($this->randomId);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('pets.index'), $response->headers->get('Location'));
    }

    public function testShowPet()
    {
        Http::fake([
            "https://petstore.swagger.io/v2/pet/{$this->randomId}" => Http::response([
                'id' => $this->randomId,
                'name' => 'Doggie',
                'status' => 'available'
            ], 200)
        ]);

        $controller = new PetController();
        $response = $controller->show($this->randomId);

        $viewData = $response->getData();
        $this->assertArrayHasKey('pet', $viewData);
        $this->assertEquals($this->randomId, $viewData['pet']->id);
        $this->assertEquals('Doggie', $viewData['pet']->name);
        $this->assertEquals('available', $viewData['pet']->status);
    }

    public function testUpdatePet()
    {
        Http::fake([
            'https://petstore.swagger.io/v2/pet' => Http::response([], 200)
        ]);

        $controller = new PetController();
        $request = Request::create("/pets/{$this->randomId}", 'PUT', [
            'name' => 'Doggie',
            'status' => 'available'
        ]);

        $response = $controller->update($request, $this->randomId);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(route('pets.show', $this->randomId), $response->headers->get('Location'));
    }
}
