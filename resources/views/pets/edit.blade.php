@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Pet</h1>
    <form action="{{ route('pets.update', $pet['id']) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $pet['name'] }}" required>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available" {{ $pet['status'] == 'available' ? 'selected' : '' }}>Available</option>
                <option value="pending" {{ $pet['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sold" {{ $pet['status'] == 'sold' ? 'selected' : '' }}>Sold</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Pet</button>
    </form>
</div>
@endsection
