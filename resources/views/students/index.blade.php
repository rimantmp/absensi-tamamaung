@extends('layouts.app', ['title' => 'Data Siswa'])
@section('actions')<a class="btn" href="{{ route('students.create') }}">Tambah Siswa</a>@endsection
@section('content')
<form class="card actions" method="get"><input style="max-width:260px" name="q" value="{{ request('q') }}" placeholder="Cari nama/NIS/NISN"><select style="max-width:220px" name="class_id"><option value="">Semua kelas</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id')==$class->id)>{{ $class->name }}</option>@endforeach</select><button class="btn secondary">Filter</button></form>
<div class="card" style="margin-top:16px"><table><tr><th>Nama</th><th>NIS/NISN</th><th>Kelas</th><th>Status</th><th>Barcode</th><th></th></tr>@foreach($students as $student)<tr><td>{{ $student->name }}</td><td>{{ $student->nis }}<br>{{ $student->nisn }}</td><td>{{ $student->class->name }}</td><td>{{ $student->status }}</td><td>{{ $student->barcode_value }}</td><td><a class="btn secondary" href="{{ route('students.edit',$student) }}">Edit</a></td></tr>@endforeach</table>{{ $students->links() }}</div>
@endsection
