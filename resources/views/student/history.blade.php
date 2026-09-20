@extends('layouts.app', ['title' => 'Riwayat Absensi Saya'])
@section('content')
<form class="card actions" method="get"><input style="max-width:220px" type="month" name="month" value="{{ $month }}"><button class="btn secondary">Filter</button><a class="btn" href="{{ route('cards.mine') }}">Kartu Saya</a></form>
<div class="grid stats">
    <div class="stat teal">
        <span class="material-symbols-outlined fill">check_circle</span>
        <div><div>Kehadiran</div><h2>{{ $percentages['Hadir'] }}%</h2></div>
    </div>
    <div class="stat ochre">
        <span class="material-symbols-outlined">event_available</span>
        <div><div>Izin</div><h2>{{ $percentages['Izin'] }}%</h2></div>
    </div>
    <div class="stat lav">
        <span class="material-symbols-outlined">sick</span>
        <div><div>Sakit</div><h2>{{ $percentages['Sakit'] }}%</h2></div>
    </div>
    <div class="stat peach">
        <span class="material-symbols-outlined">schedule</span>
        <div><div>Terlambat</div><h2>{{ $percentages['Terlambat'] }}%</h2></div>
    </div>
    <div class="stat pink">
        <span class="material-symbols-outlined">event_busy</span>
        <div><div>Tidak Hadir</div><h2>{{ $percentages['Alpa'] }}%</h2></div>
    </div>
</div>
<div class="card" style="margin-top:16px"><table><tr><th>Tanggal</th><th>Jam</th><th>Status</th><th>Catatan</th></tr>@foreach($attendances as $a)<tr><td>{{ $a->date->format('d/m/Y') }}</td><td>{{ $a->check_in_time ? $a->check_in_time->format('H:i') : '-' }}</td><td>{{ $a->status }}</td><td>{{ $a->note }}</td></tr>@endforeach</table></div>
@endsection
