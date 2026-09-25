<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <!-- CORRECTION : double balise </title> supprimée -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maison de Mode – Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body { background: var(--bg); }

    .main-content {
      min-height: calc(100vh - 130px);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 16px;
    }

    /* ─── CARTE FORMULAIRE ─── */
    .form-container {
      background: #fff;
      padding: 40px 36px;
      border-radius: 12px;
      width: 400px;
      max-width: 100%;
      text-align: center;
      box-shadow: 0 8px 40px rgba(190,138,5,0.10);
      border: 1px solid var(--border);
    }

    .form-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem;
      color: var(--gold);
      font-style: italic;
      margin-bottom: 1.5rem;
      display: block;
    }

    .form-container h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      color: var(--dark);
      margin-bottom: 6px;
      font-style: italic;
    }

    .form-container .sous-titre {
      font-size: 0.85rem;
      color: var(--gris);
      margin-bottom: 24px;
    }

    /* CORRECTION : styles input/button scoped dans .form-container
       L'original avait des styles globaux qui cassaient la navbar */
    .form-container input {
      display: block;
      width: 100%;
      padding: 11px 14px;
      margin-bottom: 14px;
      border-radius: 8px;
      border: 1px solid var(--border);
      font-size: 0.93rem;
      font-family: var(--font-body);
      color: var(--dark);
      background: var(--bg);
      outline: none;
      transition: border-color 0.2s;
    }

    .form-container input:focus { border-color: var(--gold); background: #fff; }

    .mot-de-passe-oublie {
      display: block;
      text-align: right;
      font-size: 0.80rem;
      color: var(--gris);
      text-decoration: none;
      margin-top: -8px;
      margin-bottom: 16px;
      transition: color 0.2s;
    }

    .mot-de-passe-oublie:hover { color: var(--gold); }

    .form-container button[type="submit"] {
      width: 100%;
      padding: 12px;
      background: var(--gold);
      color: #fff;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-size: 0.93rem;
      font-family: var(--font-body);
      font-weight: 500;
      letter-spacing: 0.06em;
      transition: background-color 0.2s;
      margin-top: 4px;
    }

    .form-container button[type="submit"]:hover { background: var(--gold-light); }

    .separateur {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 20px 0;
      color: #ccc;
      font-size: 0.82rem;
    }

    .separateur::before, .separateur::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border);
    }

    .lien {
      display: block;
      margin-top: 8px;
      color: var(--gris);
      text-decoration: none;
      font-size: 0.88rem;
      transition: color 0.2s;
    }

    .lien:hover { color: var(--gold); }

    .message-erreur {
      background: #fce8e8;
      color: #a84e4e;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 0.88rem;
      border-left: 3px solid #a84e4e;
      text-align: left;
    }

    /* ─── FOOTER ─── */
    footer {
      background: var(--dark);
      color: #fff;
      padding: 2rem;
      text-align: center;
      font-size: 0.82rem;
      color: #555;
    }

    @media (max-width: 768px) {
      .navbar { padding: 10px 16px; flex-wrap: wrap; }
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
      <a href="connexion.php" class="nav-icon-btn active" title="Mon compte">
        <img src="assets/user.png" alt="Connexion" onerror="this.outerHTML='👤'">
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

  <!-- ─── FORMULAIRE ─── -->
  <main class="main-content">
    <div class="form-container">
      <span class="form-logo">Maison de Mode</span>
      <h2>Connexion</h2>
      <p class="sous-titre">Accédez à votre espace personnel</p>

      <?php if (isset($_GET['error'])): ?>
        <div class="message-erreur">❌ Email ou mot de passe incorrect.</div>
      <?php endif; ?>

      <form action="backend/login.php" method="POST">
        <input type="email"    name="email"    placeholder="Adresse email"  required autocomplete="email">
        <input type="password" name="password" placeholder="Mot de passe"   required autocomplete="current-password">
        <a href="#" class="mot-de-passe-oublie">Mot de passe oublié ?</a>
        <button type="submit">Se connecter</button>
      </form>

      <div class="separateur">ou</div>
      <a href="inscription.php" class="lien">Pas encore de compte ? <strong>Créer un compte</strong></a>
    </div>
  </main>

  <footer>
    <p>© 2026 Maison de Mode. Tous droits réservés.</p>
  </footer>

</body>
</html>