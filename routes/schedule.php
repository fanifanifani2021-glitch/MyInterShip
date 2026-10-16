<?php
schedule_task('MyInternshipLocalAccountCreation', 'MyInternshipLocalAccount', 'daily', 1, 86400, ['00:00']);  // Daily 1 Kali, di jam 7

schedule_task('WeatherReport', 'WeatherReport', 'daily', 1, 86400, ['06:00']);  // Daily 1 Kali, di jam 6 
schedule_task('JadwalPerkuliahan', 'JadwalKuliah', 'daily', 1, 86400, ['06:30']);  // Daily 1 Kali,

schedule_task('MyinternshipStatsReport', 'MyInternshipStat', 'daily', 4, 86400, ['07:00', '11:00', '16:00', '21:00']);  // Daily 2 Kali, di jam 7 dan 12 