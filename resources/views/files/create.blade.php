@extends('layouts.app')

@section('content')
    <h1>Add file</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Shop name</label>
            <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name') }}" required>
        </div>

        <div class="form-group">
            <label>Choose file</label>
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
        </div>

        <button class="btn btn-success">Create QR</button>
        <a href="{{ route('files.index') }}" class="btn btn-default">Cancel</a>
    </form>
@endsection
