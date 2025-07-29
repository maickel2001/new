# ✅ Checklist de Déploiement Hostinger - CREE 2GK

## 🎯 Avant le déploiement

### 1. Compte Hostinger
- [ ] Plan Business/Premium activé
- [ ] Domaine configuré et pointé
- [ ] Accès hPanel fonctionnel

### 2. Préparation des fichiers
- [ ] Tous les fichiers téléchargés
- [ ] Structure de dossiers vérifiée
- [ ] Fichiers compressés en ZIP (optionnel)

## 🚀 Étapes de déploiement

### Étape 1: Configuration Base de Données
- [ ] Créer base MySQL dans hPanel
- [ ] Noter: nom_db, utilisateur, mot_de_passe
- [ ] Tester connexion phpMyAdmin

### Étape 2: Upload des fichiers
- [ ] Gestionnaire de fichiers hPanel
- [ ] Upload dans `/public_html/`
- [ ] Vérifier structure complète
- [ ] Permissions 755 pour dossiers, 644 pour fichiers

### Étape 3: Configuration
- [ ] Modifier `config/database-hostinger.php`
- [ ] Remplacer par vraies informations DB
- [ ] Renommer en `config/database.php`
- [ ] Générer nouvelles clés secrètes

### Étape 4: Base de données
- [ ] phpMyAdmin → Importer
- [ ] Sélectionner `database/schema.sql`
- [ ] Vérifier création des tables
- [ ] Contrôler données d'exemple

### Étape 5: Tests
- [ ] Visiter `votre-domaine.com/test-connection.php`
- [ ] Vérifier tous les tests verts
- [ ] Tester navigation du site
- [ ] Contrôler API endpoints

### Étape 6: Sécurité
- [ ] Activer SSL/HTTPS dans hPanel
- [ ] Configurer redirections HTTPS
- [ ] Vérifier fichiers .htaccess
- [ ] Supprimer `test-connection.php`

### Étape 7: Optimisation
- [ ] Activer cache Hostinger
- [ ] Configurer Cloudflare (optionnel)
- [ ] Optimiser images
- [ ] Tester vitesse de chargement

## 🔧 Configuration Hostinger spécifique

### Base de données
```
Hôte: localhost
Nom: u123456789_cree2gk
Utilisateur: u123456789_admin  
Port: 3306 (par défaut)
```

### Email SMTP
```
Serveur: smtp.hostinger.com
Port: 587
Sécurité: TLS
```

### PHP
```
Version: 8.0+ recommandée
Extensions: PDO, MySQL, cURL, OpenSSL
Limite mémoire: 256M minimum
```

## 🌐 URLs importantes

### Administration
- hPanel: `https://hpanel.hostinger.com`
- phpMyAdmin: Via hPanel → Bases de données
- Gestionnaire fichiers: Via hPanel → Fichiers

### Site web
- Site principal: `https://votre-domaine.com`
- Test connexion: `https://votre-domaine.com/test-connection.php`
- API produits: `https://votre-domaine.com/api/products.php`

## 🔍 Vérifications post-déploiement

### Fonctionnalités
- [ ] Page d'accueil charge correctement
- [ ] Navigation entre pages
- [ ] Recherche fonctionne
- [ ] Filtres produits opérationnels
- [ ] Ajout au panier
- [ ] Système de favoris
- [ ] Responsive mobile

### Performance
- [ ] Temps de chargement < 3 secondes
- [ ] Images optimisées
- [ ] CSS/JS minifiés
- [ ] Cache activé

### Sécurité
- [ ] HTTPS actif partout
- [ ] Headers sécurité configurés
- [ ] Dossiers sensibles protégés
- [ ] Logs d'erreur configurés

## 🆘 Dépannage

### Erreurs courantes
```
Erreur 500: Vérifier logs PHP dans hPanel
Connexion DB: Contrôler informations dans config/
API 404: Vérifier mod_rewrite activé
Images manquantes: Contrôler chemins relatifs
```

### Support
- Documentation: help.hostinger.com
- Chat support: 24/7 dans hPanel
- Communauté: Forum Hostinger

## 📊 Monitoring

### À surveiller
- [ ] Uptime du site
- [ ] Logs d'erreur PHP
- [ ] Utilisation base de données
- [ ] Trafic et performance
- [ ] Sauvegardes automatiques

---

## 🎉 Site déployé avec succès !

Votre plateforme CREE 2GK est maintenant opérationnelle sur Hostinger.

**Prochaines étapes :**
1. Configurer Google Analytics
2. Mettre en place les paiements réels
3. Ajouter plus de produits
4. Optimiser le SEO
5. Lancer la promotion !