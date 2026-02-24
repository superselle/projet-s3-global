<?php
/**
 * Endpoint pour supprimer un étudiant
 * Méthode: DELETE ou POST
 * Body: { "idEtudiant": int }
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

// Valider le champ obligatoire
if (!isset($input['idEtudiant']) || empty($input['idEtudiant'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le champ idEtudiant est obligatoire']);
    exit;
}

try {
    $pdo = Connexion::pdo();
    
    if (!$pdo) {
        throw new Exception("Connexion BD échouée");
    }
    
    // Vérifier que l'étudiant existe
    require_once ROOT_PATH . '/model/Etudiant.php';
    $etudiant = Etudiant::getById($input['idEtudiant']);
    if (!$etudiant) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Étudiant non trouvé']);
        exit;
    }
    
    $idUtilisateur = $etudiant->get('id_utilisateur');
    
    $pdo->beginTransaction();
    
    // 1. Supprimer les réponses aux sondages (table ETUDIANT_REPONSE)
    $stmt = $pdo->prepare("DELETE FROM ETUDIANT_REPONSE WHERE id_etudiant = :id");
    $stmt->execute(['id' => $input['idEtudiant']]);
    
    // 2. Supprimer les notes
    $stmt = $pdo->prepare("DELETE FROM NOTE WHERE id_etudiant = :id");
    $stmt->execute(['id' => $input['idEtudiant']]);
    
    // 3. Supprimer l'étudiant
    $stmt = $pdo->prepare("DELETE FROM ETUDIANT WHERE id_etudiant = :id");
    $stmt->execute(['id' => $input['idEtudiant']]);
    
    // 4. Supprimer l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM UTILISATEUR WHERE id_utilisateur = :id");
    $stmt->execute(['id' => $idUtilisateur]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Étudiant supprimé avec succès'
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
