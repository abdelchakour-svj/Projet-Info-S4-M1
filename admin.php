<?php
// Page d'administration
// Accessible uniquement au rôle 'admin'
// Affiche la liste de tous les utilisateurs avec possibilité de les bloquer/débloquer

require_once 'includes/session.php';
require_once 'includes/data.php';

verifier_connexion(['admin']);

// Si l'admin clique sur "Bloquer" ou "Activer" un utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_user_id'])) {
    $id = intval($_POST['toggle_user_id']);
    $user = trouver_utilisateur_par_id($id);
    if ($user) {
        // On inverse le champ actif (true → false, false → true)
        mettre_a_jour_utilisateur($id, ['actif' => !$user['actif']]);
    }
    header('Location: admin.php');
    exit;
}

$utilisateurs = lire_json('utilisateurs.json');
$commandes    = lire_json('commandes.json');

// On compte le nombre de commandes par client
$nb_commandes_par_client = [];
foreach ($commandes as $c) {
    $cid = $c['client_id'];
    if (!isset($nb_commandes_par_client[$cid])) $nb_commandes_par_client[$cid] = 0;
    $nb_commandes_par_client[$cid]++;
}

// Stats globales
$total_users   = count($utilisateurs);
$clients       = array_filter($utilisateurs, fn($u) => $u['role'] === 'client');
$nouveaux      = array_filter($clients, fn($u) => $u['date_inscription'] >= date('Y-m-01'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | L'Île au Fruit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <?= nav_html('admin') ?>
    </header>

    <main>
        <section class="page-header">
            <h1>Administration</h1>
            <h3>Gestion des utilisateurs</h3>
        </section>

        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $total_users ?></div>
                    <p>Utilisateurs totaux</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($nouveaux) ?></div>
                    <p>Nouveaux ce mois</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($commandes) ?></div>
                    <p>Commandes total</p>
                </div>
            </div>
        </section>

        <section class="users-section">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Téléphone</th>
                        <th>Commandes</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $u): ?>
                    <tr <?= !$u['actif'] ? 'style="opacity:0.5;"' : '' ?>>
                        <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= htmlspecialchars($u['login']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['telephone']) ?></td>
                        <td><?= $nb_commandes_par_client[$u['id']] ?? 0 ?></td>
                        <td><?= $u['actif'] ? '✅ Actif' : '🔒 Bloqué' ?></td>
                        <td>
                            <?php if ($u['id'] != $_SESSION['user_id']): // on ne peut pas se bloquer soi-même ?>
                            <form method="POST" action="admin.php" style="display:inline;">
                                <input type="hidden" name="toggle_user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn-voir"
                                    onclick="return confirm('<?= $u['actif'] ? 'Bloquer' : 'Activer' ?> cet utilisateur ?')">
                                    <?= $u['actif'] ? '🔒 Bloquer' : '✅ Activer' ?>
                                </button>
                            </form>
                            <?php else: ?>
                                <em style="color:#aaa;">Vous</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 L'Île au Fruit - Tous droits réservés.</p>
        <p>123 Rue des Fruits, 75000 Paris | Tél : 01 23 45 67 89 | Email : contact@ileaufruit.fr</p>
    </footer>
</body>
</html>
