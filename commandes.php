<?php
// Page de gestion des commandes (restaurateur et admin)
// Affiche les commandes à préparer et en livraison
// Permet de passer une commande en statut "en_livraison"

require_once 'includes/session.php';
require_once 'includes/data.php';

verifier_connexion(['restaurateur', 'admin']);

// Si le restaurateur clique sur "Passer en livraison"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $id = intval($_POST['commande_id']);
    mettre_a_jour_commande($id, ['statut' => 'en_livraison', 'livreur_id' => 9]); // livreur par défaut
    header('Location: commandes.php');
    exit;
}

// On charge toutes les commandes et on les sépare par statut
$toutes = lire_json('commandes.json');
$a_preparer  = [];
$en_livraison = [];

foreach ($toutes as $c) {
    if ($c['statut'] === 'a_preparer')   $a_preparer[]  = $c;
    if ($c['statut'] === 'en_livraison') $en_livraison[] = $c;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes | L'Île au Fruit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="commandes.css">
</head>
<body>
    <header>
        <?= nav_html('commandes') ?>
    </header>

    <main>
        <section class="page-header">
            <h1>Commandes</h1>
            <h3>Gestion des commandes en cours</h3>
        </section>

        <div class="commandes-container">

            <!-- Colonne : À préparer -->
            <section class="commandes-colonne">
                <div class="colonne-titre a-preparer">
                    <h2>🍽️ À préparer <span class="badge-count"><?= count($a_preparer) ?></span></h2>
                </div>

                <?php if (empty($a_preparer)): ?>
                    <p style="padding:1rem; color:#888;">Aucune commande à préparer.</p>
                <?php endif; ?>

                <?php foreach ($a_preparer as $c):
                    $client = trouver_utilisateur_par_id($c['client_id']);
                    $heure  = date('H:i', strtotime($c['date']));
                ?>
                    <div class="commande-card">
                        <div class="commande-header">
                            <span class="commande-id">#<?= $c['id'] ?></span>
                            <span class="commande-heure"><?= $heure ?></span>
                        </div>
                        <div class="commande-client">👤 <?= $client ? htmlspecialchars($client['prenom'] . ' ' . $client['nom']) : 'Client inconnu' ?></div>
                        <ul class="commande-items">
                            <?php foreach ($c['articles'] as $article):
                                $plat = trouver_plat_par_id($article['plat_id']);
                            ?>
                                <li>× <?= $article['quantite'] ?> <?= $plat ? htmlspecialchars($plat['nom']) : 'Plat #' . $article['plat_id'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="commande-adresse">📍 <?= htmlspecialchars($c['adresse_livraison']) ?></div>

                        <form method="POST" action="commandes.php">
                            <input type="hidden" name="commande_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn-statut btn-livraison">🚴 Passer en livraison</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- Colonne : En livraison -->
            <section class="commandes-colonne">
                <div class="colonne-titre en-livraison">
                    <h2>🚴 En livraison <span class="badge-count"><?= count($en_livraison) ?></span></h2>
                </div>

                <?php if (empty($en_livraison)): ?>
                    <p style="padding:1rem; color:#888;">Aucune commande en livraison.</p>
                <?php endif; ?>

                <?php foreach ($en_livraison as $c):
                    $client = trouver_utilisateur_par_id($c['client_id']);
                    $heure  = date('H:i', strtotime($c['date']));
                ?>
                    <div class="commande-card en-cours">
                        <div class="commande-header">
                            <span class="commande-id">#<?= $c['id'] ?></span>
                            <span class="commande-heure"><?= $heure ?></span>
                        </div>
                        <div class="commande-client">👤 <?= $client ? htmlspecialchars($client['prenom'] . ' ' . $client['nom']) : 'Client inconnu' ?></div>
                        <ul class="commande-items">
                            <?php foreach ($c['articles'] as $article):
                                $plat = trouver_plat_par_id($article['plat_id']);
                            ?>
                                <li>× <?= $article['quantite'] ?> <?= $plat ? htmlspecialchars($plat['nom']) : 'Plat #' . $article['plat_id'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="commande-adresse">📍 <?= htmlspecialchars($c['adresse_livraison']) ?></div>
                        <div class="statut-label">🚴 En cours de livraison...</div>
                    </div>
                <?php endforeach; ?>
            </section>

        </div>
    </main>

    <footer>
        <p>&copy; 2026 L'Île au Fruit - Tous droits réservés.</p>
        <p>123 Rue des Fruits, 75000 Paris | Tél : 01 23 45 67 89 | Email : contact@ileaufruit.fr</p>
    </footer>
</body>
</html>
