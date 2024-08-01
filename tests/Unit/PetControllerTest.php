<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Mockery;
use App\Http\Controllers\PetController;
use App\Services\PetService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PetControllerTest extends TestCase
{
    protected $petService;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petService = Mockery::mock(PetService::class);
        $this->controller = new PetController($this->petService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndex()
    {
        $pets = ['pet1', 'pet2'];
        $this->petService->shouldReceive('getAllPets')->once()->andReturn($pets);

        $response = $this->controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('pets.index', $response->name());
        $this->assertArrayHasKey('pets', $response->getData());
    }

    public function testCreate()
    {
        $response = $this->controller->create();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('pets.create', $response->name());
    }

    public function testStore()
    {
        $request = Request::create('/pets', 'POST', [
            'name' => 'Buddy',
            'status' => 'available'
        ]);

        $this->petService->shouldReceive('createPet')->once()->andReturn(true);

        $response = $this->controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('pets.index'), $response->headers->get('Location'));
    }

    public function testShow()
    {
        $pet = ['id' => 1, 'name' => 'Buddy'];
        $this->petService->shouldReceive('getPetById')->with(1)->once()->andReturn($pet);

        $response = $this->controller->show(1);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('pets.show', $response->name());
        $this->assertArrayHasKey('pet', $response->getData());
    }

    public function testEdit()
    {
        $pet = ['id' => 1, 'name' => 'Buddy'];
        $this->petService->shouldReceive('getPetById')->with(1)->once()->andReturn($pet);

        $response = $this->controller->edit(1);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('pets.edit', $response->name());
        $this->assertArrayHasKey('pet', $response->getData());
    }

    public function testUpdate()
    {
        $request = Request::create('/pets/1', 'PUT', [
            'name' => 'Buddy',
            'status' => 'available'
        ]);

        $this->petService->shouldReceive('updatePet')->with(1, Mockery::type('array'))->once()->andReturn(true);

        $response = $this->controller->update($request, 1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('pets.show', 1), $response->headers->get('Location'));
    }

    public function testDestroy()
    {
        $this->petService->shouldReceive('deletePet')->with(1)->once()->andReturn(true);

        $response = $this->controller->destroy(1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('pets.index'), $response->headers->get('Location'));
    }
}