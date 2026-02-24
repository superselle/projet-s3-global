<?php
/**
 * API Endpoint - Déconnexion
 * POST /api/?endpoint=logout
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Destruction de la session
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Déconnexion réussie'
]);
