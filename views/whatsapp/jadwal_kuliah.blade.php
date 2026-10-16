[Jadwal Sesi Mengajar]

Halo, Gilang 👋

Berikut adalah jadwal sesi mengajar Anda untuk hari *{{ $hari }}*:

@foreach($jadwalHariIni as $item)
- 🕒 {{ $item['time'] }}
- 📚 {{ $item['course'] }}
- 🎓 {{ $item['class'] }}
- 📍 {{ $item['room'] }}
@endforeach

Semangat mengajar hari ini 💪
