<?php
// Function to get user's IP address
function getUserIP() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    return $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
  } else {
    return $_SERVER['REMOTE_ADDR'];
  }
}

function isUsingVPN($proxy, $hosting, $isp) {
  // Use a free VPN detection API (e.g., ip-api.com)
  // Note: Free APIs may have rate limits and reliability issues.
  // $url = "http://ip-api.com/json/{$ip}?fields=continentCode,proxy,hosting,mobile";
  // $response = @file_get_contents($url);
  // if ($response === false) {
  //   return false; // Could not determine, assume not VPN
  // }
  // $data = json_decode($response, true);
  // 'proxy' true means VPN/proxy/Tor, 'hosting' true means datacenter
  $normalizedIsp = trim($isp);

  // Regex with all keywords (case-insensitive)
  $pattern = '/vpn|proxy|anonymizer/i';

  // Check if any keyword matches
  $ispMatches = preg_match($pattern, $normalizedIsp) === 1;

  if (!empty($proxy) || !empty($hosting) || $ispMatches) {
    return true;
  }
  return false;
}


// Function to get continent code from IP using a free API
function getContinentCode($ip) {
  // Handle localhost and private IPs
  if ($ip === '127.0.0.1' || $ip === '::1' ||
      filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
    // Default to a continent code, e.g., 'AF' for Africa or 'EU' for Europe, or null
    return 'AF'; // Change this to your preferred default
  }

  // $url = "https://ipapi.co/{$ip}/continent_code/";
  $url = "http://ip-api.com/json/{$ip}?fields=continentCode,isp,proxy,hosting";
  $response = @file_get_contents($url);
  if ($response === false) {
    return null; // Could not determine continent
  }
  $data = json_decode($response, true);
  // $continent = @file_get_contents($url);
  $continent = $data['continentCode'] ?? null;
  $proxy = $data['proxy'] ?? false;
  $hosting = $data['hosting'] ?? false;
  $isp = $data['isp'] ?? '';

  return array(
    'continent' => $continent,
    'proxy' => $proxy,
    'hosting' => $hosting,
    'isp' => $mobile
  );
}

if (isset($_COOKIE['user_location'])) {
  $continent = $_COOKIE['user_location'];
} else {
  $ip = getUserIP();
  $data = getContinentCode($ip);
  $continent = $data['continent'];
  $proxy = $data['proxy'];
  $hosting = $data['hosting'];
  $isp = $data['isp'];
  if (isUsingVPN($proxy, $hosting, $isp)) {
    // If using VPN, redirect to get browser location
    header('Location: /vpn-detected.php');
    exit;
    // $continent = 'VPN'; // Change this to your preferred default
  } else {
    if ($continent) {
      setcookie('user_location', $continent, time() + 3600 * 24 * 365, "/");
    }
  }
}

// if ($continent === 'AF') {
//   header('Location: https://africa.baffopolo.com');
//   exit;
// }

// Continue with the rest of your site if not Africa
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>baffo</title>
</head>
<body>
  <?php if ($continent === 'AF'): ?>
    <h1>Welcome to Baffo Africa!</h1>
    <p>We are glad to have you here.</p>
  <?php else: ?>
    <h1>Welcome to Baffo!</h1>
    <p>Explore our content tailored for your region.</p>
  <?php endif; ?>
  <p>Your continent code is: <?php echo htmlspecialchars($continent); ?></p>
  <p>Your IP address is: <?php echo htmlspecialchars(getUserIP()); ?></p>
  <p>Thank you for visiting our site!</p>
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Baffo. All rights reserved.</p>
  </footer> 
</body>
</html>
