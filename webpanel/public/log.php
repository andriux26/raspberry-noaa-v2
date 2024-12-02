







<?php












// Nustatome kelia iki log failo
$log_file = '/var/log/raspberry-noaa-v2/output.log';

// Patikriname, ar failas egzistuoja
if (file_exists($log_file)) {
    // Nuskaitome visa faila
    $log_content = file_get_contents($log_file);

    // Atvaizduojame failo turini narsykleje
    echo "<pre>";
    echo htmlspecialchars($log_content);
    echo "</pre>";
} else {
    echo "Failas nerastas: $log_file";
}
?>
