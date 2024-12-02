<?php
// Nustatykite pilna kelia iki failo
$configFile = '/home/andrius/.noaa-v2.conf';

// Funkcija nuskaityti esama reiksme
function getCurrentValue($filePath, $key) {
    if (!file_exists($filePath)) {
        die('Failas nerastas: ' . $filePath);
    }

    $fileContent = file_get_contents($filePath);

    if ($fileContent === false) {
        die('Nepavyko perskaityti failo: ' . $filePath);
    }

    // Ieskome rakto reiksmes faile
    if (preg_match('/^' . preg_quote($key, '/') . '=(\d+)/m', $fileContent, $matches)) {
        return intval($matches[1]); // Graziname reiksme kaip skaiciu
    }

    // Jei reiksmes nera, graziname null
    return null;
}

// Funkcija atnaujinti arba prideti reiksme faile
function updateConfig($filePath, $key, $newValue) {
    if (!file_exists($filePath)) {
        die('Failas nerastas: ' . $filePath);
    }

    $fileContent = file_get_contents($filePath);

    if ($fileContent === false) {
        die('Nepavyko perskaityti failo: ' . $filePath);
    }

    // Atvaizduojame arba pridekime nauja reiksme
    if (preg_match('/^' . preg_quote($key, '/') . '=\d+/m', $fileContent)) {
        $updatedContent = preg_replace(
            '/^' . preg_quote($key, '/') . '=\d+/m',
            "{$key}={$newValue}",
            $fileContent
        );
    } else {
        // Jei rakto nera, pridedame ji i failo pabaiga
        $updatedContent = rtrim($fileContent) . "\n{$key}={$newValue}\n";
    }

    // Irasome atnaujinta failo turini
    if (file_put_contents($filePath, $updatedContent) === false) {
        die('Nepavyko issaugoti failo: ' . $filePath);
    }
}

// Palydovu sarasas
$satellites = [
    'NOAA_15', 'NOAA_18', 'NOAA_19', 'METEOR_M2_3', 'METEOR_M2_4'
];

// Inicializuojame dabartines reiksmes
$currentValues = [];

// Nuskaitykite reiksmes is failo
foreach ($satellites as $satellite) {
    $currentValues["{$satellite}_SAT_MIN_ELEV"] = getCurrentValue($configFile, "{$satellite}_SAT_MIN_ELEV");
    $currentValues["{$satellite}_GAIN"] = getCurrentValue($configFile, "{$satellite}_GAIN");
}

// Patikrinkite, ar forma buvo pateikta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        // Apdoroti issaugojima
        foreach ($satellites as $satellite) {
            // Atnaujinti elevacija
            if (isset($_POST["{$satellite}_SAT_MIN_ELEV"])) {
                $elevValue = intval($_POST["{$satellite}_SAT_MIN_ELEV"]);
                if ($elevValue >= 1 && $elevValue <= 90) {
                    updateConfig($configFile, "{$satellite}_SAT_MIN_ELEV", $elevValue);
                }
            }

            // Atnaujinti GAIN
            if (isset($_POST["{$satellite}_GAIN"])) {
                $gainValue = intval($_POST["{$satellite}_GAIN"]);
                if ($gainValue >= 0 && $gainValue <= 49) {
                    updateConfig($configFile, "{$satellite}_GAIN", $gainValue);
                }
            }
        }

        // Persikrauname reiksmes po atnaujinimo
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Pavadinimu formatuojanti funkcija
function formatSatelliteName($satellite) {
    return str_replace('_', '-', ucwords(strtolower($satellite), '_'));
}
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOAA ir METEOR Parametrai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            width: 90%;
        }
        .container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .column {
            width: 48%;
            text-align: center; /* Centruoja teksta ir stulpeli */
        }
        .row {
            margin-bottom: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>NOAA ir METEOR SAT MIN ELEV ir GAIN Redagavimas</h1>
        <form method="post">
            <div class="container">
                <!-- Elevacija Kaireje -->
                <div class="column">
                    <h2>Elevacija</h2>
                    <?php foreach ($satellites as $satellite): ?>
                        <div class="row">
                            <label for="<?php echo $satellite; ?>_SAT_MIN_ELEV"><?php echo formatSatelliteName($satellite); ?>:</label><br>
                            <input 
                                type="range" 
                                id="<?php echo $satellite; ?>_SAT_MIN_ELEV" 
                                name="<?php echo $satellite; ?>_SAT_MIN_ELEV" 
                                min="1" 
                                max="90" 
                                value="<?php echo htmlspecialchars($currentValues["{$satellite}_SAT_MIN_ELEV"]); ?>" 
                                oninput="output<?php echo $satellite; ?>Elev.value = this.value"
                            >
                            <output id="output<?php echo $satellite; ?>Elev"><?php echo htmlspecialchars($currentValues["{$satellite}_SAT_MIN_ELEV"]); ?></output>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- GAIN Desineje -->
                <div class="column">
                    <h2>GAIN</h2>
                    <?php foreach ($satellites as $satellite): ?>
                        <div class="row">
                            <label for="<?php echo $satellite; ?>_GAIN"><?php echo formatSatelliteName($satellite); ?>:</label><br>
                            <input 
                                type="range" 
                                id="<?php echo $satellite; ?>_GAIN" 
                                name="<?php echo $satellite; ?>_GAIN" 
                                min="0" 
                                max="49" 
                                value="<?php echo htmlspecialchars($currentValues["{$satellite}_GAIN"]); ?>" 
                                oninput="output<?php echo $satellite; ?>Gain.value = this.value"
                            >
                            <output id="output<?php echo $satellite; ?>Gain"><?php echo htmlspecialchars($currentValues["{$satellite}_GAIN"]); ?></output>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" name="save">Issaugoti</button>
        </form>
    </div>
</body>
</html>
