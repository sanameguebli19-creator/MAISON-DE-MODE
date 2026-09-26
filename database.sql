

CREATE DATABASE IF NOT EXISTS maison_de_mode
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE maison_de_mode;

-- ---------- Utilisateurs ----------
CREATE TABLE utilisateurs (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe  VARCHAR(255) NOT NULL,   -- hashé avec password_hash()
  telephone     VARCHAR(20)  DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Produits ----------
CREATE TABLE produits (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(150) NOT NULL,
  description  TEXT         DEFAULT NULL,
  prix         DECIMAL(10,2) NOT NULL,
  image        VARCHAR(255) DEFAULT NULL,
  categorie    VARCHAR(50)  DEFAULT NULL,   -- ex : fille, garcon, robes...
  saison       VARCHAR(50)  DEFAULT NULL,   -- ex : ete, hiver...
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Panier ----------
CREATE TABLE panier (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id  INT NOT NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- ---------- Lignes du panier ----------
CREATE TABLE panier_items (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  panier_id   INT NOT NULL,
  produit_id  INT NOT NULL,
  quantite    INT NOT NULL DEFAULT 1,
  FOREIGN KEY (panier_id)  REFERENCES panier(id)   ON DELETE CASCADE,
  FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- ---------- Favoris ----------
-- Optionnel : à créer si tu branches les favoris sur la base
-- (aujourd'hui ils ne sont stockés que dans le navigateur via localStorage)
CREATE TABLE favoris (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id  INT NOT NULL,
  produit_id      INT NOT NULL,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
  FOREIGN KEY (produit_id)     REFERENCES produits(id)     ON DELETE CASCADE
);

-- ---------- Quelques produits d'exemple ----------
INSERT INTO produits (nom, description, prix, image, categorie, saison) VALUES
('Robe coton', 'Robe en coton douce et légère', 27.90, 'assets/img-fille11.avif', 'robes', 'ete'),
('Robe été',   "Robe légère et élégante pour l'été", 29.90, 'assets/image-fille-5.avif', 'robes', 'ete');