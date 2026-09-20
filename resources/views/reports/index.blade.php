@extends('layouts.app', ['title' => 'Laporan Absensi'])
@section('actions')<a class="btn secondary" href="{{ route('reports.excel', request()->query()) }}">Excel</a><a class="btn secondary" href="{{ route('reports.pdf', request()->query()) }}">PDF</a><a class="btn secondary" href="{{ route('reports.print', request()->query()) }}" target="_blank"><span class="material-symbols-outlined">print</span>Print</a>@endsection
@section('content')
<style>
    .rp{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin:0}
    .rpc{border:1px solid var(--hairline);border-left:4px solid var(--ochre);border-radius:12px;padding:14px 16px;background:var(--surface-lowest)}
    .rpc b{display:block;font-size:26px;line-height:1;letter-spacing:-.5px;margin-top:4px}
    .rpc span{color:var(--muted);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.04em}
    .rpc.h{border-left-color:var(--success)}.rpc.t{border-left-color:#f97316}.rpc.i{border-left-color:var(--lav)}.rpc.s{border-left-color:var(--pink)}.rpc.a{border-left-color:var(--error)}.rpc.o{border-left-color:var(--ochre)}
    .report-meta{font-size:13px;color:var(--muted)}
    @media print{
        body{background:#fff}
        .main{padding:0}
        .card{border-color:#ccc;box-shadow:none;overflow:visible}
        form.card{display:none!important}
        .pagination{display:none!important}
        tr{break-inside:avoid}
        table{min-width:0}
        .rp{gap:8px}
        .rpc{break-inside:avoid}
        h3{margin-top:14px}
    }
</style>
<form class="card actions no-print" method="get" style="align-items:flex-end">
    <label>Tanggal<input style="max-width:180px" type="date" name="date" value="{{ request('date') }}"></label>
    <label>Minggu<input style="max-width:180px" type="date" name="week" value="{{ request('week') }}"></label>
    <label>Bulan<input style="max-width:180px" type="month" name="month" value="{{ request('month') }}"></label>
    <label>Kelas<select style="max-width:180px" name="class_id"><option value="">Semua kelas</option>@foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></label>
    <label>Siswa<select style="max-width:220px" name="student_id"><option value="">Semua siswa</option>@foreach($students as $s)<option value="{{ $s->id }}" @selected(request('student_id')==$s->id)>{{ $s->name }}</option>@endforeach</select></label>
    <label>Status<select style="max-width:160px" name="status"><option value="">Semua status</option>@foreach(['Hadir','Terlambat','Izin','Sakit','Alpa'] as $st)<option @selected(request('status')===$st)>{{ $st }}</option>@endforeach</select></label>
    <button class="btn secondary">Terapkan Filter</button>
    <a class="btn secondary" href="{{ route('reports.index') }}">Reset</a>
    <small style="flex-basis:100%;color:var(--muted)">Periode diisi salah satu saja: <b>Tanggal</b> = rekap per hari · <b>Minggu</b> = pilih tanggal apa pun, sistem ambil Senin–Minggu pada pekan itu · <b>Bulan</b> = rekap per bulan.</small>
</form>

@if($summary['total'] > 0)
<p class="report-meta">Periode: <b>{{ $period }}</b>@if($class) · Kelas: <b>{{ $class }}</b>@endif@if($student) · Siswa: <b>{{ $student }}</b>@endif@if($status) · Status: <b>{{ $status }}</b>@endif</p>
<div class="rp" style="margin:16px 0">
    <div class="rpc"><span>Total Data</span><b>{{ $summary['total'] }}</b></div>
    <div class="rpc h"><span>Hadir</span><b>{{ $summary['Hadir'] }}</b></div>
    <div class="rpc t"><span>Terlambat</span><b>{{ $summary['Terlambat'] }}</b></div>
    <div class="rpc i"><span>Izin</span><b>{{ $summary['Izin'] }}</b></div>
    <div class="rpc s"><span>Sakit</span><b>{{ $summary['Sakit'] }}</b></div>
    <div class="rpc a"><span>Alpa</span><b>{{ $summary['Alpa'] }}</b></div>
    <div class="rpc o"><span>% Kehadiran</span><b>{{ $summary['rate'] }}%</b></div>
</div>

<div class="card"><h3 style="margin-top:0">A. Rekap per Kelas</h3><table><tr><th>Kelas</th><th>Hadir</th><th>Terlambat</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>Total</th><th>% Kehadiran</th></tr>@foreach($recapByClass as $r)<tr><td>{{ $r['name'] }}</td><td>{{ $r['Hadir'] }}</td><td>{{ $r['Terlambat'] }}</td><td>{{ $r['Izin'] }}</td><td>{{ $r['Sakit'] }}</td><td>{{ $r['Alpa'] }}</td><td><b>{{ $r['total'] }}</b></td><td>{{ $r['rate'] }}%</td></tr>@endforeach</table></div>

@if($recapByStudent->isNotEmpty())
<div class="card" style="margin-top:16px"><h3 style="margin-top:0">B. Rekap per Siswa</h3><table><tr><th>No Absen</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Hadir</th><th>Terlambat</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>Total Hari</th><th>% Kehadiran</th></tr>@foreach($recapByStudent as $r)<tr><td>{{ $r['attendance_number'] }}</td><td>{{ $r['nis'] }}</td><td>{{ $r['name'] }}</td><td>{{ $r['class'] }}</td><td>{{ $r['Hadir'] }}</td><td>{{ $r['Terlambat'] }}</td><td>{{ $r['Izin'] }}</td><td>{{ $r['Sakit'] }}</td><td>{{ $r['Alpa'] }}</td><td><b>{{ $r['total'] }}</b></td><td>{{ $r['rate'] }}%</td></tr>@endforeach</table></div>
@endif
@endif

<div class="card" style="{{ $summary['total'] ? 'margin-top:16px' : '' }}"><h3 style="margin-top:0">C. Detail Absensi</h3><table><tr><th>Tanggal</th><th>Siswa</th><th>NIS</th><th>Kelas</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th><th>Input</th><th>Catatan</th></tr>@foreach($attendances as $a)<tr><td>{{ $a->date->format('d/m/Y') }}</td><td>{{ $a->student->name }}</td><td>{{ $a->student->nis }}</td><td>{{ $a->student->class->name }}</td><td>{{ $a->check_in_time ? $a->check_in_time->format('H:i') : '-' }}</td><td>{{ $a->check_out_time ? $a->check_out_time->format('H:i') : '-' }}</td><td>{{ $a->status }}</td><td>{{ $a->input_type }}</td><td>{{ $a->note ?? '-' }}</td></tr>@endforeach</table>{{ $attendances->links() }}</div>
@endsection
