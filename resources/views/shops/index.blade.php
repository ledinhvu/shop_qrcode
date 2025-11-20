@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between">
        <h1>List Shop</h1>
        <a href="{{ route('shops.create') }}" class="btn btn-primary">Add new</a>
    </div>

    <table class="table table-striped" style="margin-top:20px">
        <thead>
            <tr>
                <th>#</th>
                <th>Shop Name</th>
                <th>PDF</th>
                <th>QR</th>
                <th>Times</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shops as $key => $s)
                <tr>
                    <td>{{ ($shops->currentPage() - 1) * $shops->perPage() + $loop->iteration }}</td>
                    <td><a href="{{ route('shops.show', $s->id) }}">{{ $s->shop_name }}</a></td>
                    <td>
                        @if($s->pdf_path)
                            <a href="{{ asset('storage/'.$s->pdf_path) }}" target="_blank">View PDF</a>
                        @endif
                    </td>
                    <td>
                        @if($s->qr_path)
                            <img src="{{ asset('storage/'.$s->qr_path) }}" width="70" alt="QR">
                        @endif
                    </td>
                    <td>{{ $s->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <form action="{{ route('shops.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No shop yet</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $shops->links() }}
@endsection
