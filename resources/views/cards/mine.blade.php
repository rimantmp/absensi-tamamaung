@extends('layouts.app', ['title' => 'Kartu Barcode Saya'])
@section('actions')<button class="btn" onclick="print()">Cetak</button>@endsection
@section('content')
<div class="card" style="display:flex;flex-direction:column;align-items:center;gap:16px;padding:24px">
    <div style="text-align:center"><p style="margin:0 0 4px;font-weight:600">Kartu Barcode Saya</p><p class="muted" style="margin:0">Gunakan kartu ini untuk melakukan absensi mandiri melalui scan QR.</p></div>
    @include('cards._card', ['student' => $student])
</div>
@endsection