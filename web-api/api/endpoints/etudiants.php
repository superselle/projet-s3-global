<?php
/**
 * API Endpoint - Liste des étudiants d'une promotion
 * GET /api/?endpoint=etudiants&promotion=ANNEE|SEM|PARCOURS
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

// Récupération du paramètre promotion (format: ANNEE|SEM|PARCOURS)
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

// Transformation en tableau associatif
$result = [];
foreach ($etudiants as $etu) {
    $result[] = [
        'idEtudiant' => $etu->get('id_etudiant'),
        'idUtilisateur' => $etu->get('id_utilisateur'),
        'nom' => $etu->get('nom'),
        'prenom' => $etu->get('prenom'),
        'email' => $etu->get('email'),
        'genre' => $etu->get('genre_utilisateur'),
        'idGroupe' => $etu->get('id_groupe'),
        'nomGroupe' => $etu->get('nom_groupe'),
        'idCovoiturage' => $etu->get('id_binome_souhaite'),
        'estRedoublant' => (bool)$etu->get('est_redoublant'),
        'estAnglophone' => (bool)$etu->get('est_anglophone'),
        'estApprenti' => (bool)$etu->get('est_apprenti'),
        'typeBac' => $etu->get('libelle_type'),
        'mentionBac' => $etu->get('libelle_mention')
    ];
}

echo json_encode([
    'success' => true,
    'data' => $result
]);
