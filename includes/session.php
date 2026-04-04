<?php
// Gestion des sessions : connexion, déconnexion, vérification des rôles, navigation
// Ce fichier est inclus en premier dans toutes les pages du site

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si un utilisateur est connecté
function est_connecte() {
    return isset($_SESSION['user_id']);
}

function get_role() {
    return $_SESSION['role'] ?? '';
}

// Enregistre les infos de l'utilisateur dans la session après connexion
function creer_session($utilisateur) {
    $_SESSION['user_id'] = $utilisateur['id'];
    $_SESSION['role']    = $utilisateur['role'];
    $_SESSION['nom']     = $utilisateur['nom'];
    $_SESSION['prenom']  = $utilisateur['prenom'];
    $_SESSION['login']   = $utilisateur['login'];
}

// Vide et détruit la session (déconnexion)
function detruire_session() {
    $_SESSION = [];
    session_destroy();
}

// Bloque l'accès si l'utilisateur n'est pas connecté ou n'a pas le bon rôle
function verifier_connexion($roles = []) {
    if (!est_connecte()) {
        header('Location: connexion.php');
        exit;
    }
    if (!empty($roles) && !in_array(get_role(), $roles)) {
        header('Location: index.php');
        exit;
    }
}

// Génère le menu de navigation HTML selon le rôle de l'utilisateur connecté
function nav_html($page_active = '') {
    $logo = '<div class="logo">
                <a href="index.php">
                    <img src="image/lgoo.png" alt="Retour à l\'accueil">
                </a>
             </div>';

    // Menu pour un visiteur non connecté
    if (!est_connecte()) {
        return '
        <nav>
            ' . $logo . '
            <ul>
                <li><a href="presentation.php">Menu</a></li>
                <li><a href="avis.php">Avis</a></li>
            </ul>
            <a href="connexion.php" class="btn-connexion">Se connecter</a>
        </nav>';
    }

    $role   = get_role();
    $prenom = htmlspecialchars($_SESSION['prenom']);
    $liens  = '';

    if ($role === 'client') {
        $liens = '
            <li><a href="presentation.php">Menu</a></li>
            <li><a href="panier.php">🛒 Panier</a></li>
            <li><a href="profil.php">Mon Profil</a></li>
            <li><a href="avis.php">Avis</a></li>';
    } elseif ($role === 'admin') {
        $liens = '
            <li><a href="admin.php">Admin</a></li>
            <li><a href="commandes.php">Commandes</a></li>';
    } elseif ($role === 'restaurateur') {
        $liens = '<li><a href="commandes.php">Gestion Commandes</a></li>';
    } elseif ($role === 'livreur') {
        $liens = '<li><a href="livraison.php">Ma Livraison</a></li>';
    }

    return '
        <nav>
            ' . $logo . '
            <ul>' . $liens . '</ul>
            <span>Bonjour, ' . $prenom . ' | </span>
            <a href="deconnexion.php" class="btn-connexion">Déconnexion</a>
        </nav>';
}
