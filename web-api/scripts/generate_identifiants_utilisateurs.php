<?php
// Génère un fichier récapitulatif (login + mdp en clair + statut) à partir de scripts/seed_utilisateurs_data.php

$dataFile = __DIR__ . '/seed_utilisateurs_data.php';
$outFile = __DIR__ . '/../info-bd/identifiants_utilisateurs.csv';

if (!file_exists($dataFile)) {
    echo "Fichier manquant: scripts/seed_utilisateurs_data.php\n";
    exit;
}

$users = require $dataFile;

$lines = array();
$lines[] = 'login;motdepasse;statut;prenom;nom;mail';

foreach ($users as $u) {
    $statut = isset($u['statut']) ? $u['statut'] : '';
    $lines[] = $u['login'] . ';' . $u['motdepasse'] . ';' . $statut . ';' . $u['prenom'] . ';' . $u['nom'] . ';' . $u['mail'];
}

file_put_contents($outFile, implode("\n", $lines));

echo 'OK: ' . count($users) . ' lignes écrites dans info-bd/identifiants_utilisateurs.csv' . "\n";
