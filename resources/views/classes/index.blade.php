@extends('layouts.app', ['title' => 'Data Kelas'])
@section('actions')<a class="btn" href="{{ route('classes.create') }}">Tambah Kelas</a>@endsection
@section('content')
<div class="card"><table>
    <tr><th>Kelas</th><th>Wali Kelas</th><th>Jumlah Siswa</th><th>Status</th><th></th></tr>
    @forelse($classes as $class)
    <tr>
        <td>{{ $class->name }}</td>
        <td>{{ $class->homeroomTeacher->name ?? '-' }}</td>
        <td>{{ $class->students_count }}</td>
        <td>{{ $class->status }}</td>
        <td style="white-space:nowrap"><a class="btn secondary" style="display:inline-flex;min-height:34px" href="{{ route('classes.edit', $class) }}">Edit</a><form style="display:inline" method="post" action="{{ route('classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas {{ $class->name }} beserta seluruh data siswanya?')">@csrf @method('delete')<button class="btn secondary" style="min-height:34px;color:var(--error);border-color:var(--hairline)">Hapus</button></form></td>
    </tr>
    @empty
    <tr><td colspan="5">Belum ada kelas. Klik "Tambah Kelas" untuk membuat kelas baru.</td></tr>
    @endforelse
</table></div>
@endsection