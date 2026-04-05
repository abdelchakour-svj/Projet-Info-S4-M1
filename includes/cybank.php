<?php
// Simulation de l'API CYBank
// Dans un vrai projet, ceci serait un appel HTTP à l'API externe.
// Pour les besoins du projet scolaire, on simule un paiement qui réussit toujours.

function cybank_payer($montant, $numero_carte, $expiration, $cvv) {
    // Validation basique du format carte (16 chiffres)
    $carte_propre = preg_replace('/\s+/', '', $numero_carte);
    if (!preg_match('/^\d{16}$/', $carte_propre)) {
        return ['succes' => false, 'message' => 'Numéro de carte invalide (16 chiffres requis)'];
    }
    if (!preg_match('/^\d{2}\/\d{2}$/', $expiration)) {
        return ['succes' => false, 'message' => 'Date d\'expiration invalide (MM/AA)'];
    }
    if (!preg_match('/^\d{3}$/', $cvv)) {
        return ['succes' => false, 'message' => 'CVV invalide (3 chiffres)'];
    }

    // Simulation : on génère un identifiant de transaction
    $transaction_id = 'CYB-' . strtoupper(bin2hex(random_bytes(4)));

    return [
        'succes'         => true,
        'transaction_id' => $transaction_id,
        'montant'        => $montant,
        'message'        => 'Paiement accepté',
    ];
}
