@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">{{ $pet['name'] }}</h1>
            <p class="card-text"><strong>ID:</strong> {{ $pet['id'] }}</p>
            <div class="d-flex justify-content-between">
                <a href="{{ route('pets.edit', $pet['id']) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST" class="ml-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
