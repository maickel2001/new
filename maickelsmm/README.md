# MaickelSMM - Panneau SMM Professionnel

![MaickelSMM Logo](assets/images/logo.png)

## 📋 Description

**MaickelSMM** est un panneau SMM (Social Media Marketing) professionnel développé en PHP natif avec MySQL. Il s'agit d'un clone amélioré inspiré de TarantulaSMM, offrant une interface moderne et sombre avec toutes les fonctionnalités nécessaires pour gérer un business SMM.

### ✨ Fonctionnalités Principales

- **🎯 Services SMM Complets** : Plus de 80 services pré-configurés pour toutes les plateformes populaires
- **💳 Paiement Manuel** : Système de paiement Mobile Money (MTN, Moov, Orange) avec upload de preuve
- **👥 Multi-utilisateurs** : Système complet d'authentification et de gestion des rôles
- **📱 Responsive Design** : Interface moderne et mobile-first inspirée de TarantulaSMM
- **🛡️ Sécurité Renforcée** : Protection CSRF, validation des uploads, sessions sécurisées
- **⚡ Performance** : Code optimisé, requêtes efficaces, cache intégré
- **🔧 Administration** : Panneau d'administration complet sans besoin de coder

## 🚀 Installation Rapide

### Prérequis

- **PHP 8.0+** avec extensions : MySQLi, PDO, cURL, GD, MBString, Zip
- **MySQL 5.7+** ou MariaDB 10.2+
- **Apache/Nginx** avec mod_rewrite activé
- **Certificat SSL** recommandé pour la production

### 1. Téléchargement

```bash
# Cloner le repository
git clone https://github.com/votre-username/maickelsmm.git
cd maickelsmm

# Ou télécharger et extraire l'archive ZIP
wget https://github.com/votre-username/maickelsmm/archive/main.zip
unzip main.zip
```

### 2. Configuration de la Base de Données

```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE maickelsmm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON maickelsmm.* TO 'maickel_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';
FLUSH PRIVILEGES;
EXIT;

# Importer le schéma et les données
mysql -u maickel_user -p maickelsmm < database.sql
```

### 3. Configuration PHP

Éditez le fichier `config/database.php` :

```php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'maickelsmm');
define('DB_USER', 'maickel_user');
define('DB_PASS', 'votre_mot_de_passe_securise');
```

Éditez le fichier `config/config.php` pour ajuster l'URL de base :

```php
define('SITE_URL', 'https://votre-domaine.com');
```

### 4. Permissions des Dossiers

