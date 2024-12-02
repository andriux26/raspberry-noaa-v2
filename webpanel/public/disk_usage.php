<?php
// Gauti laisva vieta diske (baitais)
$disk_free = disk_free_space("/"); // "/" reiskia pagrindini disko skaidini

// Gauti viso disko talpa (baitais)
$disk_total = disk_total_space("/");

// Konvertuoti i zmogui suprantama formata (GB)
$disk_free_gb = round($disk_free / 1024 / 1024 / 1024, 2);
$disk_total_gb = round($disk_total / 1024 / 1024 / 1024, 2);

// Skaiciuojame, kiek procentu vietos uzimta
$disk_used_percent = round((($disk_total - $disk_free) / $disk_total) * 100, 2);

// Graziname JSON formata
header('Content-Type: application/json');
echo json_encode([
    'disk_free_gb' => $disk_free_gb,
    'disk_total_gb' => $disk_total_gb,
    'disk_used_percent' => $disk_used_percent
]);
