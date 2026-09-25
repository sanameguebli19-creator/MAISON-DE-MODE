<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "backend/db.php";

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { session_destroy(); header('Location: connexion.php'); exit(); }

// Récupérer commandes/panier
$stmt = $pdo->prepare("
    SELECT p.id, pr.nom, pr.prix, pi.quantite, p.created_at
    FROM panier p
    JOIN panier_items pi ON pi.panier_id = p.id
    JOIN produits pr ON pr.id = pi.produit_id
    WHERE p.utilisateur_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$commandes = $stmt->fetchAll();

$initiale   = strtoupper(mb_substr($user['nom'], 0, 1));
$msg_succes = '';
$msg_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifier') {
    $nouveau_nom = trim($_POST['nouveau_nom']);
    $nouveau_tel = trim($_POST['nouveau_tel'] ?? '');
    $nouveau_mdp = $_POST['nouveau_mdp'];

    if (empty($nouveau_nom)) {
        $msg_erreur = "Le nom ne peut pas être vide.";
    } else {
        if (!empty($nouveau_mdp)) {
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, telephone=?, mot_de_passe=? WHERE id=?");
            $stmt->execute([$nouveau_nom, $nouveau_tel, $hash, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom=?, telephone=? WHERE id=?");
            $stmt->execute([$nouveau_nom, $nouveau_tel, $_SESSION['user_id']]);
        }
        $_SESSION['nom'] = $nouveau_nom;
        $user['nom']     = $nouveau_nom;
        $initiale        = strtoupper(mb_substr($nouveau_nom, 0, 1));
        $msg_succes      = "Profil mis à jour avec succès !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maison de Mode – Mon compte</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body { background: var(--bg); }

    .main-content {
      max-width: 820px;
      margin: 50px auto;
      padding: 0 20px;
      display: flex;
      flex-direction: column;
      gap: 28px;
    }

    /* ─── CARTE PROFIL ─── */
    .carte-profil {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 28px;
      display: flex;
      align-items: center;
      gap: 22px;
      box-shadow: 0 4px 20px rgba(190,138,5,0.08);
    }

    .avatar {
      width: 68px; height: 68px;
      border-radius: 50%;
      background: var(--gold);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem; color: #fff; flex-shrink: 0;
    }

    .profil-info h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem; color: var(--dark);
      font-style: italic; margin-bottom: 3px;
    }

    .profil-info p { font-size: 0.85rem; color: var(--gris); }

    .profil-actions { margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap; }

    .btn-modifier {
      padding: 8px 18px;
      border: 1.5px solid var(--gold);
      background: transparent; color: var(--gold);
      border-radius: 30px; cursor: pointer;
      font-size: 0.83rem; font-family: var(--font-body);
      font-weight: 500; transition: all 0.2s;
    }

    .btn-modifier:hover { background: var(--gold); color: #fff; }

    .btn-deconnexion {
      padding: 8px 18px;
      border: 1.5px solid #999;
      background: transparent; color: #666;
      border-radius: 30px; cursor: pointer;
      font-size: 0.83rem; font-family: var(--font-body);
      font-weight: 500; transition: all 0.2s;
      text-decoration: none;
    }

    .btn-deconnexion:hover { background: var(--dark); color: #fff; border-color: var(--dark); }

    /* ─── FORMULAIRE MODIFICATION ─── */
    .form-modifier {
      display: none;
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 28px;
      box-shadow: 0 4px 20px rgba(190,138,5,0.08);
    }

    .form-modifier h3 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.3rem; color: var(--gold);
      font-style: italic; margin-bottom: 18px;
    }

    .form-modifier input {
      display: block; width: 100%;
      padding: 10px 14px; margin-bottom: 12px;
      border-radius: 8px; border: 1px solid var(--border);
      font-size: 0.9rem; font-family: var(--font-body);
      color: var(--dark); background: var(--bg);
      outline: none; transition: border-color 0.2s;
    }

    .form-modifier input:focus { border-color: var(--gold); background: #fff; }

    .btn-row { display: flex; gap: 10px; margin-top: 4px; }

    .btn-save {
      padding: 10px 24px;
      background: var(--gold); color: #fff;
      border: none; border-radius: 30px; cursor: pointer;
      font-size: 0.9rem; font-family: var(--font-body);
      font-weight: 500; transition: background 0.2s;
    }

    .btn-save:hover { background: var(--gold-light); }

    .btn-cancel {
      padding: 10px 24px;
      background: transparent; color: var(--gris);
      border: 1px solid var(--border); border-radius: 30px;
      cursor: pointer; font-size: 0.9rem; font-family: var(--font-body);
      transition: all 0.2s;
    }

    .btn-cancel:hover { border-color: var(--gold); color: var(--gold); }

    .msg-ok { background:#e8f5e9; color:#2e7d32; padding:10px 14px; border-radius:8px; margin-bottom:14px; font-size:0.88rem; border-left:3px solid #2e7d32; }
    .msg-err { background:#fce8e8; color:#a84e4e; padding:10px 14px; border-radius:8px; margin-bottom:14px; font-size:0.88rem; border-left:3px solid #a84e4e; }

    /* ─── SECTION COMMANDES ─── */
    .section-titre {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem; color: var(--gold);
      font-style: italic; margin-bottom: 14px;
      display: flex; align-items: center; gap: 10px;
    }

    .badge-count {
      background: var(--gold); color: #fff;
      font-size: 0.72rem; font-family: var(--font-body);
      font-weight: 600; padding: 2px 10px;
      border-radius: 20px; font-style: normal;
    }

    .commande-item {
      background: #fff; border: 1px solid var(--border);
      border-radius: 12px; padding: 16px 20px;
      display: flex; align-items: center; gap: 16px;
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .commande-item:hover { box-shadow: 0 6px 20px rgba(190,138,5,0.10); transform: translateY(-2px); }

    .commande-icone {
      width: 44px; height: 44px; border-radius: 10px;
      background: var(--gold-pale);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.2rem; flex-shrink: 0;
    }

    .commande-info { flex: 1; }
    .commande-info h4 { font-weight: 500; font-size: 0.95rem; color: var(--dark); margin-bottom: 3px; }
    .commande-info p  { font-size: 0.82rem; color: var(--gris); }

    .commande-prix {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem; font-weight: 600; color: var(--gold);
    }

    .etat-vide {
      text-align: center; padding: 50px 20px;
      background: #fff; border: 1px dashed var(--border);
      border-radius: 12px;
    }

    .etat-vide .icone { font-size: 2.5rem; margin-bottom: 12px; }
    .etat-vide p { font-size: 0.9rem; color: var(--gris); margin-bottom: 16px; }

    .btn-shop {
      display: inline-block; background: var(--gold); color: #fff;
      text-decoration: none; padding: 10px 26px;
      border-radius: 30px; font-size: 0.9rem;
      font-family: var(--font-body); font-weight: 500;
      transition: background 0.2s;
    }

    .btn-shop:hover { background: var(--gold-light); }

    /* ─── FOOTER ─── */
    footer {
      background: var(--dark); color: #555;
      padding: 2rem; text-align: center;
      font-size: 0.82rem; margin-top: 20px;
    }

    @media (max-width: 768px) {
      .carte-profil { flex-direction: column; align-items: flex-start; }
      .profil-actions { margin-left: 0; }
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
      <a href="dashboard.php" class="nav-icon-btn active" title="Mon compte">
        <img src="assets/user.png" alt="Mon compte" onerror="this.outerHTML='👤'">
      </a>
    </div>
  </nav>

  <!-- ─── MARQUEE ─── -->
  <div class="marquee-wrap">
    <div class="marquee-track">
      <span class="marquee-item">Livraison offerte dès 120€</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Retours gratuits 30 jours</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Matières premium sélectionnées</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Mode responsable & durable</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Livraison offerte dès 120€</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Retours gratuits 30 jours</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Matières premium sélectionnées</span>
      <span class="marquee-item marquee-dot">·</span>
      <span class="marquee-item">Mode responsable & durable</span>
      <span class="marquee-item marquee-dot">·</span>
    </div>
  </div>

  <!-- ─── CONTENU ─── -->
  <main class="main-content">

    <!-- PROFIL -->
    <div class="carte-profil">
      <div class="avatar"><?= htmlspecialchars($initiale) ?></div>
      <div class="profil-info">
        <h2>Bonjour, <?= htmlspecialchars($user['nom']) ?> 👋</h2>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <?php if (!empty($user['telephone'])): ?>
          <p><?= htmlspecialchars($user['telephone']) ?></p>
        <?php endif; ?>
      </div>
      <div class="profil-actions">
        <button class="btn-modifier" onclick="toggleForm()">✏️ Modifier</button>
        <a href="backend/logout.php" class="btn-deconnexion">🚪 Se déconnecter</a>
      </div>
    </div>

    <!-- FORMULAIRE MODIFICATION -->
    <div class="form-modifier" id="formModifier">
      <h3>✏️ Modifier mon profil</h3>
      <?php if ($msg_succes): ?><div class="msg-ok"><?= $msg_succes ?></div><?php endif; ?>
      <?php if ($msg_erreur): ?><div class="msg-err"><?= $msg_erreur ?></div><?php endif; ?>
      <form method="POST" action="dashboard.php">
        <input type="hidden"   name="action"      value="modifier">
        <input type="text"     name="nouveau_nom"  placeholder="Nom complet" value="<?= htmlspecialchars($user['nom']) ?>" required>
        <input type="tel"      name="nouveau_tel"  placeholder="Téléphone"   value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
        <input type="password" name="nouveau_mdp"  placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)">
        <div class="btn-row">
          <button type="submit" class="btn-save">Sauvegarder</button>
          <button type="button" class="btn-cancel" onclick="toggleForm()">Annuler</button>
        </div>
      </form>
    </div>

    <!-- MES ACHATS -->
    <div>
      <h2 class="section-titre">
        🛍️ Mes achats
        <span class="badge-count"><?= count($commandes) ?></span>
      </h2>

      <?php if (empty($commandes)): ?>
        <div class="etat-vide">
          <div class="icone">👗</div>
          <p>Vous n'avez pas encore passé de commande.</p>
          <a href="enfant.html" class="btn-shop">Découvrir la collection</a>
        </div>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
          <?php foreach ($commandes as $c): ?>
          <div class="commande-item">
            <div class="commande-icone">🛍️</div>
            <div class="commande-info">
              <h4><?= htmlspecialchars($c['nom']) ?></h4>
              <p>Quantité : <?= $c['quantite'] ?> · <?= date('d/m/Y', strtotime($c['created_at'])) ?></p>
            </div>
            <span class="commande-prix"><?= number_format($c['prix'] * $c['quantite'], 2) ?> €</span>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>

  <footer>
    <p>© 2026 Maison de Mode. Tous droits réservés.</p>
  </footer>

  <script>
    function toggleForm() {
      const f = document.getElementById('formModifier');
      f.style.display = f.style.display === 'block' ? 'none' : 'block';
      f.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    <?php if ($msg_succes || $msg_erreur): ?>
      document.getElementById('formModifier').style.display = 'block';
    <?php endif; ?>
  </script>

</body>
</html>