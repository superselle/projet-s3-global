<?php
/**
 * Endpoint pour ajouter un nouvel étudiant
 * Méthode: POST
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

// Valider les champs obligatoires
$required = ['nom', 'prenom', 'email', 'login', 'motDePasse', 'genre', 'typeBac', 'idPromotion'];
foreach ($required as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Le champ $field est obligatoire"]);
        exit;
    }
}

try {
    $pdo = Connexion::pdo();
    
    if (!$pdo) {
        throw new Exception("Connexion BD échouée");
    }
    
    // Décomposer l'ID de promotion (format: "ANNEE|SEMESTRE|PARCOURS")
    $promotionParts = explode('|', $input['idPromotion']);
    if (count($promotionParts) !== 3) {
        throw new Exception("Format d'ID de promotion invalide");
    }
    
    list($annee, $semestre, $idParcours) = $promotionParts;
    
    // Récupérer l'ID du type de bac
    require_once ROOT_PATH . '/model/TypeBac.php';
    $typeBac = TypeBac::findByNom($input['typeBac']);
    if (!$typeBac) {
        throw new Exception("Type de bac invalide: " . $input['typeBac']);
    }
    
    // Récupérer l'ID de la mention (si fournie, sinon utiliser PASS = Passable)
    $idMentionBac = 'PASS'; // Valeur par défaut
    if (!empty($input['mentionBac'])) {
        require_once ROOT_PATH . '/model/MentionBac.php';
        $mentionBac = MentionBac::findByNom($input['mentionBac']);
        if ($mentionBac) {
            $idMentionBac = $mentionBac['idMentionBac'];
        }
    }
    
    // Vérifier si l'email existe déjà
    require_once ROOT_PATH . '/model/Utilisateur.php';
    $existant = Utilisateur::findByEmail($input['email']);
    if ($existant) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Un utilisateur avec cet email existe déjà']);
        exit;
    }
    
    // Vérifier si le login existe déjà
    $existant = Utilisateur::findByLogin($input['login']);
    if ($existant) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Un utilisateur avec ce login existe déjà']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // 1. Créer l'utilisateur
    $mdpHash = password_hash($input['motDePasse'], PASSWORD_DEFAULT);
    $login = $input['login'];
    
    $stmt = $pdo->prepare("INSERT INTO UTILISATEUR (nom_utilisateur, prenom_utilisateur, mail_utilisateur, login_utilisateur, mdp_hash_utilisateur, genre_utilisateur, statut_utilisateur) 
               VALUES (:nom, :prenom, :email, :login, :mdp, :genre, 'ETUDIANT')");
    
    $stmt->execute([
        'nom' => $input['nom'],
        'prenom' => $input['prenom'],
        'email' => $input['email'],
        'login' => $login,
        'mdp' => $mdpHash,
        'genre' => $input['genre']
    ]);
    
    $idUtilisateur = $pdo->lastInsertId();
    
    // 2. Créer l'étudiant
    $stmt = $pdo->prepare("INSERT INTO ETUDIANT (id_utilisateur, id_parcours, id_type, id_mention, est_redoublant, est_anglophone, est_apprenti) 
               VALUES (:idUser, :parcours, :type, :mention, :redoublant, :anglophone, :apprenti)");
    
    $stmt->execute([
        'idUser' => $idUtilisateur,
        'parcours' => $idParcours,
        'type' => $typeBac['idTypeBac'],
        'mention' => $idMentionBac,
        'redoublant' => isset($input['estRedoublant']) && $input['estRedoublant'] ? 1 : 0,
        'anglophone' => isset($input['estAnglophone']) && $input['estAnglophone'] ? 1 : 0,
        'apprenti' => isset($input['estApprenti']) && $input['estApprenti'] ? 1 : 0
    ]);
    
    $idEtudiant = $pdo->lastInsertId();
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Étudiant ajouté avec succès',
        'data' => [
            'idEtudiant' => $idEtudiant,
            'idUtilisateur' => $idUtilisateur
        ]
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