```bash
# Définir les permissions correctes
chmod 755 assets/uploads/
chmod 755 assets/uploads/payments/
chmod 755 assets/uploads/banners/
chown -R www-data:www-data assets/uploads/

# Configuration Apache (si nécessaire)
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Configuration du Serveur Web

#### Apache (.htaccess inclus)
Le fichier `.htaccess` est déjà configuré pour Apache.

#### Nginx
Ajoutez cette configuration à votre bloc server :

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## 🔧 Configuration Initiale

### 1. Accès Administrateur

**Compte admin par défaut :**
- **Email :** admin@maickelsmm.com
- **Mot de passe :** password123

⚠️ **IMPORTANT :** Changez immédiatement ces identifiants après la première connexion !

### 2. Configuration des Paiements

1. Connectez-vous à l'administration
2. Allez dans **Paramètres > Paiements**
3. Configurez vos numéros Mobile Money :
   ```json
   {
     "mtn": "67890123",
     "moov": "60123456", 
     "orange": "07654321"
   }
   ```

### 3. Personnalisation du Site

#### Logo et Branding
- Uploadez votre logo dans `assets/images/`
- Modifiez les paramètres dans **Paramètres > Général**

#### Méthodes de Contact
- Configurez votre numéro WhatsApp
- Définissez l'email de contact
- Ajustez les heures de support

## 📚 Guide d'Utilisation

### Pour les Clients

#### 1. Passer une Commande
1. Parcourir les services sur la page d'accueil
2. Cliquer sur "Commander" pour le service désiré
3. Remplir le formulaire (quantité, lien, informations de contact)
4. Sélectionner la méthode de paiement
5. Confirmer la commande

#### 2. Effectuer le Paiement
1. Envoyer le montant exact via Mobile Money
2. Accéder à la page de commande via le lien reçu par email
3. Uploader la preuve de paiement (capture d'écran)
4. Attendre la validation et le traitement

#### 3. Suivi de Commande
- Statuts disponibles : En attente → En cours → Terminé
- Notifications par email à chaque étape
- Accès direct via lien ou dashboard (si connecté)

### Pour les Administrateurs

#### 1. Gestion des Services
- **Ajouter/Modifier** : Prix, descriptions, quantités min/max
- **Organiser** : Ordre d'affichage, catégories
- **Activer/Désactiver** : Contrôle de la disponibilité

#### 2. Gestion des Commandes
- **Visualisation** : Toutes les commandes avec filtres
- **Traitement** : Changement de statut, ajout de notes
- **Preuves de paiement** : Validation des uploads clients

#### 3. Gestion des Utilisateurs
- **Clients** : Liste, modification, blocage
- **Administrateurs** : Création de comptes admin

#### 4. Paramètres du Site
- **Général** : Nom, logo, couleurs, devise
- **Paiements** : Méthodes, instructions, numéros
- **Email** : Configuration SMTP, templates
- **Sécurité** : Maintenance, restrictions

## 🛡️ Sécurité

### Mesures Implémentées

- **Authentification** : Mots de passe hashés avec bcrypt
- **Sessions** : Sécurisées avec régénération d'ID
- **CSRF** : Protection contre les attaques cross-site
- **XSS** : Échappement automatique des données
- **Upload** : Validation stricte des fichiers
- **SQL** : Requêtes préparées (PDO)
- **Rate Limiting** : Protection contre le brute force

### Bonnes Pratiques

```bash
# Sauvegardes régulières
mysqldump -u maickel_user -p maickelsmm > backup_$(date +%Y%m%d).sql

# Mise à jour des permissions
find assets/uploads/ -type f -exec chmod 644 {} \;
find assets/uploads/ -type d -exec chmod 755 {} \;

# Monitoring des logs
tail -f /var/log/apache2/error.log
```

## 🎨 Personnalisation

### Thème et Design

Le design utilise des variables CSS personnalisables dans `assets/css/style.css` :

```css
:root {
    --primary-color: #6366f1;
    --secondary-color: #ec4899;
    --bg-primary: #0f172a;
    --bg-secondary: #1e293b;
    /* ... autres variables ... */
}
```

### Ajout de Services

1. **Via l'interface admin** (recommandé)
2. **Via SQL** pour l'import en masse :

```sql
INSERT INTO services (category_id, name, description, min_quantity, max_quantity, price_per_1000, delivery_time, guarantee, status) 
VALUES (1, 'Nouveau Service Instagram', 'Description du service', 100, 10000, 2500.00, '1-3 jours', 'yes', 'active');
```

### Nouvelles Catégories

```sql
INSERT INTO categories (name, description, icon, sort_order, status) 
VALUES ('Nouvelle Plateforme', 'Description de la plateforme', 'fab fa-nouvelle-icone', 16, 'active');
```

## 🔧 Maintenance

### Tâches Régulières

#### Quotidiennes
- Vérification des nouvelles commandes
- Validation des preuves de paiement
- Réponse aux messages de contact

#### Hebdomadaires
- Sauvegarde de la base de données
- Nettoyage des logs anciens
- Vérification des mises à jour de sécurité

#### Mensuelles
- Analyse des statistiques
- Optimisation de la base de données
- Révision des prix et services

### Commandes Utiles

```bash
# Nettoyage des logs
find /var/log -name "*.log" -mtime +30 -delete

# Optimisation MySQL
mysqlcheck -u root -p --optimize --all-databases

