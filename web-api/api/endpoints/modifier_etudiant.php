<?php
/**
 * Endpoint pour modifier un étudiant existant
 * Méthode: PUT ou POST
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

// Valider les champs obligatoires
$required = ['idEtudiant', 'nom', 'prenom', 'email', 'login', 'genre', 'typeBac'];
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
    
    // Vérifier que l'étudiant existe
    require_once ROOT_PATH . '/model/Etudiant.php';
    $etudiant = Etudiant::getById($input['idEtudiant']);
    if (!$etudiant) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Étudiant non trouvé']);
        exit;
    }
    
    $idUtilisateur = (int)$etudiant->get('id_utilisateur');
    
    // Vérifier si l'email existe déjà (pour un autre utilisateur)
    require_once ROOT_PATH . '/model/Utilisateur.php';
    $existant = Utilisateur::findByEmail($input['email']);
    // Si l'email existe ET appartient à un autre utilisateur
    if ($existant) {
        $idExistant = isset($existant['id_utilisateur']) ? (int)$existant['id_utilisateur'] : 0;
        if ($idExistant !== 0 && $idExistant !== $idUtilisateur) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Un autre utilisateur avec cet email existe déjà']);
            exit;
        }
    }
    
    // Vérifier si le login existe déjà (pour un autre utilisateur)
    $existant = Utilisateur::findByLogin($input['login']);
    if ($existant) {
        $idExistant = isset($existant['id_utilisateur']) ? (int)$existant['id_utilisateur'] : 0;
        if ($idExistant !== 0 && $idExistant !== $idUtilisateur) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Un autre utilisateur avec ce login existe déjà']);
            exit;
        }
    }
    
    // Récupérer l'ID du type de bac
    require_once ROOT_PATH . '/model/TypeBac.php';
    $typeBac = TypeBac::findByNom($input['typeBac']);
    if (!$typeBac) {
        throw new Exception("Type de bac invalide: " . $input['typeBac']);
    }
    
    // Récupérer l'ID de la mention (si fournie, sinon PASS = Passable)
    $idMentionBac = 'PASS'; // Valeur par défaut
    if (!empty($input['mentionBac'])) {
        require_once ROOT_PATH . '/model/MentionBac.php';
        $mentionBac = MentionBac::findByNom($input['mentionBac']);
        if ($mentionBac) {
            $idMentionBac = $mentionBac['idMentionBac'];
        }
    }
    
    $pdo->beginTransaction();
    
    // 1. Mettre à jour l'utilisateur
    $stmt = $pdo->prepare("UPDATE UTILISATEUR SET 
                nom_utilisateur = :nom,
                prenom_utilisateur = :prenom,
                mail_utilisateur = :email,
                login_utilisateur = :login,
                genre_utilisateur = :genre
                WHERE id_utilisateur = :idUtilisateur");
    
    $stmt->execute([
        'nom' => $input['nom'],
        'prenom' => $input['prenom'],
        'email' => $input['email'],
        'login' => $input['login'],
        'genre' => $input['genre'],
        'idUtilisateur' => $idUtilisateur
    ]);
    
    // 2. Mettre à jour le mot de passe si fourni
    if (!empty($input['motDePasse'])) {
        $stmt = $pdo->prepare("UPDATE UTILISATEUR SET mdp_hash_utilisateur = :mdp WHERE id_utilisateur = :id");
        $stmt->execute([
            'mdp' => password_hash($input['motDePasse'], PASSWORD_DEFAULT),
            'id' => $idUtilisateur
        ]);
    }
    
    // 3. Mettre à jour l'étudiant
    $stmt = $pdo->prepare("UPDATE ETUDIANT SET 
               id_type = :idType,
               id_mention = :idMention,
               est_redoublant = :estRedoublant,
               est_anglophone = :estAnglophone
               WHERE id_etudiant = :idEtudiant");
    
    $stmt->execute([
        'idType' => $typeBac['idTypeBac'],
        'idMention' => $idMentionBac,
        'estRedoublant' => isset($input['estRedoublant']) && $input['estRedoublant'] ? 1 : 0,
        'estAnglophone' => isset($input['estAnglophone']) && $input['estAnglophone'] ? 1 : 0,
        'idEtudiant' => $input['idEtudiant']
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Étudiant modifié avec succès',
        'data' => ['idEtudiant' => $input['idEtudiant']]
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
