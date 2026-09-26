# Maison de Mode

Site e-commerce de vêtements pour enfants, développé en HTML, CSS, JavaScript,
PHP et MySQL. Projet réalisé dans le cadre de ma formation Développeur Web et
Web Mobile.

## Fonctionnalités

- Catalogue de produits filtrable par catégorie et par saison, avec recherche et tri par prix
- Fiches produit avec ajout au panier et aux favoris
- Inscription et connexion (mots de passe hashés avec `password_hash`, sessions PHP)
- Espace client : modification du profil, historique des achats
- Requêtes SQL préparées (PDO) sur une base MySQL

## Stack technique

- **Front-end** : HTML5, CSS3, JavaScript
- **Back-end** : PHP (PDO)
- **Base de données** : MySQL

## Conception

Le dossier [`diagrammes/`](./diagrammes) contient le diagramme de classes et le
diagramme de cas d'utilisation réalisés en amont du développement.

## Installation locale (XAMPP)

1. Copie le dossier du projet dans `htdocs` (ex. `C:/xampp/htdocs/maison-de-mode`)
2. Démarre Apache et MySQL depuis le panneau de contrôle XAMPP
3. Crée la base de données : ouvre phpMyAdmin, crée une base `maison_de_mode`,
   puis importe le fichier [`database.sql`](./database.sql)
4. Configure la connexion à la base :
   ```
   cp backend/db.example.php backend/db.php
   ```
   puis renseigne tes identifiants MySQL dans `backend/db.php`
5. Ouvre `http://localhost/maison-de-mode/accueil.html`



## Pistes d'amélioration

- Panier et favoris enregistrés en base pour les utilisateurs connectés (aujourd'hui stockés dans le navigateur)
- Espace administrateur pour gérer les produits et les commandes
- Fiches produit générées dynamiquement depuis la base de données