@extends('layouts.app', ['title' => 'Riwayat Absensi Saya'])
@section('content')
<form class="card actions" method="get"><input style="max-width:220px" type="month" name="month" value="{{ $month }}"><button class="btn secondary">Filter</button><a class="btn" href="{{ route('cards.mine') }}">Kartu Saya</a></form>
<div class="card" style="margin-top:16px"><table><tr><th>Tanggal</th><th>Jam</th><th>Status</th><th>Catatan</th></tr>@foreach($attendances as $a)<tr><td>{{ $a->date->format('d/m/Y') }}</td><td>{{ $a->check_in_time ? $a->check_in_time->format('H:i') : '-' }}</td><td>{{ $a->status }}</td><td>{{ $a->note }}</td></tr>@endforeach</table></div>
@endsection
