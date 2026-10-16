[Weather Report]

Halo, {{ $nama }}.

Berikut adalah prakiraan cuaca untuk 
hari ini, ({{ $tanggal }})!

━━━━━━━━━━━━━━
🛣️ *Cuaca Perjalanan Pergi ke Polibatam

@foreach($perjalananPergi as $data)
📍 *{{ $data['lokasi']['desa'] ?? 'Tidak Diketahui' }}*

@foreach($data['cuaca'] as $item)
{!! $getEmoji($item['weather_desc']) !!} {{ $item['weather_desc'] }}
🌡️ {{ $item['t'] }}°C
@endforeach

@endforeach

━━━━━━━━━━━━━━
📅 *Cuaca Lengkap Hari Ini*

@foreach($cuacaLengkap as $data)
📍 *{{ $data['lokasi']['desa'] ?? 'Tidak Diketahui' }} ({{ $data['lokasi']['adm4'] ?? '' }})*

@foreach($data['cuaca'] as $item)
—
🕒 {{ date('H:i', strtotime($item['local_datetime'])) }}
{!! $getEmoji($item['weather_desc']) !!} {{ $item['weather_desc'] }}
🌡️ {{ $item['t'] }}°C
@endforeach

@endforeach
