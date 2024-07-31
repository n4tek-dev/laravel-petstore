@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Pets</h1>
        <a href="{{ route('pets.create') }}" class="btn btn-primary">Add New Pet</a>
    </div>
    <div class="list-group">
        @foreach($pets as $pet)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>ID:</strong> {{ $pet['id'] }} | <strong>Name:</strong> {{ $pet['name'] }} | <strong>Status:</strong> {{ $pet['status'] }}
                </div>
                <a href="{{ route('pets.show', $pet['id']) }}" class="btn btn-info btn-sm">View</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
