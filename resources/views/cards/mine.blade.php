@extends('layouts.app', ['title' => 'Kartu Barcode Saya'])
@section('actions')<button class="btn" onclick="print()">Cetak</button>@endsection
@section('content')
<div class="print-card"><strong>{{ $student->name }}</strong><div class="muted">{{ $student->class->name }} · NIS {{ $student->nis }} · NISN {{ $student->nisn }}</div><div style="margin:14px 0">{!! QrCode::size(170)->generate($student->barcode_value) !!}</div><code>{{ $student->barcode_value }}</code></div>
@endsection
