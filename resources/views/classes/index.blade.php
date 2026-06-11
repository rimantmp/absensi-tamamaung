@extends('layouts.app', ['title' => 'Data Kelas'])
@section('content')
<div class="grid two">
<form class="card grid" method="post" action="{{ route('classes.store') }}">@csrf<h3>Tambah Kelas</h3><label>Nama Kelas<input name="name"></label><label>Wali Kelas<select name="homeroom_teacher_id"><option value="">-</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select></label><label>Tahun Ajaran<input name="academic_year" value="2025/2026"></label><label>Status<select name="status"><option value="active">active</option><option value="inactive">inactive</option></select></label><button class="btn">Simpan</button></form>
<div class="card"><table><tr><th>Kelas</th><th>Wali</th><th>Siswa</th><th>Status</th></tr>@foreach($classes as $class)<tr><td>{{ $class->name }}</td><td>{{ $class->homeroomTeacher->name ?? '-' }}</td><td>{{ $class->students_count }}</td><td>{{ $class->status }}</td></tr>@endforeach</table></div>
</div>
@endsection
