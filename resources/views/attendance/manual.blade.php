@extends('layouts.app', ['title' => 'Absensi Manual'])
@section('content')
<form class="card grid" method="post" action="{{ route('attendance.manual.store') }}">@csrf
<div class="grid two"><label>Tanggal<input type="date" name="date" value="{{ today()->format('Y-m-d') }}"></label><label>Siswa<select name="student_id">@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->name }} - {{ $student->class->name }}</option>@endforeach</select></label></div>
<div class="grid two"><label>Status<select name="status"><option>Hadir</option><option>Terlambat</option><option>Izin</option><option>Sakit</option><option>Alpa</option></select></label><label>Catatan<textarea name="note"></textarea></label></div>
<button class="btn">Simpan Absensi</button></form>
@endsection
