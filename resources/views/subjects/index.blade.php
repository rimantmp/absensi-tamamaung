@extends('layouts.app', ['title' => 'Mata Pelajaran'])
@section('actions')@endsection
@section('content')
<div class="card">
    <h3 style="margin-top:0">Tambah Mata Pelajaran</h3>
    <form class="actions" method="post" action="{{ route('subjects.store') }}" style="align-items:flex-end">
        @csrf
        <label>Nama Mapel<input type="text" name="name" required placeholder="cth. Matematika"></label>
        <label>Kode<input style="max-width:120px" type="text" name="code" placeholder="cth. MTK"></label>
        <button class="btn">Simpan</button>
    </form>
</div>

<div class="card" style="margin-top:16px"><table>
    <tr><th>Nama Mapel</th><th>Kode</th><th>Jumlah Nilai</th><th></th></tr>
    @forelse($subjects as $subject)
    <tr>
        <td>
            <form method="post" action="{{ route('subjects.update', $subject) }}" style="display:flex;gap:8px;align-items:center">
                @csrf @method('put')
                <input style="min-width:220px" type="text" name="name" value="{{ $subject->name }}" required>
                <input style="width:100px" type="text" name="code" value="{{ $subject->code }}">
                <button class="btn secondary" style="min-height:34px">Simpan</button>
            </form>
        </td>
        <td>{{ $subject->code ?? '-' }}</td>
        <td>{{ $subject->grades_count }}</td>
        <td><form method="post" action="{{ route('subjects.destroy', $subject) }}" onsubmit="return confirm('Hapus mapel {{ $subject->name }} beserta seluruh nilainya?')">@csrf @method('delete')<button class="btn secondary" style="min-height:34px;color:var(--error);border-color:var(--hairline)">Hapus</button></form></td>
    </tr>
    @empty
    <tr><td colspan="4">Belum ada mata pelajaran.</td></tr>
    @endforelse
</table></div>
@endsection
