<?php
// On s'assure que la session est active pour lire les infos utilisateur
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Récupération sécurisée des données de session (Nouvelle Architecture)
// Si le contrôleur a déjà passé ces variables, on les utilise, sinon on pioche en session
$isConnected = $isConnected ?? ($_SESSION['is_connected'] ?? false);
$userRole = $userRole ?? ($_SESSION['role'] ?? '');

// 2. Construction du nom d'affichage (Prénom + Nom)
if (!isset($userName)) {
    $prenom = $_SESSION['user_prenom'] ?? '';
    $nom = $_SESSION['user_nom'] ?? '';
    $userName = trim($prenom . ' ' . $nom);
    
    // Fallback si vide
    if (empty($userName)) {
        $userName = $_SESSION['user_email'] ?? 'Utilisateur';
    }
}

// 3. Gestion du lien du Logo (Redirection intelligente)
// Par défaut : page de connexion
$brandHref = 'index.php?controller=auth&action=connexion';

if ($isConnected) {
    switch ($userRole) {
        case 'etudiant':
            $brandHref = 'index.php?controller=etudiant&action=dashboard';
            break;
        case 'enseignant':
            $brandHref = 'index.php?controller=enseignant&action=dashboard';
            break;
        case 'responsable_filiere':
            $brandHref = 'index.php?controller=responsableFiliere&action=dashboard';
            break;
        case 'responsable_formation':
            $brandHref = 'index.php?controller=responsableFormation&action=dashboard';
            break;
        default:
            $brandHref = 'index.php?controller=profil&action=infos';
            break;
    }
}

// Titre par défaut
$pageTitle = $pageTitle ?? 'Plateforme Pédagogique - SAE S301';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <link rel="stylesheet" href="public/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a class="header-brand" href="<?= $brandHref ?>">
                <span class="brand-line-small">université</span>
                <span class="brand-line-main">PARIS-SACLAY</span>
                <span class="brand-line-sub">IUT D'ORSAY</span>
            </a>
            
            <div class="header-right">
                <?php if ($isConnected): ?>
                    <div class="header-user-info">
                        <span class="header-user-name">
                            <i class="fa fa-user-circle"></i> <?= htmlspecialchars($userName) ?>
                        </span>
                        <?php if (!empty($userRole)): ?>
                            <span class="header-user-role badge badge-secondary">
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $userRole))) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <a href="index.php?controller=auth&action=deconnexion" class="btn-header">
                        Déconnexion
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=auth&action=connexion" class="btn-header">
                        Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>