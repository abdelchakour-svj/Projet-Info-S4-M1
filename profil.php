<?php
// Page profil du client connecté
// Affiche ses informations personnelles, son niveau de fidélité et son historique de commandes

require_once 'includes/session.php';
require_once 'includes/data.php';

verifier_connexion(['client']);

$user = trouver_utilisateur_par_id($_SESSION['user_id']);
$commandes = commandes_du_client($_SESSION['user_id']);

// On récupère les initiales pour l'avatar
$initiales = strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1));

// Calcul du niveau de fidélité selon les points
$points = $user['points_fidelite'];
if ($points >= 300)      $niveau = '💎 Platine';
elseif ($points >= 200)  $niveau = '🥇 Gold';
elseif ($points >= 100)  $niveau = '🥈 Argent';
else                     $niveau = '🥉 Bronze';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | L'Île au Fruit</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="profil.css">
</head>
<body>
    <header>
        <?= nav_html('profil') ?>
    </header>

    <main>
        <section class="profil-header">
            <div class="avatar"><?= $initiales ?></div>
            <div>
                <h1><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h1>
                <p>Membre depuis <?= date('F Y', strtotime($user['date_inscription'])) ?></p>
                <span class="badge">⭐ <?= $niveau ?></span>
            </div>
        </section>

        <div class="profil-grid">

            <section class="card">
                <h2>📋 Mes informations</h2>

                <div class="info-ligne">
                    <span>📧 Email</span>
                    <div><span><?= htmlspecialchars($user['login']) ?></span></div>
                </div>
                <div class="info-ligne">
                    <span>📱 Téléphone</span>
                    <div><span><?= htmlspecialchars($user['telephone']) ?></span></div>
                </div>
                <div class="info-ligne">
                    <span>📍 Adresse</span>
                    <div><span><?= htmlspecialchars($user['adresse']) ?></span></div>
                </div>
                <div class="info-ligne">
                    <span>🔔 Code interphone</span>
                    <div><span><?= htmlspecialchars($user['code_interphone']) ?: 'Aucun' ?></span></div>
                </div>
                <div class="info-ligne">
                    <span>🏢 Étage</span>
                    <div><span><?= htmlspecialchars($user['etage']) ?: 'Non précisé' ?></span></div>
                </div>
            </section>

            <section class="card">
                <h2>💎 Compte fidélité</h2>

                <div class="cercle-container">
                    <svg class="cercle-svg" viewBox="0 0 120 120">
                        <circle class="cercle-fond" cx="60" cy="60" r="54"/>
                        <circle class="cercle-progression" cx="60" cy="60" r="54"/>
                    </svg>
                    <div class="cercle-texte">
                        <div class="points"><?= $points ?></div>
                        <div class="label">points</div>
                    </div>
                </div>

                <?php if ($points < 300): ?>
                    <p class="fidelite-info">
                        Plus que <strong><?= 300 - $points ?> points</strong> pour le niveau Platine !
                    </p>
                <?php else: ?>
                    <p class="fidelite-info">🎉 Vous êtes au niveau maximum !</p>
                <?php endif; ?>

                <div class="niveaux">
                    <div class="niveau <?= $points >= 0   ? 'actif' : '' ?>">🥉 Bronze</div>
                    <div class="niveau <?= $points >= 100 ? 'actif' : '' ?>">🥈 Argent</div>
                    <div class="niveau <?= $points >= 200 ? 'actif' : '' ?>">🥇 Gold</div>
                    <div class="niveau <?= $points >= 300 ? 'actif' : '' ?>">💎 Platine</div>
                </div>
            </section>

            <section class="card card-full">
                <h2>📦 Mes commandes</h2>

                <?php if (empty($commandes)): ?>
                    <p style="color:#888; text-align:center; padding:2rem;">Vous n'avez pas encore de commande.</p>
                <?php else: ?>
                    <?php foreach ($commandes as $c): ?>
                        <div class="commande-item">
                            <div class="commande-info">
                                <span class="commande-date"><?= date('d/m/Y à H:i', strtotime($c['date'])) ?></span>
                                <span class="commande-detail"><?= htmlspecialchars(noms_articles($c['articles'])) ?></span>
                            </div>
                            <div class="commande-right">
                                <span class="commande-prix"><?= number_format($c['total'], 2, ',', ' ') ?> €</span>
                                <?php if ($c['statut'] === 'livree'): ?>
                                    <span class="commande-statut livree">✅ Livrée</span>
                                <?php elseif ($c['statut'] === 'en_livraison'): ?>
                                    <span class="commande-statut en-cours">🚴 En livraison</span>
                                <?php else: ?>
                                    <span class="commande-statut">🍽️ En préparation</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

        </div>
    </main>

    <footer>
        <p>&copy; 2026 L'Île au Fruit - Tous droits réservés.</p>
        <p>123 Rue des Fruits, 75000 Paris | Tél : 01 23 45 67 89 | Email : contact@ileaufruit.fr</p>
    </footer>
</body>
</html>
