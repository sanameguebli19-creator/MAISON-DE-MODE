(() => {
  'use strict';

  /* ================= MENU MOBILE ================= */

  const menuBtn = document.querySelector('.menu-toggle');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      document.querySelector('.nav-links')?.classList.toggle('show');
    });
  }

  /* ================= CARROUSEL ================= */
  /* Uniquement si la page contient des slides (accueil.html) */

  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.dot');

  if (slides.length && dots.length) {
    let current = 0;

    const goTo = (index) => {
      if (index >= slides.length) index = 0;
      if (index < 0) index = slides.length - 1;

      slides[current].classList.remove('active');
      dots[current].classList.remove('active');

      current = index;

      slides[current].classList.add('active');
      dots[current].classList.add('active');
    };

    setInterval(() => goTo(current + 1), 3000);
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
  }

  /* ================= FILTRES CATALOGUE ================= */
  /* Uniquement si la page contient des produits filtrables (enfant.html) */

  window.filtrer = function (categorie, element) {
    document.querySelectorAll('.produit').forEach((p) => {
      const cat = p.getAttribute('data-category');
      p.style.display = (categorie === 'all' || cat === categorie) ? 'block' : 'none';
    });

    document.querySelectorAll('.filtre-btn').forEach((btn) => btn.classList.remove('active'));
    element.classList.add('active');

    afficherSaisons(categorie);
  };

  function afficherSaisons(categorie) {
    const container = document.querySelector('.produits-wrap');
    if (!container) return;

    document.querySelectorAll('.titre-saison').forEach((t) => t.remove());

    if (categorie !== 'robes') {
      document.querySelectorAll('.produit').forEach((p) => (p.style.display = 'block'));
      return;
    }

    const saisons = {
      printemps: '🌸 Printemps',
      ete: '☀️ Été',
      automne: '🍂 Automne',
      hiver: '❄️ Hiver',
    };

    Object.entries(saisons).forEach(([saison, titreTexte]) => {
      const produitsSaison = [...document.querySelectorAll('.produit')]
        .filter((p) => p.dataset.category === 'robes' && p.dataset.saison?.includes(saison));

      if (!produitsSaison.length) return;

      const titre = document.createElement('h2');
      titre.textContent = titreTexte;
      titre.classList.add('titre-saison');
      container.appendChild(titre);

      produitsSaison.forEach((p) => {
        container.appendChild(p);
        p.style.display = 'block';
      });
    });
  }

  /* ================= PANIER & FAVORIS ================= */
  /* Le panier et les favoris vivent en session PHP (backend/panier.php,
     backend/favoris.php) : ça fonctionne avec ou sans compte connecté,
     et sans dupliquer les données entre le navigateur et la base. */

  const compteurPanier = document.getElementById('compteur-panier');
  const compteurFavoris = document.getElementById('compteur-favoris');

  function majBadge(el, count) {
    if (!el) return;
    el.textContent = count;
    el.style.display = count > 0 ? 'flex' : 'none';
  }

  async function appelerApi(url, action, produitId) {
    const reponse = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, produit_id: produitId }),
    });

    if (!reponse.ok) throw new Error('Erreur ' + reponse.status);
    return reponse.json();
  }

  // Rafraîchit les compteurs du panier et des favoris au chargement
  async function initCompteurs() {
    try {
      const [panier, favoris] = await Promise.all([
        appelerApi('backend/panier.php', 'list'),
        appelerApi('backend/favoris.php', 'list'),
      ]);
      majBadge(compteurPanier, panier.count ?? 0);
      majBadge(compteurFavoris, favoris.count ?? 0);
    } catch {
      // Si l'API n'est pas joignable (ex. page ouverte hors serveur PHP), on ignore silencieusement
    }
  }

  if (compteurPanier || compteurFavoris) {
    initCompteurs();
  }

  // Boutons de la fiche produit (produit.php)
  const btnPanier = document.getElementById('btn-panier');
  const btnFavori = document.getElementById('btn-favori');
  const message = document.getElementById('produit-message');

  if (btnPanier) {
    btnPanier.addEventListener('click', async () => {
      const produitId = Number(btnPanier.dataset.id);
      btnPanier.disabled = true;

      try {
        const data = await appelerApi('backend/panier.php', 'add', produitId);
        majBadge(compteurPanier, data.count ?? 0);
        if (message) message.textContent = 'Ajouté au panier.';
      } catch {
        if (message) message.textContent = "L'ajout au panier a échoué. Réessayez.";
      } finally {
        btnPanier.disabled = false;
      }
    });
  }

  if (btnFavori) {
    btnFavori.addEventListener('click', async () => {
      const produitId = Number(btnFavori.dataset.id);
      const estActif = btnFavori.classList.contains('actif');
      const action = estActif ? 'remove' : 'add';

      try {
        const data = await appelerApi('backend/favoris.php', action, produitId);
        majBadge(compteurFavoris, data.count ?? 0);
        btnFavori.classList.toggle('actif', !estActif);
        btnFavori.textContent = !estActif ? '♥ Dans vos favoris' : '♡ Ajouter aux favoris';
      } catch {
        if (message) message.textContent = "L'action a échoué. Réessayez.";
      }
    });
  }
})();