# Vérification de l'espace disque
df -h
du -sh assets/uploads/
```

## 📊 Statistiques et Monitoring

### Métriques Importantes

- **Commandes** : Total, en cours, terminées
- **Revenus** : Journaliers, mensuels, annuels
- **Clients** : Nouveaux, récurrents, actifs
- **Services** : Les plus populaires, rentabilité

### Outils de Monitoring

1. **Google Analytics** : Ajoutez votre code dans les paramètres
2. **Logs système** : Surveillance des erreurs PHP/MySQL
3. **Uptime monitoring** : Services comme UptimeRobot

## 🚨 Dépannage

### Problèmes Courants

#### 1. Erreur de connexion à la base de données
```bash
# Vérifier les paramètres
cat config/database.php

# Tester la connexion
mysql -u maickel_user -p maickelsmm -e "SELECT 1"
```

#### 2. Problèmes d'upload
```bash
# Vérifier les permissions
ls -la assets/uploads/
chmod 755 assets/uploads/payments/
```

#### 3. Erreurs 500
```bash
# Consulter les logs
tail -f /var/log/apache2/error.log
tail -f /var/log/php_errors.log
```

#### 4. Sessions qui ne fonctionnent pas
```php
// Vérifier la configuration PHP
phpinfo();
// Rechercher session.save_path et session.cookie_domain
```

### Support Technique

- **Documentation** : Ce README et commentaires dans le code
- **Logs** : Consultez toujours les logs en cas d'erreur
- **Communauté** : Issues GitHub pour les bugs et suggestions

## 📈 Optimisation des Performances

### Base de Données

```sql
-- Index pour les requêtes fréquentes
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);
CREATE INDEX idx_services_category ON services(category_id, status);
```

### Cache et CDN

1. **Cache PHP** : Activez OPcache
2. **Cache navigateur** : Headers HTTP configurés
3. **CDN** : Pour les assets statiques (recommandé)

### Configuration PHP Recommandée

```ini
; php.ini optimisé pour MaickelSMM
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20
opcache.enable = 1
opcache.memory_consumption = 128
```

## 🔄 Mises à Jour

### Processus de Mise à Jour

1. **Sauvegarde complète**
   ```bash
   mysqldump -u maickel_user -p maickelsmm > backup_avant_maj.sql
   tar -czf files_backup.tar.gz . --exclude='*.sql'
   ```

2. **Test en environnement de développement**

3. **Application en production**
   ```bash
   # Mode maintenance
   echo "Site en maintenance" > maintenance.html
   
   # Mise à jour des fichiers
   # ...
   
   # Mise à jour de la base de données si nécessaire
   mysql -u maickel_user -p maickelsmm < update.sql
   
   # Désactiver la maintenance
   rm maintenance.html
   ```

### Changelog

#### Version 1.0.0 (Initial Release)
- ✅ Système complet de gestion SMM
- ✅ 80+ services pré-configurés
- ✅ Paiement Mobile Money
- ✅ Interface responsive moderne
- ✅ Panneau d'administration complet
- ✅ Sécurité renforcée

## 📝 Licence et Crédits

### Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

### Crédits
- **Développement** : Équipe MaickelSMM
- **Design inspiré de** : TarantulaSMM
- **Icônes** : Font Awesome
- **Polices** : Google Fonts (Inter)

### Contributions
Les contributions sont les bienvenues ! Veuillez :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📞 Support

### Assistance Technique
- **Email** : support@maickelsmm.com
- **Documentation** : Ce README
- **Issues GitHub** : Pour les bugs et améliorations

### Support Commercial
- **WhatsApp** : +225 07 12 34 56 78
- **Email** : contact@maickelsmm.com
- **Heures** : 24h/24 - 7j/7

---

**MaickelSMM** - Votre solution complète pour le marketing des réseaux sociaux 🚀

*Développé avec ❤️ pour booster votre succès sur les réseaux sociaux*