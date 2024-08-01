<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Pet;

/**
 * Class PetService.
 */
class PetService
{
    private $apiUrl = 'https://petstore.swagger.io/v2/pet';

    public function getAllPets()
    {
        $statuses = ['available', 'pending', 'sold'];
        return Cache::remember('pets', 60, function() use ($statuses) {
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
    }

    public function createPet($data)
    {
        $response = Http::post($this->apiUrl, $data);
        if ($response->successful()) {
            Cache::forget('pets');
            return $response->json();
        }
        return null;
    }

    public function getPetById($id)
    {
        return Cache::remember("pet_{$id}", 60, function() use ($id) {
            $response = Http::get("{$this->apiUrl}/{$id}");
            $petData = $response->json();

            if (!isset($petData['id'])) {
                return null;
            }

            return Pet::fromApiResponse($petData);
        });
    }

    public function updatePet($id, $data)
    {
        $data['id'] = $id;
        $response = Http::put($this->apiUrl, $data);
        if ($response->successful()) {
            Cache::forget("pet_{$id}");
            Cache::forget('pets');
            return $response->json();
        }
        return null;
    }

    public function deletePet($id)
    {
        $response = Http::delete("{$this->apiUrl}/{$id}");
        if ($response->successful()) {
            Cache::forget("pet_{$id}");
            Cache::forget('pets');
            return true;
        }
        return false;
    }
}