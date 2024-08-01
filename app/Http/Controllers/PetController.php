<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PetService;

class PetController extends Controller
{
    protected $petService;

    public function __construct(PetService $petService)
    {
        $this->petService = $petService;
    }

    public function index()
    {
        $pets = $this->petService->getAllPets();
        return view('pets.index', compact('pets'));
    }

    public function create()
    {
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,pending,sold'
        ]);

        $pet = $this->petService->createPet($validatedData);
        if ($pet) {
            return redirect()->route('pets.index')->with('success', 'Pet created successfully');
        }
        return back()->withErrors('Pet creation failed');
    }

    public function show($id)
    {
        $pet = $this->petService->getPetById($id);
        if (!$pet) {
            return redirect()->route('pets.index')->withErrors('Pet not found or API response is invalid.');
        }
        return view('pets.show', compact('pet'));
    }

    public function edit($id)
    {
        $pet = $this->petService->getPetById($id);
        if (!$pet) {
            return redirect()->route('pets.index')->withErrors('Pet not found or API response is invalid.');
        }
        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,pending,sold'
        ]);

        $pet = $this->petService->updatePet($id, $validatedData);
        if ($pet) {
            return redirect()->route('pets.show', $id)->with('success', 'Pet updated successfully');
        }
        return back()->withErrors('Pet update failed');
    }

    public function destroy($id)
    {
        if ($this->petService->deletePet($id)) {
            return redirect()->route('pets.index')->with('success', 'Pet deleted successfully');
        }
        return back()->withErrors('Pet deletion failed');
    }
}