<?php
/**
 * API REST - Point d'entrée principal
 * Route les requêtes vers les endpoints appropriés
 */

// Configuration CORS pour permettre les requêtes depuis l'application Java
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Cookie');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Gestion des requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin racine du projet
define('ROOT_PATH', dirname(__DIR__) . '/');

// Changer le répertoire de travail pour que les require_once relatifs fonctionnent
chdir(ROOT_PATH);

// Chargement des configurations
require_once ROOT_PATH . 'config/connexion.php';

// Initialiser la connexion à la base de données
Connexion::connect();

// Récupération de l'endpoint demandé
$endpoint = $_GET['endpoint'] ?? '';

// Vérification de l'existence de l'endpoint
$endpointFile = __DIR__ . '/endpoints/' . $endpoint . '.php';

if (!$endpoint || !file_exists($endpointFile)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint non trouvé',
        'endpoint' => $endpoint
    ]);
    exit;
}

// Inclusion et exécution de l'endpoint
require_once $endpointFile;
