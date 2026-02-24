<?php
/**
 * API Endpoint - Sauvegarde des affectations étudiants/groupes
 * POST /api/?endpoint=affectations
 * Body: { "affectations": [{"idEtudiant": 1, "idGroupe": 5}, ...] }
 */

// Nettoyer tout output buffer précédent
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require_once ROOT_PATH . 'model/Etudiant.php';
require_once ROOT_PATH . 'model/Groupe.php';
require_once ROOT_PATH . 'config/connexion.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Vérification de la session
if (!isset($_SESSION['is_connected']) || !$_SESSION['is_connected']) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// Vérification du rôle (commentée pour les tests)
// $role = $_SESSION['role'] ?? '';
// if (!in_array($role, ['responsable_filiere', 'responsable_formation'])) {
//     ob_end_clean();
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Accès refusé']);
//     exit;
// }

// Récupération des données JSON
$input = json_decode(file_get_contents('php://input'), true);
$affectations = $input['affectations'] ?? [];

// Log pour debug
error_log("=== AFFECTATIONS PHP ===");
error_log("Input reçu: " . json_encode($input));
error_log("Nombre affectations: " . count($affectations));

if (empty($affectations) || !is_array($affectations)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Traitement des affectations
$pdo = Connexion::pdo();
$pdo->beginTransaction();

try {
    $count = 0;
    
    foreach ($affectations as $affectation) {
        $idEtudiant = $affectation['idEtudiant'] ?? null;
        $idGroupe = $affectation['idGroupe'] ?? null;
        
        error_log("Traitement: Etudiant $idEtudiant -> Groupe $idGroupe");
        
        if ($idEtudiant && $idGroupe) {
            // Passer la connexion PDO pour utiliser la même transaction
            $updated = Etudiant::updateGroupe($idEtudiant, $idGroupe, $pdo);
            error_log("Lignes mises à jour: $updated");
            if ($updated > 0) {
                $count++;
            }
        }
    }
    
    error_log("Total affectations enregistrées: $count");
    $pdo->commit();
    error_log("Transaction commit OK");
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => "$count affectations enregistrées",
        'count' => $count
    ]);
    
} catch (Exception $e) {    error_log("=== ERREUR PHP ===");
    error_log("Message: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
        $pdo->rollBack();
    
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
    ]);
}
