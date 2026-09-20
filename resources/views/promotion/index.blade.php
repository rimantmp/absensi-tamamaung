@extends('layouts.app', ['title' => 'Kenaikan Kelas'])
@section('content')
<form class="card actions" method="get" style="align-items:flex-end">
    <label>Kelas<select style="max-width:200px" name="class_id"><option value="">-- Pilih kelas --</option>@foreach($classes as $c)<option value="{{ $c->id }}" @selected($class?->id === $c->id)>{{ $c->name }}</option>@endforeach</select></label>
    <label>Tahun Ajaran<input style="max-width:140px" type="text" name="school_year" value="{{ $schoolYear }}"></label>
    <label>Semester<select style="max-width:130px" name="semester"><option value="Ganjil" @selected($semester === 'Ganjil')>Ganjil</option><option value="Genap" @selected($semester === 'Genap')>Genap</option></select></label>
    <button class="btn secondary">Tampilkan</button>
</form>

@if(! $class)
<div class="card"><p class="muted" style="margin:0">Pilih kelas untuk menentukan kenaikan.</p></div>
@else
<form method="post" action="{{ route('promotion.process') }}" onsubmit="return confirm('Proses promosi? Siswa Naik pindah ke kelas tujuan, siswa Lulus dinonaktifkan. Tindakan ini memindahkan data siswa.')">
    @csrf
    <div class="card actions" style="margin-top:16px;align-items:center">
        <b>Promosi:</b>
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="school_year" value="{{ $schoolYear }}">
        <select style="max-width:220px" name="target_class_id" required>
            <option value="">-- Kelas tujuan --</option>
            @foreach($targets as $t)<option value="{{ $t->id }}">{{ $t->name }} (Tingkat {{ $t->level ?? '?' }})</option>@endforeach
        </select>
        <button class="btn">Proses Promosi</button>
        <span class="muted tiny">Siswa "Naik" pindah kelas · "Lulus" dinonaktifkan · "Tinggal" tetap.</span>
    </div>
</form>

<form method="post" action="{{ route('promotion.decisions') }}">
    @csrf
    <div class="card" style="margin-top:16px">
        <div class="actions" style="justify-content:space-between">
            <h3 style="margin:0">Daftar Siswa — {{ $class->name }}</h3>
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="school_year" value="{{ $schoolYear }}">
            <button class="btn secondary">Simpan Keputusan</button>
        </div>
        <p class="muted tiny">Rata-rata dihitung dari nilai akhir tiap mapel ({{ $semester }}, {{ $schoolYear }}).</p>
        <table>
            <tr><th>No</th><th>NIS</th><th>Nama Siswa</th><th>Rata-rata Nilai</th><th>Keputusan</th></tr>
            @foreach($students as $i => $student)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $student->nis }}</td>
                <td>{{ $student->name }}</td>
                <td><b>{{ isset($averages[$student->id]) && $averages[$student->id] !== null ? number_format($averages[$student->id], 2) : '-' }}</b></td>
                <td>
                    @php($existing = $decisions->get($student->id))
                    <div style="display:flex;gap:14px;font-size:14px">
                        @foreach(['Naik', 'Tinggal', 'Lulus'] as $opt)
                        <label style="display:inline-flex;align-items:center;gap:6px;font-weight:400;cursor:pointer">
                            <input style="min-height:auto;width:auto" type="radio" name="decision[{{ $student->id }}]" value="{{ $opt }}" @checked(($existing?->decision ?? request("decision.{$student->id}")) === $opt)> {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endforeach
            @if($students->isEmpty())
            <tr><td colspan="5">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endif
        </table>
        @if($class->level >= 6)
        <p class="muted tiny" style="margin-bottom:0">Kelas tingkat {{ $class->level }}: siswa yang lulus akan dinonaktifkan (alumni).</p>
        @endif
    </div>
</form>
@endif
@endsection
