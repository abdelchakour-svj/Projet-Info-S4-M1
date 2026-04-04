<?php
// Fonctions pour lire et écrire les fichiers JSON du dossier data/
// On utilise ces fonctions dans toutes les pages pour ne pas répéter le code

function lire_json($fichier) {
    $chemin = __DIR__ . '/../data/' . $fichier;
    $contenu = file_get_contents($chemin);
    return json_decode($contenu, true);
}

function ecrire_json($fichier, $data) {
    $chemin = __DIR__ . '/../data/' . $fichier;
    file_put_contents($chemin, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Cherche un utilisateur par son email dans utilisateurs.json
function trouver_utilisateur_par_login($login) {
    $utilisateurs = lire_json('utilisateurs.json');
    foreach ($utilisateurs as $u) {
        if ($u['login'] === $login) {
            return $u;
        }
    }
    return false; // pas trouvé
}

// Cherche un utilisateur par son id
function trouver_utilisateur_par_id($id) {
    $utilisateurs = lire_json('utilisateurs.json');
    foreach ($utilisateurs as $u) {
        if ($u['id'] == $id) {
            return $u;
        }
    }
    return false;
}

// Ajoute un nouvel utilisateur dans le fichier JSON
function ajouter_utilisateur($nouvel_user) {
    $utilisateurs = lire_json('utilisateurs.json');
    
    // On génère un id automatiquement (max existant + 1)
    $max_id = 0;
    foreach ($utilisateurs as $u) {
        if ($u['id'] > $max_id) $max_id = $u['id'];
    }
    $nouvel_user['id'] = $max_id + 1;
    
    $utilisateurs[] = $nouvel_user;
    ecrire_json('utilisateurs.json', $utilisateurs);
}

// Modifie certains champs d'un utilisateur existant
function mettre_a_jour_utilisateur($id, $nouvelles_valeurs) {
    $utilisateurs = lire_json('utilisateurs.json');
    foreach ($utilisateurs as &$u) {
        if ($u['id'] == $id) {
            foreach ($nouvelles_valeurs as $cle => $val) {
                $u[$cle] = $val;
            }
            break;
        }
    }
    ecrire_json('utilisateurs.json', $utilisateurs);
}

// Retourne toutes les commandes d'un client donné
function commandes_du_client($client_id) {
    $commandes = lire_json('commandes.json');
    $resultat = [];
    foreach ($commandes as $c) {
        if ($c['client_id'] == $client_id) {
            $resultat[] = $c;
        }
    }
    return $resultat;
}

// Modifie certains champs d'une commande existante
function mettre_a_jour_commande($id, $nouvelles_valeurs) {
    $commandes = lire_json('commandes.json');
    foreach ($commandes as &$c) {
        if ($c['id'] == $id) {
            foreach ($nouvelles_valeurs as $cle => $val) {
                $c[$cle] = $val;
            }
            break;
        }
    }
    ecrire_json('commandes.json', $commandes);
}

// Ajoute une nouvelle commande dans commandes.json
function ajouter_commande($nouvelle_commande) {
    $commandes = lire_json('commandes.json');
    
    $max_id = 1000;
    foreach ($commandes as $c) {
        if ($c['id'] > $max_id) $max_id = $c['id'];
    }
    $nouvelle_commande['id'] = $max_id + 1;
    
    $commandes[] = $nouvelle_commande;
    ecrire_json('commandes.json', $commandes);
    return $nouvelle_commande['id'];
}

// Cherche un plat par son id dans plats.json
function trouver_plat_par_id($id) {
    $plats = lire_json('plats.json');
    foreach ($plats as $p) {
        if ($p['id'] == $id) return $p;
    }
    return false;
}

// Transforme une liste d'articles en texte lisible, ex: "Smoothie x1, Jus x2"
function noms_articles($articles) {
    $noms = [];
    foreach ($articles as $article) {
        $plat = trouver_plat_par_id($article['plat_id']);
        if ($plat) {
            $noms[] = $plat['nom'] . ' x' . $article['quantite'];
        }
    }
    return implode(', ', $noms);
}
