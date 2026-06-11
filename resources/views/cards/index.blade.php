@extends('layouts.app', ['title' => 'Barcode/QR Siswa'])
@section('actions')<button class="btn" onclick="print()">Cetak</button>@endsection
@section('content')
<form class="card actions no-print" method="get"><select style="max-width:220px" name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select><button class="btn secondary">Filter</button></form>
<div class="cards-grid" style="margin-top:16px">@foreach($students as $student)<div class="print-card"><strong>{{ $student->name }}</strong><div class="muted">{{ $student->class->name }} · NIS {{ $student->nis }} · NISN {{ $student->nisn }}</div><div style="margin:14px 0">{!! QrCode::size(150)->generate($student->barcode_value) !!}</div><code>{{ $student->barcode_value }}</code></div>@endforeach</div>
@endsection
