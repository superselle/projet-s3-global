<?php
/**
 * API Endpoint - Authentification
 * POST /api/?endpoint=login
 */

require_once ROOT_PATH . 'model/Utilisateur.php';
require_once ROOT_PATH . 'model/Etudiant.php';
require_once ROOT_PATH . 'model/Enseignant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données JSON
$input = json_decode(file_get_contents('php://input'), true);
$login = $input['login'] ?? '';
$password = $input['password'] ?? '';

if (!$login || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Identifiants manquants']);
    exit;
}

// Vérification des identifiants
$user = Utilisateur::getByLogin($login);

if (!$user || !password_verify($password, $user->get('mdp_hash_utilisateur'))) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    exit;
}

// Création de la session
$_SESSION['is_connected'] = true;
$_SESSION['user_id'] = $user->get('id_utilisateur');
$_SESSION['user_login'] = $user->get('login_utilisateur');
$_SESSION['user_prenom'] = $user->get('prenom_utilisateur');
$_SESSION['user_nom'] = $user->get('nom_utilisateur');

// Détermination du rôle
$role = '';
$roleData = null;

if ($ens = Enseignant::getByIdUtilisateur($user->get('id_utilisateur'))) {
    $roleId = $ens->get('id_role');
    
    if (in_array($roleId, ['RESP_FIL', 'RESP_PED', '2'])) {
        $role = 'responsable_filiere';
    } elseif (in_array($roleId, ['RESP_FORM', '1'])) {
        $role = 'responsable_formation';
    } else {
        $role = 'enseignant';
    }
    
    $roleData = [
        'id_enseignant' => $ens->get('id_enseignant'),
        'id_role' => $roleId
    ];
} elseif ($etu = Etudiant::getByIdUtilisateur($user->get('id_utilisateur'))) {
    $role = 'etudiant';
    $_SESSION['id_etudiant'] = $etu->get('id_etudiant');
    
    $roleData = [
        'id_etudiant' => $etu->get('id_etudiant')
    ];
}

if (!$role) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Aucun rôle associé']);
    exit;
}

$_SESSION['role'] = $role;

// Réponse JSON
echo json_encode([
    'success' => true,
    'message' => 'Connexion réussie',
    'data' => [
        'id' => $user->get('id_utilisateur'),
        'login' => $user->get('login_utilisateur'),
        'nom' => $user->get('nom_utilisateur'),
        'prenom' => $user->get('prenom_utilisateur'),
        'email' => $user->get('mail_utilisateur'),
        'role' => $role,
        'roleData' => $roleData,
        'sessionId' => session_id()
    ]
]);
