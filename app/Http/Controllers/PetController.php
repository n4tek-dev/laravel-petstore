<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Pet;

class PetController extends Controller
{
    private $apiUrl = 'https://petstore.swagger.io/v2/pet';

    public function index()
    {
        $statuses = ['available', 'pending', 'sold'];
        $pets = Cache::remember('pets', 60, function() use ($statuses) {
            $allPets = [];
            foreach ($statuses as $status) {
                $response = Http::get("{$this->apiUrl}/findByStatus", ['status' => $status]);
                if ($response->successful()) {
                    $petsData = $response->json();
                    $allPets = array_merge($allPets, array_map(fn($data) => Pet::fromApiResponse($data), $petsData));
                }
            }
            return $allPets;
        });

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

        $response = Http::post($this->apiUrl, $validatedData);
        if ($response->successful()) {
            Cache::forget('pets');
            return redirect()->route('pets.index')->with('success', 'Pet created successfully');
        }
        return back()->withErrors($response->json());
    }

    public function show($id)
    {
        $pet = Cache::remember("pet_{$id}", 60, function() use ($id) {
            $response = Http::get("{$this->apiUrl}/{$id}");
            $petData = $response->json();

            if (!isset($petData['id'])) {
                return null;
            }

            return Pet::fromApiResponse($petData);
        });

        if (!$pet) {
            return redirect()->route('pets.index')->withErrors('Pet not found or API response is invalid.');
        }

        return view('pets.show', compact('pet'));
    }

    public function edit($id)
    {
        $response = Http::get("{$this->apiUrl}/{$id}");
        $petData = $response->json();
        $pet = Pet::fromApiResponse($petData);
        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,pending,sold'
        ]);

        $validatedData['id'] = $id;

        $response = Http::put($this->apiUrl, $validatedData);
        if ($response->successful()) {
            Cache::forget("pet_{$id}");
            Cache::forget('pets');
            return redirect()->route('pets.show', $id)->with('success', 'Pet updated successfully');
        }
        return back()->withErrors($response->json());
    }

    public function destroy($id)
    {
        $response = Http::delete("{$this->apiUrl}/{$id}");
        if ($response->successful()) {
            Cache::forget("pet_{$id}");
            Cache::forget('pets'); 
            return redirect()->route('pets.index')->with('success', 'Pet deleted successfully');
        }
        return back()->withErrors($response->json());
    }
}