<?php
// Nustatykite pagrindini kataloga
$base_dir = '/srv/audio';

// Gaukite dabartini kataloga is URL arba naudokite pagrindini kataloga
$current_dir = isset($_GET['dir']) ? realpath($base_dir . '/' . $_GET['dir']) : $base_dir;

// Patikrinkite, ar dabartinis katalogas yra galioje pagrindinio katalogo ribose
if (strpos($current_dir, $base_dir) !== 0) {
    die('Neleidziama prieiga prie katalogo.');
}

// Failo istrynimo funkcija
if (isset($_GET['delete'])) {
    $file_to_delete = realpath($base_dir . '/' . $_GET['delete']);
    if (strpos($file_to_delete, $base_dir) !== 0 || !file_exists($file_to_delete)) {
        die('Neleidziama prieiga prie failo arba failas neegzistuoja.');
    }
    if (unlink($file_to_delete)) {
        echo "<script>alert('Failas sekmingai istrintas!');</script>";
    } else {
        echo "<script>alert('Nepavyko istrinti failo.');</script>";
    }
}

// Failo atsisiuntimo funkcija
if (isset($_GET['download'])) {
    $file_to_download = realpath($base_dir . '/' . $_GET['download']);
    if (strpos($file_to_download, $base_dir) !== 0 || !file_exists($file_to_download)) {
        die('Neleidziama prieiga prie failo.');
    }
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
    header('Content-Length: ' . filesize($file_to_download));
    readfile($file_to_download);
    exit;
}

// Gaukite katalogo turini
$files = scandir($current_dir);

// HTML pradzia su baziniu puslapiu
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' type='text/css' href='/assets/css/header.css'>
    <link rel='stylesheet' type='text/css' href='/assets/css/footer.css'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css'>
    <title>Raspberry NOAA V2</title>
</head>
<body>
<header class='mb-3'>
  <div class='navbar navbar-expand navbar-dark bg-dark'>
    <ul class='navbar-nav mr-auto'>
      <li class='nav-item'>
        <a class='nav-link' href='/passes'>Passes</a>
      </li>
      <li class='nav-item'>
        <a class='nav-link' href='/captures?page_no=1'>Captures</a>
      </li>
      <li class='nav-item active'>
        <a class='nav-link' href='/audio.php'>File</a>
      </li>
      <li class='nav-item'>
        <a class='nav-link' href='/admin/passes'>Admin</a>
      </li>
    </ul>
    <span class='navbar-text hdd-space ml-3'>
      <em>HDD: <span id='disk-free'></span> <span id='disk-total'></span>
      <span id='disk-percent'>Loading...</span></em>
    </span>
  </div>
</header>
<div class='container'>
    <h1>Failu narsykle</h1>
    <p>Dabartinis katalogas: $current_dir</p>";

// Grizti aukstyn nuoroda
if ($current_dir !== $base_dir) {
    $parent_dir = dirname(str_replace($base_dir, '', $current_dir));
    echo "<p><a href='?dir=" . urlencode($parent_dir) . "'>Grizti i tevini kataloga</a></p>";
}

echo "<ul>";
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;

    $file_path = $current_dir . '/' . $file;
    $relative_path = str_replace($base_dir . '/', '', $file_path);

    // Tikriname, ar tai yra failas ar katalogas
    if (is_dir($file_path)) {
        echo "<li><a href='?dir=" . urlencode($relative_path) . "'>$file</a></li>";
    } else {
        // Gauname failo dydi MB formatu
        $file_size = filesize($file_path) / 1024 / 1024; // Baitus paverciame i MB
        $file_size_formatted = number_format($file_size, 2) . ' MB';

        echo "<li>$file ($file_size_formatted)
                <a href='?download=" . urlencode($relative_path) . "'>Atsisiusti</a>
                <a href='?delete=" . urlencode($relative_path) . "' onclick=\"return confirm('Ar tikrai norite istrinti si faila?');\">Istrinti</a>
              </li>";
    }
}
echo "</ul>
</div>
<footer class='footer text-center'>
  <div class='container'>
    <a href='https://github.com/jekhokie/raspberry-noaa-v2'>
      <img class='img-footer' src='/assets/web_img/logo-small-v2.png' alt='logo'>
    </a>
  </div>
</footer>
<script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
