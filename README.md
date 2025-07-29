# CREE 2GK - Plateforme de Produits Numériques

Une plateforme e-commerce moderne spécialisée dans la vente de produits numériques avec livraison instantanée.

## 🚀 Fonctionnalités

### 🛍️ Boutique en ligne
- **Catalogue complet** : Cartes gaming, streaming, logiciels, cartes prépayées, crypto, VPN
- **Recherche avancée** avec filtres par catégorie, prix, marque
- **Système de favoris** et notation produits
- **Panier intelligent** avec gestion des quantités
- **Codes promo** et système de réductions

### 🎨 Interface utilisateur
- **Design moderne** avec thème sombre
- **Responsive design** adaptatif mobile/desktop
- **Animations fluides** et micro-interactions
- **Navigation intuitive** avec barre de recherche
- **Notifications temps réel**

### 🔐 Sécurité & Paiement
- **Paiement sécurisé** SSL 256 bits
- **Intégration KiaPay** et PayPal
- **Gestion des sessions** utilisateur
- **Protection CSRF** et validation des données

### ⚡ Performance
- **Livraison instantanée** des codes numériques
- **Cache optimisé** pour les performances
- **API REST** pour les données dynamiques
- **Base de données MySQL** optimisée

## 🛠️ Technologies utilisées

### Frontend
- **HTML5** sémantique
- **CSS3** avec Flexbox/Grid
- **JavaScript ES6+** vanilla
- **Responsive design** mobile-first

### Backend
- **PHP 8+** avec PDO
- **MySQL 8+** base de données
- **API REST** pour les échanges de données
- **Sessions** et authentification

### Outils & Services
- **Remix Icons** pour l'iconographie
- **LocalStorage** pour la persistance côté client
- **AJAX** pour les requêtes asynchrones

## 📁 Structure du projet

```
cree2gk/
├── index.html                 # Page d'accueil
├── cartes-gaming.html         # Page cartes gaming
├── panier.html               # Page panier
├── assets/
│   ├── css/
│   │   └── style.css         # Styles principaux
│   └── js/
│       ├── main.js           # JavaScript principal
│       ├── gaming.js         # Page cartes gaming
│       └── cart.js           # Gestion du panier
├── api/
│   ├── products.php          # API produits
│   ├── categories.php        # API catégories
│   └── cart.php             # API panier
├── config/
│   └── database.php          # Configuration BDD
└── database/
    └── schema.sql            # Structure de la base
```

## 🚀 Installation

### Prérequis
- **PHP 8.0+**
- **MySQL 8.0+**
- **Serveur web** (Apache/Nginx)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-repo/cree2gk.git
cd cree2gk
```

2. **Configuration de la base de données**
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE cree2gk_db;

# Importer le schéma
mysql -u root -p cree2gk_db < database/schema.sql
```

3. **Configuration**
```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cree2gk_db');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

4. **Démarrer le serveur**
```bash
# Avec PHP built-in server
php -S localhost:8000

# Ou configurer Apache/Nginx
```

5. **Accéder au site**
```
http://localhost:8000
```

## 📊 Base de données

### Tables principales
- **users** : Gestion des utilisateurs
- **categories** : Catégories de produits
- **products** : Catalogue des produits
- **orders** : Commandes clients
- **order_items** : Articles des commandes
- **digital_codes** : Codes numériques
- **cart** : Panier d'achats
- **reviews** : Avis clients

## 🔧 API Endpoints

### Produits
```
GET /api/products.php              # Liste des produits
GET /api/products.php?category=X   # Produits par catégorie
GET /api/products.php?search=X     # Recherche produits
```

### Catégories
```
GET /api/categories.php            # Liste des catégories
```

### Panier
```
GET /api/cart.php                  # Contenu du panier
POST /api/cart.php                 # Ajouter au panier
PUT /api/cart.php                  # Modifier quantité
DELETE /api/cart.php               # Supprimer du panier
```

## 🎯 Fonctionnalités clés

### Gestion du panier
- Ajout/suppression de produits
- Modification des quantités
- Calcul automatique des totaux
- Persistance avec localStorage
- Codes promo et réductions

### Système de recherche
- Recherche en temps réel
- Filtres par catégorie, prix, marque
- Tri par popularité, prix, note
- Pagination des résultats

### Interface responsive
- Design mobile-first
- Breakpoints optimisés
- Navigation tactile
- Performance mobile

## 🔒 Sécurité

### Mesures implémentées
- **Validation des données** côté serveur
- **Requêtes préparées** PDO
- **Protection XSS** avec htmlspecialchars
- **Sessions sécurisées** avec tokens
- **HTTPS** recommandé en production

## 📈 Performance

### Optimisations
- **Lazy loading** des images
- **Minification** CSS/JS
- **Cache navigateur** optimisé
- **Requêtes SQL** optimisées
- **CDN** pour les ressources statiques

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

- **Email** : support@cree2gk.com
- **Documentation** : [docs.cree2gk.com](https://docs.cree2gk.com)
- **Issues** : [GitHub Issues](https://github.com/votre-repo/cree2gk/issues)

## 🎉 Remerciements

- **Remix Icons** pour l'iconographie
- **Tailwind CSS** pour l'inspiration du design
- **PHP Community** pour les bonnes pratiques

---

**CREE 2GK** - Votre plateforme de confiance pour les produits numériques 🚀