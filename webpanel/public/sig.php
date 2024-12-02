<?php
// Nustatome satdump komandą, kad stebėtų 1691 MHz dažnį
$command = "satdump signal --freq 1691e6";

// Vykdome komandą ir gauname išvestį
$output = shell_exec($command);

// Rodo satdump išvestį puslapyje
echo "<pre>$output</pre>";

// Galime atnaujinti puslapį kas kelias sekundes
?>
<html>
<head>
    <meta http-equiv="refresh" content="5"> <!-- Puslapis atsinaujins kas 5 sekundes -->
    <title>Satdump Real-time Monitor</title>
</head>
<body>
    <h1>Satdump Dažnių Monitorius (1691 MHz)</h1>
    <p>Žemiau pateikiami duomenys atnaujinami kas 5 sekundes:</p>
    <pre><?php echo $output; ?></pre>
</body>
</html>