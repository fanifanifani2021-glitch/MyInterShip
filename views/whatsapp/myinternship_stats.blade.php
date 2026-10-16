[MyInternship Stats]

Halo, Gilang.
Berikut adalah statistik terbaru untuk Server MyInternship
- *Status Server*: {{ $status_server }}
- *Status API Login Polibatam*: {{ ($api_polibatam === false) ? 'Up' : '*Down*' }}

Statistik Hardware Server MyInternship
- *CPU Usage (Last 1 Minute)*: {{ $cpu_usage }}
- *Total RAM + SWAP Capacity*: {{ $total_ram }}
- *Total RAM + SWAP Usage*: {{ $used_ram }}
- *Total RAM + SWAP Available*: {{ $free_ram }}

Statistik Mahasiswa Magang Per Prodi:
@foreach($dataStatistikJurusan as $jurusan)

- *Jurusan {{ $jurusan['major'] }}*
@foreach($jurusan['prodi_list'] as $prodi)
- *{{ $prodi['jenjang']. " " .$prodi['prodi'] }}* ({{ $prodi['jenjang']. " " .$prodi['study_program'] }})
- Total Mahasiswa: {{ $prodi['total_mahasiswa'] }}
- Mahasiswa Angkatan {{ $prodi['tahun_angkatan_1'] }}: {{ $prodi['angkatan_1'] }}
- Mahasiswa Angkatan {{ $prodi['tahun_angkatan_2'] }}: {{ $prodi['angkatan_2'] }}
@endforeach

`-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`
@endforeach

Quick Access Link
- [CDC Login](https://myinternship.id/index.php?page=login_cdc)
- [Lecturer Login](https://myinternship.id/index.php?page=lecturer_login)
- [Student Login](https://myinternship.id/index.php?page=student_login)
- [Industry Login](https://myinternship.id/index.php?page=login_industri)

Terima kasih telah menggunakan layanan MyInternship!
