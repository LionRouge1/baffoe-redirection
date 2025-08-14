<?php
session_start();

if (isset($_POST['lat'], $_POST['lon'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];

    // Reverse geocode using free API
    $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json";

    $opts = [
        "http" => [
            "header" => "User-Agent: LocationApp/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $response = @file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if ($data && isset($data['address']['country_code'])) {
        $_SESSION['country_code'] = strtoupper($data['address']['country_code']);
    } else {
        $_SESSION['country_code'] = 'US'; // fallback
    }

    echo "OK";
} else {
    echo "No location data";
}
