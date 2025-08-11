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

// Function to get continent code from IP using a free API
function getContinentCode($ip) {
  $url = "https://ipapi.co/{$ip}/continent_code/";
  $continent = @file_get_contents($url);
  return $continent ? trim($continent) : null;
}

if (isset($_COOKIE['user_location'])) {
  $continent = $_COOKIE['user_location'];
} else {
  $ip = getUserIP();
  $continent = getContinentCode($ip);
  if ($continent) {
    setcookie('user_location', $continent, time() + 3600 * 24 * 365, "/");
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
