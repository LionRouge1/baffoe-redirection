<?php
// Function to get continent code from country code
function getAfricanContinentByCountryCode($countryCode) {
    // African country codes mapped to continent (Africa)
    $africanCountries = [
        'DZ', 'AO', 'BJ', 'BW', 'BF', 'BI', 'CV', 'CM', 'CF', 'TD', 'KM', 'CG', 'CD', 'DJ', 'EG',
        'GQ', 'ER', 'SZ', 'ET', 'GA', 'GM', 'GH', 'GN', 'GW', 'CI', 'KE', 'LS', 'LR', 'LY', 'MG',
        'MW', 'ML', 'MR', 'MU', 'MA', 'MZ', 'NA', 'NE', 'NG', 'RW', 'ST', 'SN', 'SC', 'SL', 'SO',
        'ZA', 'SS', 'SD', 'TZ', 'TG', 'TN', 'UG', 'ZM', 'ZW'
    ];

    $countryCode = strtoupper($countryCode); // normalize case

    if (in_array($countryCode, $africanCountries, true)) {
        return 'AF';
    }
    
    return null; // not in Africa
}

if (isset($_POST['lat'], $_POST['lon'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    
    try {
        // Validate latitude and longitude
        if (!is_numeric($lat) || !is_numeric($lon) || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            throw new Exception("Invalid latitude or longitude values.");
        }

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
            $countryCode = strtoupper($data['address']['country_code']);
            $continent = getAfricanContinentByCountryCode($countryCode);
            if ($continent) {
                setcookie('user_location', $continent, time() + 3600 * 24 * 365, "/");
            } else {
                setcookie('user_location', $$data['address']['country_code'], time() + 3600 * 24 * 365, "/");
            }
        } else {
            throw new Exception("Could not retrieve location data.");
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
    // Reverse geocode using free API

    // if ($data && isset($data['address']['country_code'])) {
    //     $_SESSION['country_code'] = strtoupper($data['address']['country_code']);
    // } else {
    //     $_SESSION['country_code'] = 'US'; // fallback
    // }

    echo "OK";
} else {
    echo "No location data";
}
