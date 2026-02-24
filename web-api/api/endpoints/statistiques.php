<?php
/**
 * API Endpoint - Statistiques d'une promotion
 * GET /api/?endpoint=statistiques&promotion=ANNEE|SEM|PARCOURS
 */

require_once ROOT_PATH . 'model/Etudiant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérification de la session
if (!isset($_SESSION['is_connected']) || !$_SESSION['is_connected']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// Vérification du rôle
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['enseignant', 'responsable_filiere', 'responsable_formation'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

// Récupération du paramètre promotion
$promotionId = $_GET['promotion'] ?? '';
if (!$promotionId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Paramètre promotion manquant']);
    exit;
}

// Décomposition de l'ID composite
$parts = explode('|', $promotionId);
if (count($parts) !== 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format de promotion invalide']);
    exit;
}

list($annee, $semestre, $parcours) = $parts;

// Récupération des étudiants
$etudiants = Etudiant::getAllByPromo($annee, $parcours);

// Calcul des statistiques
$stats = [
    'total' => count($etudiants),
    'nbFilles' => 0,
    'nbGarcons' => 0,
    'nbRedoublants' => 0,
    'nbAnglophones' => 0,
    'nbApprentis' => 0
];

foreach ($etudiants as $etu) {
    if ($etu->get('genre_utilisateur') === 'F') {
        $stats['nbFilles']++;
    } else {
        $stats['nbGarcons']++;
    }
    
    if ($etu->get('est_redoublant')) {
        $stats['nbRedoublants']++;
    }
    
    if ($etu->get('est_anglophone')) {
        $stats['nbAnglophones']++;
    }
    
    if ($etu->get('est_apprenti')) {
        $stats['nbApprentis']++;
    }
}

echo json_encode([
    'success' => true,
    'data' => $stats
]);
