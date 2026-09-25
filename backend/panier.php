<?php


declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = []; // format : [ produit_id => quantite ]
}

$donnees = json_decode(file_get_contents('php://input'), true) ?? [];
$action  = $donnees['action'] ?? 'list';

function repondreAvecPanier(PDO $pdo): void {
    $ids = array_keys($_SESSION['panier']);
    $items = [];
    $total = 0.0;
    $count = 0;

    if ($ids) {
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, nom, prix, image FROM produits WHERE id IN ($marks)");
        $stmt->execute($ids);

        foreach ($stmt->fetchAll() as $produit) {
            $quantite = (int) $_SESSION['panier'][$produit['id']];
            $sousTotal = $produit['prix'] * $quantite;

            $items[] = [
                'id'        => (int) $produit['id'],
                'nom'       => $produit['nom'],
                'prix'      => (float) $produit['prix'],
                'image'     => $produit['image'],
                'quantite'  => $quantite,
                'sousTotal' => round($sousTotal, 2),
            ];

            $total += $sousTotal;
            $count += $quantite;
        }
    }

    echo json_encode([
        'ok'    => true,
        'items' => $items,
        'total' => round($total, 2),
        'count' => $count,
    ]);
}

switch ($action) {

    case 'add':
        $produitId = (int) ($donnees['produit_id'] ?? 0);
        $quantite  = max(1, (int) ($donnees['quantite'] ?? 1));

        // Vérifie que le produit existe vraiment avant de l'ajouter
        $stmt = $pdo->prepare("SELECT id FROM produits WHERE id = ?");
        $stmt->execute([$produitId]);

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'erreur' => 'Produit introuvable.']);
            break;
        }

        $_SESSION['panier'][$produitId] = ($_SESSION['panier'][$produitId] ?? 0) + $quantite;
        repondreAvecPanier($pdo);
        break;

    case 'remove':
        $produitId = (int) ($donnees['produit_id'] ?? 0);
        unset($_SESSION['panier'][$produitId]);
        repondreAvecPanier($pdo);
        break;

    case 'clear':
        $_SESSION['panier'] = [];
        repondreAvecPanier($pdo);
        break;

    case 'list':
    default:
        repondreAvecPanier($pdo);
        break;
}