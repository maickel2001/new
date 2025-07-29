# 🚀 Guide de déploiement CREE 2GK sur Hostinger

## 📋 Prérequis Hostinger

### Plan d'hébergement recommandé
- **Business** ou **Premium** (pour PHP et MySQL)
- **PHP 8.0+** activé
- **Base de données MySQL** incluse
- **SSL gratuit** (Let's Encrypt)

## 🔧 Configuration Hostinger

### 1. Accès au panneau de contrôle
1. Connectez-vous à votre **hPanel Hostinger**
2. Allez dans **Bases de données MySQL**
3. Créez une nouvelle base de données

### 2. Configuration de la base de données
```sql
-- Notez ces informations depuis hPanel :
Nom de la base : votre_nom_db
Utilisateur : votre_user_db  
Mot de passe : votre_password_db
Hôte : localhost (généralement)
```

### 3. Upload des fichiers
1. Utilisez le **Gestionnaire de fichiers** ou **FTP**
2. Uploadez tous les fichiers dans `/public_html/`
3. Structure finale :
```
public_html/
├── index.html
├── cartes-gaming.html
├── panier.html
├── assets/
├── api/
├── config/
└── database/
```

## ⚙️ Configuration PHP

### 1. Modifier config/database.php
```php
// Remplacez par vos vraies informations Hostinger
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_nom_db_hostinger');
define('DB_USER', 'votre_user_db_hostinger');
define('DB_PASS', 'votre_password_db_hostinger');
```

### 2. Importer la base de données
1. Dans hPanel → **phpMyAdmin**
2. Sélectionnez votre base
3. **Importer** → Choisir `database/schema.sql`
4. Cliquer **Exécuter**

## 🌐 Configuration domaine

### 1. DNS et domaine
- Pointez votre domaine vers Hostinger
- Activez le **SSL gratuit** dans hPanel
- Configurez les **redirections HTTPS**

### 2. URLs de production
```javascript
// Dans assets/js/main.js, ajustez si nécessaire :
const API_BASE_URL = 'https://votre-domaine.com/api/';
```

## 🔒 Sécurité Hostinger

### 1. Fichier .htaccess
Créez `/public_html/.htaccess` :
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
</IfModule>
```

### 2. Protection des dossiers sensibles
Créez `/public_html/config/.htaccess` :
```apache
Order Deny,Allow
Deny from all
```

## 📧 Configuration Email

### 1. SMTP Hostinger
```php
// Dans config/database.php
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@votre-domaine.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe-email');
```

## 🚀 Déploiement étape par étape

### Étape 1 : Préparation
1. ✅ Téléchargez tous les fichiers du projet
2. ✅ Compressez en ZIP si nécessaire
3. ✅ Préparez vos informations de base de données

### Étape 2 : Upload
1. ✅ Connectez-vous à hPanel Hostinger
2. ✅ Gestionnaire de fichiers → public_html
3. ✅ Uploadez et décompressez les fichiers
4. ✅ Vérifiez la structure des dossiers

### Étape 3 : Base de données
1. ✅ Créez la base MySQL dans hPanel
2. ✅ Notez les informations de connexion
3. ✅ Importez schema.sql via phpMyAdmin
4. ✅ Vérifiez que les tables sont créées

### Étape 4 : Configuration
1. ✅ Modifiez config/database.php
2. ✅ Testez la connexion à la base
3. ✅ Configurez les emails si nécessaire
4. ✅ Activez SSL et HTTPS

### Étape 5 : Test
1. ✅ Visitez votre-domaine.com
2. ✅ Testez la navigation
3. ✅ Testez l'ajout au panier
4. ✅ Vérifiez les API endpoints

## 🔧 Optimisations Hostinger

### 1. Performance
- Activez la **mise en cache** dans hPanel
- Utilisez **Cloudflare** (gratuit avec Hostinger)
- Optimisez les **images** avant upload

### 2. Monitoring
- Configurez **Google Analytics**
- Surveillez les **logs d'erreur** PHP
- Utilisez **Uptime monitoring**

## 🆘 Dépannage courant

### Erreur 500
```bash
# Vérifiez les logs d'erreur dans hPanel
# Souvent : permissions de fichiers incorrectes
chmod 755 dossiers/
chmod 644 fichiers.php
```

### Connexion base de données
```php
// Test de connexion simple
<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=votre_db", "user", "pass");
    echo "Connexion réussie !";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
```

### API non accessible
- Vérifiez que **mod_rewrite** est activé
- Contrôlez les **permissions** des dossiers API
- Testez directement : `votre-domaine.com/api/products.php`

## 📞 Support

- **Documentation Hostinger** : help.hostinger.com
- **Support 24/7** : Chat en direct hPanel
- **Communauté** : Forum Hostinger

---

🎉 **Votre site CREE 2GK sera opérationnel sur Hostinger !**