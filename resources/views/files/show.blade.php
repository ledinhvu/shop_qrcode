@extends('layouts.app')

@section('content')
    <h1>Details: {{ $file->shop_name }}</h1>

    <p>
        @if($fileUrl)
            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary">Show file</a>
        @endif
    </p>

    <div style="margin-top:20px">
        @if($qrUrl)
            <h4>QR code (scan to open file)</h4>
            <img src="{{ $qrUrl }}" alt="QR code">
        @else
            <p>No QR yet</p>
        @endif
    </div>

    <p style="margin-top:20px">
        <a href="{{ route('files.index') }}" class="btn btn-default">Back</a>
    </p>
@endsection
