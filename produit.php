<?php


declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/backend/db.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

$estFavori = $produit && in_array($produit['id'], $_SESSION['favoris'] ?? [], true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $produit ? htmlspecialchars($produit['nom']) . ' – Maison de Mode' : 'Produit introuvable – Maison de Mode' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .main-content {
      max-width: 900px;
      margin: 50px auto;
      padding: 0 20px 60px;
    }

    .fil-ariane {
      font-size: 0.82rem;
      color: var(--gris);
      margin-bottom: 24px;
    }

    .fil-ariane a { color: var(--gris); text-decoration: none; }
    .fil-ariane a:hover { color: var(--gold); }

    .produit-detail {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: start;
    }

    .produit-image-principale {
      width: 100%;
      border-radius: 12px;
      background: #fff;
      border: 1px solid var(--border);
      overflow: hidden;
    }

    .produit-image-principale img {
      width: 100%;
      display: block;
      object-fit: cover;
    }

    .produit-info h1 {
      font-family: 'Cormorant Garamond', serif;
      font-style: italic;
      font-size: 2rem;
      color: var(--dark);
      margin-bottom: 10px;
    }

    .produit-prix {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      color: var(--gold);
      margin-bottom: 20px;
    }

    .produit-description {
      color: var(--gris);
      line-height: 1.7;
      margin-bottom: 28px;
    }

    .produit-actions { display: flex; gap: 12px; flex-wrap: wrap; }

    .btn-ajouter {
      padding: 13px 28px;
      background: var(--gold);
      color: #fff;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-size: 0.93rem;
      font-family: var(--font-body);
      font-weight: 500;
      transition: background 0.2s;
    }

    .btn-ajouter:hover { background: var(--gold-light); }
    .btn-ajouter:disabled { opacity: 0.6; cursor: wait; }

    .btn-favori {
      padding: 13px 22px;
      background: transparent;
      border: 1.5px solid var(--border);
      border-radius: 30px;
      cursor: pointer;
      font-size: 0.93rem;
      font-family: var(--font-body);
      transition: all 0.2s;
    }

    .btn-favori.actif { border-color: var(--gold); color: var(--gold); }

    .produit-message {
      margin-top: 14px;
      font-size: 0.88rem;
      color: var(--gris);
      min-height: 1.3em;
    }

    .introuvable {
      text-align: center;
      padding: 60px 20px;
    }

    @media (max-width: 700px) {
      .produit-detail { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- ─── NAVBAR ─── -->
  <nav class="navbar">
    <a href="accueil.html" class="logo">
      <img src="assets/logo.png" alt="Maison de Mode" onerror="this.style.display='none'">
    </a>
    <ul class="nav-links">
      <li><a href="femme.html">Femme</a></li>
      <li><a href="homme.html">Homme</a></li>
      <li><a href="enfant.html">Enfant</a></li>
      <li><a href="promotions.html">Promotions</a></li>
    </ul>
    <div class="search-box">
      <input type="text" placeholder="Rechercher un produit...">
    </div>
    <div class="nav-icons">
      <a href="accueil.html" class="nav-icon-btn" title="Accueil">
        <img src="assets/home.png" alt="Accueil" onerror="this.outerHTML='🏠'">
      </a>
      <div class="nav-icon-btn" title="Panier">
        <img src="assets/panier.png" alt="Panier" onerror="this.outerHTML='🛒'">
        <span class="badge" id="compteur-panier">0</span>
      </div>
      <div class="nav-icon-btn" title="Favoris">
        <img src="assets/favoris.png" alt="Favoris" onerror="this.outerHTML='♡'">
        <span class="badge" id="compteur-favoris">0</span>
      </div>
      <a href="connexion.php" class="nav-icon-btn" title="Mon compte">
        <img src="assets/user.png" alt="Connexion" onerror="this.outerHTML='👤'">
      </a>
    </div>
  </nav>

  <main class="main-content">

    <?php if (!$produit): ?>

      <div class="introuvable">
        <h1>Produit introuvable</h1>
        <p>Ce produit n'existe pas ou n'est plus disponible.</p>
        <p style="margin-top:16px;"><a href="enfant.html" class="btn-favori">Retour au catalogue</a></p>
      </div>

    <?php else: ?>

      <p class="fil-ariane">
        <a href="accueil.html">Accueil</a> ›
        <a href="enfant.html">Enfant</a> ›
        <?= htmlspecialchars($produit['nom']) ?>
      </p>

      <div class="produit-detail">

        <div class="produit-image-principale">
          <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
        </div>

        <div class="produit-info">
          <h1><?= htmlspecialchars($produit['nom']) ?></h1>
          <p class="produit-prix"><?= number_format((float) $produit['prix'], 2) ?> €</p>
          <p class="produit-description"><?= nl2br(htmlspecialchars($produit['description'] ?? '')) ?></p>

          <div class="produit-actions">
            <button type="button" class="btn-ajouter" id="btn-panier" data-id="<?= (int) $produit['id'] ?>">
              Ajouter au panier
            </button>
            <button type="button" class="btn-favori<?= $estFavori ? ' actif' : '' ?>" id="btn-favori" data-id="<?= (int) $produit['id'] ?>">
              <?= $estFavori ? '♥ Dans vos favoris' : '♡ Ajouter aux favoris' ?>
            </button>
          </div>

          <p class="produit-message" id="produit-message" role="status" aria-live="polite"></p>
        </div>

      </div>

    <?php endif; ?>

  </main>

  <footer>
    <p>© 2026 Maison de Mode. Tous droits réservés.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>