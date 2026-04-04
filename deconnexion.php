<?php
/*
 * deconnexion.php
 *
 * Détruit la session de l'utilisateur connecté et redirige vers connexion.php.
 * Appelé via le lien "Déconnexion" présent dans nav_html().
 */

require_once 'includes/session.php';

detruire_session();
header('Location: connexion.php');
exit;
