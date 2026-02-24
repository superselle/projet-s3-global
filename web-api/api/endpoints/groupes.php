<?php
/**
 * API Endpoint - Liste des groupes d'une promotion
 * GET /api/?endpoint=groupes&promotion=ANNEE|SEM|PARCOURS
 */

require_once ROOT_PATH . 'model/Groupe.php';

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

// Récupération des groupes
$groupes = Groupe::getByPromotion($promotionId);

// Transformation en tableau associatif
$result = [];
foreach ($groupes as $groupe) {
    $result[] = [
        'id' => $groupe->get('id_groupe'),
        'nom' => $groupe->get('nom_groupe'),
        'effectif' => (int)$groupe->get('effectif'),
        'effectifMax' => (int)$groupe->get('effectif_max'),
        'lettre' => $groupe->get('lettre'),
        'parcours' => $groupe->get('id_parcours'),
        'annee' => $groupe->get('annee_scolaire'),
        'semestre' => $groupe->get('semestre')
    ];
}

echo json_encode([
    'success' => true,
    'data' => $result
]);
