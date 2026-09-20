@extends('layouts.app', ['title' => $class?->exists ? 'Edit Kelas' : 'Tambah Kelas'])
@section('actions')<a class="btn secondary" href="{{ route('classes.index') }}">Kembali</a>@endsection
@section('content')
<form class="card grid" method="post" action="{{ $class?->exists ? route('classes.update', $class) : route('classes.store') }}">
    @csrf @if($class?->exists) @method('put') @endif
    <div class="grid two">
        <label>Nama Kelas<input name="name" value="{{ old('name', $class->name ?? '') }}" placeholder="contoh: 4A"></label>
        <label>Tahun Ajaran<input name="academic_year" value="{{ old('academic_year', $class->academic_year ?? now()->year.'/'.(now()->year + 1)) }}" placeholder="contoh: 2025/2026"></label>
    </div>
    <label>Tingkat (untuk kenaikan kelas)<select name="level"><option value="">- Tidak ditentukan -</option>@foreach([1,2,3,4,5,6] as $lvl)<option value="{{ $lvl }}" @selected(old('level', $class->level ?? '')==$lvl)>Tingkat {{ $lvl }}</option>@endforeach</select></label>
    <label>Wali Kelas<select name="homeroom_teacher_id"><option value="">- Tanpa wali kelas -</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id ?? '')==$teacher->id)>{{ $teacher->name }}</option>@endforeach</select></label>
    <label>Status<select name="status"><option value="active" @selected(old('status', $class->status ?? 'active')==='active')>active</option><option value="inactive" @selected(old('status', $class->status ?? '')==='inactive')>inactive</option></select></label>
    @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
    <button class="btn">Simpan</button>
</form>
@endsection