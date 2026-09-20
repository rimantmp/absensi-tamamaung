<table>
    <thead>
    <tr><th>Tanggal</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th><th class="c">Jam Masuk</th><th class="c">Jam Pulang</th><th>Status</th><th class="c">Input</th><th>Catatan</th></tr>
    </thead>
    <tbody>
    @foreach($attendances as $a)
    <tr>
        <td>{{ $a->date->format('d/m/Y') }}</td>
        <td>{{ $a->student->nis }}</td>
        <td>{{ $a->student->name }}</td>
        <td>{{ $a->student->class->name }}</td>
        <td class="c">{{ $a->check_in_time ? $a->check_in_time->format('H:i') : '-' }}</td>
        <td class="c">{{ $a->check_out_time ? $a->check_out_time->format('H:i') : '-' }}</td>
        <td>{{ $a->status }}</td>
        <td class="c">{{ $a->input_type }}</td>
        <td>{{ $a->note ?? '-' }}</td>
    </tr>
    @endforeach
    </tbody>
</table>