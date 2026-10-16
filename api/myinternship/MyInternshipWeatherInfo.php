<?php
session_start();

function getWeatherInfo($locationKey = 1127709, $apiKey = null, $apiEndpoint = "http://dataservice.accuweather.com/currentconditions/v1/")
{

    $resync = true;
    $result = array();

    if (isset($_SESSION['WEATHER'])) {
        // Jika sudah waktunya resync, maka set $resync sebagai true;
        // cek apakah sudah waktu nya NEXT_SYNC dari $_SESSION['WEATHER']
        $nextSyncDate = $_SESSION['WEATHER']['NEXT_SYNC'];
        $currentDate = date('Y-m-d');

        if ($currentDate <= $nextSyncDate) {
            $resync = false;
            $result = $_SESSION['WEATHER'];
        }
    }


    if ($resync) {
        $url = $apiEndpoint . $locationKey . "?apikey=$apiKey";


        $ch = curl_init();

        // Set URL untuk permintaan
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set untuk mengembalikan respons sebagai string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set pilihan cURL lainnya sesuai kebutuhan (misalnya: CURLOPT_RETURNTRANSFER, CURLOPT_TIMEOUT, dll.)
        $response = curl_exec($ch);
        $data = json_decode($response, true);


        if ($data === false) {
            $result['WEATHER'] = null;
        } else {
            $currentWeather = $data[0];

            $result['WEATHER']['CURRENT_SYNC'] = date('Y-m-d');
            $result['WEATHER']['NEXT_SYNC'] = date('Y-m-d', strtotime('+1 day'));
            $result['WEATHER']['TEMPERATURE'] = $currentWeather['Temperature']['Metric']['Value'];
            $result['WEATHER']['WEATHER_TEXT'] = $currentWeather['WeatherText'];
            $result['WEATHER']['LOCALOBSERVATIONDATETIME'] = $currentWeather['LocalObservationDateTime'];
            $result['WEATHER']['WEATHERICON'] = $currentWeather['WeatherIcon'];
            $result['WEATHER']['WEATHERICON_URL'] = "https://developer.accuweather.com/sites/default/files/" .sprintf('%02s', $currentWeather['WeatherIcon']). "-s.png";
        }
    }

    return $result;
}

$locationKey = $_POST['locationKey'] ?? env('WEATHER_LOCATION_KEY');
$apiKey = $_POST['apiKey'] ?? env('WEATHER_API_KEY');

// Memanggil fungsi untuk mendapatkan data berdasarkan nik
$result = getWeatherInfo($locationKey, $apiKey);

if (!$result) {
    response_json(400, array('message' => 'Weather data not available.', 'error_code' => '400'));
}

response_json(200, $result);
