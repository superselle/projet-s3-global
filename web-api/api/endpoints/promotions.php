<?php
/**
 * API Endpoint - Liste des promotions
 * GET /api/?endpoint=promotions
 */

require_once ROOT_PATH . 'model/Promotion.php';
require_once ROOT_PATH . 'config/connexion.php';

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

// Vérification du rôle (uniquement enseignants et responsables)
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['enseignant', 'responsable_filiere', 'responsable_formation'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

try {
    // Récupération des promotions
    $promotions = Promotion::getAll();

    // Transformation en tableau associatif avec statistiques
    $result = [];
    foreach ($promotions as $promo) {
        $promoId = $promo->get('id');
        $parts = explode('|', $promoId);
        
        // Compter les étudiants (via la table GROUPE)
        $sqlEtu = "SELECT COUNT(DISTINCT e.id_etudiant) 
                   FROM ETUDIANT e
                   JOIN GROUPE g ON e.id_groupe = g.id_groupe
                   WHERE g.annee_scolaire = :a AND g.semestre = :s AND g.id_parcours = :p";
        $stmtEtu = Connexion::pdo()->prepare($sqlEtu);
        $stmtEtu->execute(['a' => $parts[0], 's' => $parts[1], 'p' => $parts[2]]);
        $nbEtudiants = $stmtEtu->fetchColumn();
        
        // Compter les groupes
        $sqlGrp = "SELECT COUNT(*) FROM GROUPE g
                   WHERE g.annee_scolaire = :a AND g.semestre = :s AND g.id_parcours = :p";
        $stmtGrp = Connexion::pdo()->prepare($sqlGrp);
        $stmtGrp->execute(['a' => $parts[0], 's' => $parts[1], 'p' => $parts[2]]);
        $nbGroupes = $stmtGrp->fetchColumn();
        
        $result[] = [
            'id' => $promoId,
            'annee' => $promo->get('annee_scolaire'),
            'semestre' => $promo->get('semestre'),
            'parcours' => $promo->get('id_parcours'),
            'nomParcours' => $promo->get('nom_parcours'),
            'label' => $promo->getLabel(),
            'nbEtudiants' => (int)$nbEtudiants,
            'nbGroupes' => (int)$nbGroupes
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $result
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
