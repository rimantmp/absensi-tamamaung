@extends('layouts.app', ['title' => 'Data Pengguna', 'subtitle' => 'Kelola akun pengguna sistem beserta peran dan status aksesnya.'])
@section('actions')
<button type="button" class="btn" onclick="openModal('user-create-modal')">
    <span class="material-symbols-outlined" style="font-size:18px">add</span>Tambah Pengguna
</button>
@endsection

@section('content')
<style>
    /* Toolbar — satu baris di dalam card, tinggi seragam */
    .usr-toolbar form{display:flex;flex-wrap:wrap;gap:10px;align-items:center;width:100%}
    .usr-search{position:relative;flex:1 1 260px;max-width:340px}
    .usr-search .material-symbols-outlined{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:19px;color:var(--muted);pointer-events:none}
    .usr-search input{width:100%;height:42px;min-height:42px;padding-left:40px;background:#fff}
    .usr-select{height:42px;min-height:42px;width:176px;flex:0 0 auto;cursor:pointer}
    .usr-toolbar .btn,.usr-toolbar .btn-ghost{height:42px;min-height:42px;padding:0 16px}
    .usr-toolbar .btn-ghost{display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:9px;border:1px solid var(--outline-variant);background:#fff;color:var(--body);font:600 14px/1 Inter,sans-serif;cursor:pointer;text-decoration:none}
    .usr-toolbar .btn-ghost:hover{background:var(--surface-low)}
    .usr-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:18px;border-top:1px solid var(--hairline);margin-top:18px}
    .usr-head h3{margin:0;font-size:15px;font-weight:650}
    .usr-count{color:var(--muted);font-size:13px}

    .usr-login strong{display:block;font-weight:600;color:var(--ink);line-height:1.3}
    .usr-login span{display:block;font-size:12px;color:var(--muted);margin-top:1px}
    .badge{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:3px 10px;font-size:12px;font-weight:600;line-height:1.6;white-space:nowrap}
    .badge .material-symbols-outlined{font-size:13px}
    .badge.active{background:#e7f8ef;color:#067647}
    .badge.inactive{background:#f2f4f7;color:#475467}
    .badge.role{background:#eef4ff;color:#1d4ed8}
    .usr-actions{display:flex;gap:6px;justify-content:flex-end}
    .usr-icon-btn{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;border-radius:8px;border:1px solid var(--outline-variant);background:#fff;color:var(--body);cursor:pointer;transition:border-color .15s,background .15s,color .15s}
    .usr-icon-btn .material-symbols-outlined{font-size:18px}
    .usr-icon-btn.edit:hover{border-color:var(--primary);color:var(--primary);background:#f5f9ff}
    .usr-icon-btn.del:hover{border-color:var(--error);color:var(--error);background:#fff5f5}
    .usr-icon-btn:disabled{opacity:.4;cursor:not-allowed}

    .modal-backdrop{position:fixed;inset:0;z-index:90;background:rgba(16,24,40,.52);display:none;align-items:flex-start;justify-content:center;padding:48px 16px;overflow-y:auto}
    .modal-backdrop.open{display:flex}
    .modal{width:min(560px,100%);background:#fff;border-radius:16px;box-shadow:0 24px 60px rgba(16,24,40,.28);overflow:hidden;animation:usrPop .18s ease}
    @keyframes usrPop{from{opacity:0;transform:translateY(-8px) scale(.985)}to{opacity:1;transform:translateY(0) scale(1)}}
    .modal-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:18px 22px;border-bottom:1px solid var(--hairline)}
    .modal-head h3{margin:0;font-size:17px;font-weight:650;color:var(--ink)}
    .modal-close{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;border:0;border-radius:8px;background:transparent;color:var(--muted);cursor:pointer}
    .modal-close:hover{background:var(--surface-low);color:var(--ink)}
    .modal-close .material-symbols-outlined{font-size:20px}
    .modal-body{padding:22px}
    .modal-foot{display:flex;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--hairline);background:var(--surface-lowest)}
    .btn-ghost{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:10px 16px;border-radius:9px;border:1px solid var(--outline-variant);background:#fff;color:var(--body);font:600 14px/1 Inter,sans-serif;cursor:pointer;text-decoration:none}
    .btn-ghost:hover{background:var(--surface-low)}

    @media(max-width:560px){
        .usr-search{flex:1 1 100%;max-width:none}
        .usr-select{flex:1 1 100%;width:auto}
        .usr-toolbar .btn,.usr-toolbar .btn-ghost{flex:1 1 auto}
    }
</style>

<div class="card">
    <div class="usr-toolbar">
        <form method="get" action="{{ route('users.index') }}">
            <label class="usr-search">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, atau username...">
            </label>
            <select class="usr-select" name="role">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <select class="usr-select" name="status">
                <option value="">Semua Status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="btn"><span class="material-symbols-outlined" style="font-size:18px">tune</span>Filter</button>
            <a class="btn-ghost" href="{{ route('users.index') }}"><span class="material-symbols-outlined" style="font-size:18px">refresh</span>Reset</a>
        </form>
    </div>

    <div class="usr-head">
        <h3>Daftar Pengguna</h3>
        <span class="usr-count">{{ $users->total() }} akun</span>
    </div>

    <table style="margin-top:8px">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Login</th>
                <th>Role</th>
                <th>Status</th>
                <th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="font-weight:600;color:var(--ink)">{{ $user->name }}</td>
                <td class="usr-login"><strong>{{ $user->username }}</strong><span>{{ $user->email }}</span></td>
                <td>@foreach($user->roles as $role)<span class="badge role">{{ $role->name }}</span>@endforeach</td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'active' : 'inactive' }}">
                        <span class="material-symbols-outlined">{{ $user->status === 'active' ? 'check_circle' : 'block' }}</span>
                        {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div class="usr-actions">
                        <button type="button" class="usr-icon-btn edit" title="Edit"
                            onclick="openEdit('{{ $user->id }}','{{ addslashes($user->name) }}','{{ addslashes($user->username) }}','{{ addslashes($user->email) }}','{{ $user->roles->pluck('name')->first() }}','{{ $user->status }}')">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <form method="post" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                            @csrf @method('delete')
                            <button type="submit" class="usr-icon-btn del" title="Hapus" @disabled($user->id === auth()->id())>
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:36px 12px">Tidak ada pengguna yang cocok dengan filter.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>

{{-- Modal Tambah Pengguna --}}
<div class="modal-backdrop" id="user-create-modal" role="dialog" aria-modal="true">
    <div class="modal">
        <form method="post" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-head">
                <h3>Tambah Pengguna</h3>
                <button type="button" class="modal-close" onclick="closeModal('user-create-modal')" aria-label="Tutup"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="modal-body">
                <div class="grid two">
                    <label>Nama Lengkap<input name="name" value="{{ old('name') }}" required autofocus></label>
                    <label>Username<input name="username" value="{{ old('username') }}" required></label>
                </div>
                <div class="grid">
                    <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
                </div>
                <div class="grid two">
                    <label>Password<input type="password" name="password" required></label>
                    <label>Role<select name="role"><option>Admin</option><option>Guru</option><option>Kepala Sekolah</option><option>Siswa</option></select></label>
                </div>
                <div class="grid">
                    <label>Status<select name="status"><option value="active">active</option><option value="inactive">inactive</option></select></label>
                </div>
                @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeModal('user-create-modal')">Batal</button>
                <button type="submit" class="btn">Buat Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Pengguna --}}
<div class="modal-backdrop" id="user-edit-modal" role="dialog" aria-modal="true">
    <div class="modal">
        <form id="user-edit-form" method="post" action="">
            @csrf @method('put')
            <div class="modal-head">
                <h3>Edit Pengguna</h3>
                <button type="button" class="modal-close" onclick="closeModal('user-edit-modal')" aria-label="Tutup"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="modal-body">
                <div class="grid two">
                    <label>Nama Lengkap<input name="name" id="edit-name" required></label>
                    <label>Username<input name="username" id="edit-username" required></label>
                </div>
                <div class="grid">
                    <label>Email<input type="email" name="email" id="edit-email" required></label>
                </div>
                <div class="grid two">
                    <label>Password Baru <span style="font-weight:400;color:var(--muted)">(kosongkan jika tetap)</span><input type="password" name="password" placeholder="••••••••"></label>
                    <label>Role<select name="role" id="edit-role"><option>Admin</option><option>Guru</option><option>Kepala Sekolah</option><option>Siswa</option></select></label>
                </div>
                <div class="grid">
                    <label>Status<select name="status" id="edit-status"><option value="active">active</option><option value="inactive">inactive</option></select></label>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeModal('user-edit-modal')">Batal</button>
                <button type="submit" class="btn">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        const first = document.querySelector('#' + id + ' input[autofocus], #' + id + ' input, #' + id + ' select');
        if (first) setTimeout(() => first.focus(), 50);
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }
    function openEdit(id, name, username, email, role, status) {
        const route = '{{ route('users.update', '__ID__') }}'.replace('__ID__', id);
        document.getElementById('user-edit-form').action = route;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        document.getElementById('edit-status').value = status;
        openModal('user-edit-modal');
    }
    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) closeModal(backdrop.id);
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m.id));
    });
    @if($errors->any())
    openModal('user-create-modal');
    @endif
</script>
@endpush
@endsection