@extends('layouts.app', ['title' => 'Dashboard Hari Ini'])
@section('content')
<div class="grid stats">
    <div class="stat ochre">
        <span class="material-symbols-outlined">groups</span>
        <div><div class="muted">Siswa Aktif</div><h2>{{ $totalStudents }}</h2></div>
    </div>
    <div class="stat teal">
        <span class="material-symbols-outlined fill">check_circle</span>
        <div><div>Hadir Hari Ini</div><h2>{{ $counts['Hadir'] }}</h2></div>
    </div>
    <div class="stat peach">
        <span class="material-symbols-outlined">schedule</span>
        <div><div>Terlambat</div><h2>{{ $counts['Terlambat'] }}</h2></div>
    </div>
    <div class="stat lav">
        <span class="material-symbols-outlined">event_busy</span>
        <div><div>Izin/Sakit/Alpa</div><h2>{{ $counts['Izin'] + $counts['Sakit'] + $counts['Alpa'] }}</h2></div>
    </div>
</div>
<div class="grid two" style="margin-top:16px">
    <div class="card"><h3 style="margin-top:0">Rekap per Kelas</h3><table><tr><th>Kelas</th><th>Absen</th><th>Total</th></tr>@foreach($classSummary as $class)<tr><td>{{ $class->name }}</td><td>{{ $class->attended_today }}</td><td>{{ $class->total_students }}</td></tr>@endforeach</table></div>
    <div class="card"><h3 style="margin-top:0">Belum Absen</h3><table><tr><th>Nama</th><th>Kelas</th></tr>@foreach($notYet as $student)<tr><td>{{ $student->name }}</td><td>{{ $student->class->name }}</td></tr>@endforeach</table></div>
</div>
@endsection
