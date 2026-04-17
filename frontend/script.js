

// ================= CAROUSEL =================
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let current = 0;

function goTo(index) {
    if (index >= slides.length) index = 0;
    if (index < 0) index = slides.length - 1;

    slides[current].classList.remove('active');
    dots[current].classList.remove('active');

    current = index;

    slides[current].classList.add('active');
    dots[current].classList.add('active');
}

setInterval(() => { goTo(current + 1); }, 3000);
dots.forEach((dot, i) => {
    dot.addEventListener('click', () => goTo(i));
});
function filtrer(categorie, element) {

    let produits = document.querySelectorAll('.produit');

    produits.forEach(p => {
        let cat = p.getAttribute('data-category');

        if (categorie === 'all' || cat === categorie) {
            p.style.display = "block";
        } else {
            p.style.display = "none";
        }
    });

    // bouton actif
    document.querySelectorAll('.filtre-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    element.classList.add('active');

    // affichage saison si robes
    afficherSaisons(categorie);
}
function afficherSaisons(categorie) {

    let container = document.querySelector('.produits-wrap');

    // supprimer anciens titres
    document.querySelectorAll('.titre-saison').forEach(t => t.remove());

    if (categorie !== 'robes') {
        document.querySelectorAll('.produit').forEach(p => p.style.display = "block");
        return;
    }

    let saisons = ["printemps", "ete", "automne", "hiver"];

    saisons.forEach(saison => {

        let produitsSaison = [...document.querySelectorAll('.produit')]
            .filter(p => p.dataset.category === "robes" && p.dataset.saison.includes(saison));

        if (produitsSaison.length === 0) return;

        let titre = document.createElement('h2');

        if (saison === "printemps") titre.textContent = "🌸 Printemps";
        if (saison === "ete") titre.textContent = "☀️ Été";
        if (saison === "automne") titre.textContent = "🍂 Automne";
        if (saison === "hiver") titre.textContent = "❄️ Hiver";

        titre.classList.add('titre-saison');
        container.appendChild(titre);

        produitsSaison.forEach(p => {
            container.appendChild(p);
            p.style.display = "block";
        });

    });
}


// ================= POPUP PRODUIT =================
function ouvrirProduit(nom, prix, images, description, ages, couleurs) {
    document.getElementById('popup-nom').textContent = nom;
    document.getElementById('popup-prix').textContent = prix;
    document.getElementById('popup-desc').textContent = description;
    document.getElementById('popup-ages').textContent = ages;
    document.getElementById('popup-couleurs').textContent = couleurs;

    // image principale
    document.getElementById('popup-img').src = images[0];

    // miniatures
    const thumbnails = document.getElementById('popup-thumbnails');
    thumbnails.innerHTML = '';
    images.forEach((imgSrc, i) => {
        const mini = document.createElement('img');
        mini.src = imgSrc;
        mini.style = 'width:60px; height:60px; object-fit:cover; border-radius:6px; cursor:pointer; border:2px solid ' + (i === 0 ? '#be8a05' : '#ddd');
        mini.onclick = function() {
            document.getElementById('popup-img').src = imgSrc;
            document.querySelectorAll('#popup-thumbnails img').forEach(m => m.style.borderColor = '#ddd');
            mini.style.borderColor = '#be8a05';
        };
        thumbnails.appendChild(mini);
    });

    // ouvre le dialog ✅ une seule ligne !
    document.getElementById('ma-popup').showModal();
}

function choisirTaille(btn) {
    document.querySelectorAll('#ma-popup button').forEach(b => {
        b.style.background = '';
        b.style.color = '';
        b.style.borderColor = '#ddd';
    });
    btn.style.background = '#be8a05';
    btn.style.color = '#fff';
    btn.style.borderColor = '#be8a05';
}

let panierCount = 0;
function ajouterPanier() {
    panierCount++;
    document.getElementById('compteur-panier').textContent = panierCount;
    document.getElementById('compteur-panier').style.display = 'flex';
    alert('✅ Article ajouté au panier !');
}

let favorisCount = 0;
function ajouterFavoris() {
    favorisCount++;
    document.getElementById('compteur-favoris').textContent = favorisCount;
    document.getElementById('compteur-favoris').style.display = 'flex';
    alert('❤️ Article ajouté aux favoris !');
}

// fermer en cliquant dehors
document.getElementById('ma-popup').addEventListener('click', function(e) {
    if (e.target === this) this.close();
});


  


