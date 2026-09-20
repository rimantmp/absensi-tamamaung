@extends('layouts.app', ['title' => 'Barcode/QR Siswa'])
@section('actions')<button class="btn" onclick="print()">Cetak</button>@endsection
@section('content')
<form class="card actions no-print" method="get"><select style="max-width:220px" name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select><button class="btn secondary">Filter</button></form>
<div class="cards-grid" style="margin-top:16px">@foreach($students as $student)@include('cards._card', ['student' => $student])@endforeach</div>
@endsection