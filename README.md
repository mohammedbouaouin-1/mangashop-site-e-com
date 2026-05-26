# 📚 MangaShop — Boutique E-Commerce & Impression à la Demande

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892bf.svg?style=flat-square)](https://php.net)
[![Database](https://img.shields.io/badge/database-mysql-%2300758f.svg?style=flat-square)](https://mysql.com)
[![Design](https://img.shields.io/badge/design-vanilla--css%20%2F%20glassmorphism-orange.svg?style=flat-square)](https://en.wikipedia.org/wiki/Glassmorphism)
[![Hosting Compatible](https://img.shields.io/badge/hosting-infinityfree%20compatible-brightgreen.svg?style=flat-square)](https://infinityfree.com)

**MangaShop** est une application web e-commerce ultra-moderne et performante de vente et d'impression à la demande de mangas au Maroc. Développée en PHP natif (architecture MVC propre) sans framework lourd, elle propose un design SaaS épuré, interactif et haut de gamme.

---

## ✨ Fonctionnalités Clés

### 🎨 Design Premium & Micro-interactions
* **Thème Dynamique** : Switch fluide entre **Mode Sombre** (Dark Mode) et **Mode Clair** (Light Mode) avec persistance locale (`localStorage`).
* **Esthétique Glassmorphism** : Cartes premium avec flous d'arrière-plan, transitions fluides en CSS pur et ombres douces.
* **Slider Hero 3D** : Un carrousel haut de gamme avec rotation 3D interactive des couvertures de mangas qui suit le mouvement du curseur.

### 🛒 Expérience Shopping & Commandes
* **Panier Glissant (Drawer)** : Panier dynamique accessible de partout avec calcul en temps réel et **jauge de livraison gratuite** animée.
* **Recherche AJAX Instantanée** : Barre de recherche avec autocomplétion et suggestions de produits en direct sans recharger la page.
* **Bundles (Packs)** : Regroupement de plusieurs volumes dans un pack avec réduction automatique calculée et affichée.
* **Paiements Flexibles** : Intégration de **Stripe** (Payment Element officiel) et option de Paiement à la Livraison (COD - Cash On Delivery).

### 🖨️ Module d'Impression Personnalisée
* Formulaire avancé permettant aux utilisateurs de télécharger leur fichier PDF (manga personnalisé).
* Choix des spécifications de fabrication (Format, type de couverture, nombre de pages, quantité).
* Système de demande de devis instantané enregistré dans l'espace administration.

### 🛡️ Espace Administration Premium
* **Dashboard Analytics** : Indicateurs de performance (revenus mensuels, panier moyen, ventes totales, commandes en cours).
* **Statistiques Graphiques** : Visualisation claire et épurée des tendances de vente.
* **Gestion Globale** : CRUD complet des produits, catégories, packs (bundles), commandes, devis et abonnés à la newsletter.
* **Codes Promos** : Création et gestion de coupons de réduction actifs, expirables ou limités en utilisation.
* **Sécurité & Logs** : Historique d'activité des administrateurs et journalisation des tentatives de connexion suspectes.
* **Exportation** : Export des commandes et données au format CSV en un clic.

---

## 🛠️ Stack Technique

* **Backend** : PHP 7.4+ (Architecture MVC modulaire avec contrôleurs et modèles).
* **Base de données** : MySQL via l'API PDO (sécurisé contre les injections SQL grâce aux requêtes préparées).
* **Frontend** : HTML5 sémantique, Vanilla CSS (architecture moderne de variables de conception), JavaScript moderne (ES6+, AJAX via Fetch API).
* **Services Tiers** : Stripe API pour les paiements en ligne, PHPMailer pour les envois d'e-mails professionnels.

---

## 🚀 Installation en Local (Développement)

### 📋 Prérequis
* Un serveur local comme **XAMPP**, **WampServer** ou **MAMP**.
* PHP version 7.4 minimum.
* MySQL / MariaDB.

### ⚙️ Étapes d'installation
1. **Cloner le projet** dans le répertoire racine de votre serveur local (ex: `htdocs/` pour XAMPP) :
   ```bash
   git clone https://github.com/mohammedbouaouin-1/mangashop-site-e-com.git mangashop
   ```

2. **Créer le fichier de configuration** :
   * Copiez le fichier `includes/config.sample.php` et renommez-le en **`includes/config.php`**.
   * Ouvrez-le et ajustez vos accès de base de données si nécessaire :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'mangashop');
     ```

3. **Lancer l'installation de la Base de Données** :
   * Créez simplement une base de données vide nommée `mangashop` dans votre phpMyAdmin local.
   * **Magique ✨** : L'application détecte automatiquement que la base est vide et **importera automatiquement toutes les tables et données de démonstration** lors de votre première visite sur le site !

---

## 🌐 Déploiement en Production (InfinityFree)

Le projet a été optimisé pour fonctionner sur les hébergements gratuits comme **InfinityFree** sans modification du code source.

1. Créez une base de données MySQL sur votre panneau InfinityFree.
2. Ouvrez le fichier `includes/config.php` en local et ajoutez vos identifiants de production dans le bloc `else` (les accès de développement local restent ainsi protégés et séparés !).
3. Importez le fichier `database.sql` dans votre phpMyAdmin InfinityFree.
4. Téléversez l'intégralité du contenu du dossier `mangashop` à l'intérieur du dossier distant `/htdocs` de votre serveur FTP via **FileZilla**.

*Pour plus de détails, consultez notre [Guide d'Hébergement InfinityFree personnalisé](file:///C:/Users/admin/.gemini/antigravity/brain/d9e09967-6cc2-47c3-b421-60eab80274e2/guide_hebergement_infinityfree.md) directement inclus à la racine de ce dépôt.*

---

## 🔑 Identifiants d'Administration par Défaut

Pour accéder au panneau d'administration (`http://localhost/mangashop/admin/`) :
* **Identifiant (Email)** : `admin@mangashop.ma`
* **Mot de passe** : `admin123` (ou celui défini dans votre base de données locale)

---

## 📝 Licence
Ce projet est développé à des fins de démonstration et d'apprentissage. Tous droits réservés.
