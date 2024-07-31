<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'id', 'name', 'status', 'category', 'tags', 'photoUrls'
    ];

    public static function fromApiResponse(array $data)
    {
        return new self([
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? '',
            'status' => $data['status'] ?? '',
            'category' => $data['category'] ?? [],
            'tags' => $data['tags'] ?? [],
            'photoUrls' => $data['photoUrls'] ?? []
        ]);
    }
}