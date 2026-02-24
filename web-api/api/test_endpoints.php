<?php
/**
 * Script de test pour vérifier les endpoints API
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Test des Endpoints API</h1>";
echo "<p>Base URL: " . dirname($_SERVER['PHP_SELF']) . "/</p>";

$endpoints = [
    'login',
    'logout',
    'promotions',
    'etudiants',
    'groupes',
    'affectations',
    'statistiques',
    'ajouter_etudiant',
    'modifier_etudiant',
    'supprimer_etudiant'
];

echo "<h2>Fichiers d'endpoints présents :</h2>";
echo "<ul>";
foreach ($endpoints as $endpoint) {
    $file = __DIR__ . '/endpoints/' . $endpoint . '.php';
    $exists = file_exists($file);
    $color = $exists ? 'green' : 'red';
    $status = $exists ? '✓ PRÉSENT' : '✗ MANQUANT';
    
    echo "<li style='color: $color'><strong>$endpoint</strong>: $status";
    if ($exists) {
        echo " (" . filesize($file) . " octets)";
    }
    echo "</li>";
}
echo "</ul>";

echo "<h2>Configuration serveur :</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</li>";
echo "<li>Request URI: " . $_SERVER['REQUEST_URI'] . "</li>";
echo "</ul>";

echo "<h2>Test d'accès direct aux endpoints :</h2>";
echo "<ul>";
foreach ($endpoints as $endpoint) {
    $url = "index.php?endpoint=$endpoint";
    echo "<li><a href='$url' target='_blank'>$endpoint</a></li>";
}
echo "</ul>";

?>
