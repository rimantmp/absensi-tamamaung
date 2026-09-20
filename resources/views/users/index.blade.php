@extends('layouts.app', ['title' => 'Data Pengguna'])
@section('content')
<div class="grid two" style="align-items:start">
<form class="card grid" method="post" action="{{ route('users.store') }}">@csrf<h3>Tambah Pengguna</h3><label>Nama<input name="name"></label><label>Username<input name="username"></label><label>Email<input type="email" name="email"></label><label>Password<input type="password" name="password"></label><label>Role<select name="role"><option>Admin</option><option>Guru</option><option>Kepala Sekolah</option><option>Siswa</option></select></label><label>Status<select name="status"><option value="active">active</option><option value="inactive">inactive</option></select></label><button class="btn">Buat Akun</button></form>
<div class="card"><table><tr><th>Nama</th><th>Login</th><th>Role</th><th>Status</th></tr>@foreach($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->username }}<br>{{ $user->email }}</td><td>{{ $user->roles->pluck('name')->join(', ') }}</td><td>{{ $user->status }}</td></tr>@endforeach</table></div>
</div>
@endsection
