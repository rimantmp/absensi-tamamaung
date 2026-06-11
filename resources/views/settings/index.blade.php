@extends('layouts.app', ['title' => 'Pengaturan Jam Masuk'])
@section('content')
<form class="card grid" method="post" action="{{ route('settings.store') }}">@csrf
<div class="grid two"><label>Jam Masuk<input type="time" name="school_start_time" value="{{ substr($setting->school_start_time,0,5) }}"></label><label>Batas Terlambat<input type="time" name="late_time_limit" value="{{ substr($setting->late_time_limit,0,5) }}"></label></div>
<div class="grid two">@foreach(['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu'] as $key=>$label)<label style="display:flex;align-items:center;gap:8px"><input style="width:auto;min-height:auto" type="checkbox" name="active_days[]" value="{{ $key }}" @checked(in_array($key,$setting->active_days ?? []))> {{ $label }}</label>@endforeach</div>
<button class="btn">Simpan Pengaturan</button></form>
@endsection
