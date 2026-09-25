<?php



declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['favoris']) || !is_array($_SESSION['favoris'])) {
    $_SESSION['favoris'] = []; // format : [ produit_id, produit_id, ... ]
}

$donnees = json_decode(file_get_contents('php://input'), true) ?? [];
$action  = $donnees['action'] ?? 'list';

function repondreAvecFavoris(PDO $pdo): void {
    $ids = array_values($_SESSION['favoris']);
    $items = [];

    if ($ids) {
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, nom, prix, image FROM produits WHERE id IN ($marks)");
        $stmt->execute($ids);
        $items = $stmt->fetchAll();
    }

    echo json_encode([
        'ok'    => true,
        'items' => $items,
        'count' => count($items),
    ]);
}

switch ($action) {

    case 'add':
        $produitId = (int) ($donnees['produit_id'] ?? 0);

        $stmt = $pdo->prepare("SELECT id FROM produits WHERE id = ?");
        $stmt->execute([$produitId]);

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'erreur' => 'Produit introuvable.']);
            break;
        }

        if (!in_array($produitId, $_SESSION['favoris'], true)) {
            $_SESSION['favoris'][] = $produitId;
        }

        repondreAvecFavoris($pdo);
        break;

    case 'remove':
        $produitId = (int) ($donnees['produit_id'] ?? 0);
        $_SESSION['favoris'] = array_values(array_diff($_SESSION['favoris'], [$produitId]));
        repondreAvecFavoris($pdo);
        break;

    case 'list':
    default:
        repondreAvecFavoris($pdo);
        break;
}