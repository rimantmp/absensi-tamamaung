@extends('layouts.app', ['title' => $student?->exists ? 'Edit Siswa' : 'Tambah Siswa'])
@section('content')
<form class="card grid" method="post" action="{{ $student?->exists ? route('students.update',$student) : route('students.store') }}">
    @csrf @if($student?->exists) @method('put') @endif
    <div class="grid two"><label>Nama<input name="name" value="{{ old('name',$student->name ?? '') }}"></label><label>NIS<input name="nis" value="{{ old('nis',$student->nis ?? '') }}"></label></div>
    <div class="grid two"><label>NISN<input name="nisn" value="{{ old('nisn',$student->nisn ?? '') }}"></label><label>Jenis Kelamin<select name="gender"><option @selected(old('gender',$student->gender ?? '')==='Laki-laki')>Laki-laki</option><option @selected(old('gender',$student->gender ?? '')==='Perempuan')>Perempuan</option></select></label></div>
    <div class="grid two"><label>Kelas<select name="class_id">@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id',$student->class_id ?? '')==$class->id)>{{ $class->name }}</option>@endforeach</select></label><label>Akun Siswa<select name="user_id"><option value="">Tanpa akun</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('user_id',$student->user_id ?? '')==$user->id)>{{ $user->name }}</option>@endforeach</select></label></div>
    <label>Status<select name="status"><option value="active" @selected(old('status',$student->status ?? 'active')==='active')>active</option><option value="inactive" @selected(old('status',$student->status ?? '')==='inactive')>inactive</option></select></label>
    @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
    <button class="btn">Simpan</button>
</form>
@endsection
