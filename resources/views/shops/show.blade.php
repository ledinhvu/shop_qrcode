@extends('layouts.app')

@section('content')
    <h1>Details: {{ $shop->shop_name }}</h1>

    <p>
        @if($pdfUrl)
            <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-primary">Show PDF</a>
        @endif
    </p>

    <div style="margin-top:20px">
        @if($qrUrl)
            <h4>QR code (scan to open PDF)</h4>
            <img src="{{ $qrUrl }}" alt="QR code">
        @else
            <p>No QR yet</p>
        @endif
    </div>

    <p style="margin-top:20px">
        <a href="{{ route('shops.index') }}" class="btn btn-default">Back</a>
    </p>
@endsection
