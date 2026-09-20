@extends('layouts.app', ['title' => 'Nilai Siswa'])
@section('actions')@if($class)<a class="btn secondary" href="{{ route('grades.print', ['class_id' => $class->id, 'school_year' => $schoolYear, 'semester' => $semester]) }}" target="_blank"><span class="material-symbols-outlined">print</span>Cetak</a>@endif@endsection
@section('content')
<form class="card actions no-print" method="get" style="align-items:flex-end">
    <label>Kelas<select style="max-width:200px" name="class_id"><option value="">-- Pilih kelas --</option>@foreach($classes as $c)<option value="{{ $c->id }}" @selected($class?->id === $c->id)>{{ $c->name }}</option>@endforeach</select></label>
    <label>Tahun Ajaran<input style="max-width:140px" type="text" name="school_year" value="{{ $schoolYear }}" placeholder="2026/2027"></label>
    <label>Semester<select style="max-width:130px" name="semester"><option value="Ganjil" @selected($semester === 'Ganjil')>Ganjil</option><option value="Genap" @selected($semester === 'Genap')>Genap</option></select></label>
    <button class="btn secondary">Tampilkan</button>
    <small style="flex-basis:100%;color:var(--muted)">Isi nilai 0–100. Kosongkan sel jika belum dinilai; menyimpan ulang dengan sel kosong akan menghapus nilai tersebut.</small>
</form>

@if(! $class)
<div class="card"><p class="muted" style="margin:0">Pilih kelas untuk mengelola nilai.</p></div>
@elseif($students->isEmpty())
<div class="card"><p class="muted" style="margin:0">Belum ada siswa aktif di kelas ini.</p></div>
@else
<form method="post" action="{{ route('grades.store') }}">
    @csrf
    <input type="hidden" name="class_id" value="{{ $class->id }}">
    <input type="hidden" name="school_year" value="{{ $schoolYear }}">
    <input type="hidden" name="semester" value="{{ $semester }}">

    @php($types = [['tugas', 'A. Nilai Tugas'], ['mid', 'B. Nilai Mid Semester'], ['semester', 'C. Nilai Semester']])
    @foreach($types as [$key, $heading])
    <div class="card" style="margin-top:16px">
        <h3 style="margin-top:0">{{ $heading }}</h3>
        <table>
            <tr><th>No</th><th>NIS</th><th>Nama Siswa</th>@foreach($subjects as $sub)<th class="c">{{ $sub->name }}</th>@endforeach</tr>
            @foreach($students as $i => $student)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $student->nis }}</td>
                <td style="white-space:nowrap">{{ $student->name }}</td>
                @foreach($subjects as $sub)
                    @php($grade = $grades->get("{$student->id}:{$sub->id}"))
                    <td class="c"><input style="max-width:90px;text-align:center" type="number" min="0" max="100" step="0.01" name="scores[{{ $student->id }}][{{ $sub->id }}][{{ $key }}]" value="{{ $grade?->{$key === 'tugas' ? 'score_tugas' : ($key === 'mid' ? 'score_mid' : 'score_semester')} }}"></td>
                @endforeach
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach

    <div class="card actions" style="margin-top:16px">
        @if($subjects->isEmpty())
        <p class="muted" style="margin:0">Belum ada mata pelajaran. Tambahkan terlebih dahulu di menu Data Mapel.</p>
        @else
        <button class="btn">Simpan Semua Nilai</button>
        <span class="muted tiny">{{ $schoolYear }} · Semester {{ $semester }} · {{ $subjects->count() }} mapel × {{ $students->count() }} siswa</span>
        @endif
    </div>
</form>
@endif
@endsection
