# 🚀 Installation MaickelSMM sur Hostinger

## 📋 Prérequis

- Compte Hostinger avec hébergement web
- Accès au panneau de contrôle hPanel
- Accès FTP/File Manager
- Base de données MySQL disponible

## 🔧 Étape 1 : Configuration de la Base de Données

### 1.1 Créer la Base de Données
1. Connectez-vous à votre **hPanel Hostinger**
2. Allez dans **Bases de données > Bases de données MySQL**
3. Cliquez sur **Créer une base de données**
4. Nommez votre base : `u123456789_maickelsmm` (remplacez par votre préfixe)
5. Créez un utilisateur avec tous les privilèges
6. **Notez bien** :
   - Nom de la base : `u123456789_maickelsmm`
   - Utilisateur : `u123456789_maickel`
   - Mot de passe : `votre_mot_de_passe_securise`
   - Hôte : `localhost`

### 1.2 Importer le Script SQL
1. Dans **Bases de données**, cliquez sur **Gérer** à côté de votre base
2. Ouvrez **phpMyAdmin**
3. Sélectionnez votre base de données
4. Cliquez sur **Importer**
5. Sélectionnez le fichier `database.sql` du projet
6. Cliquez sur **Exécuter**

## 📁 Étape 2 : Upload des Fichiers

### 2.1 Via File Manager (Recommandé)
1. Dans hPanel, allez dans **Fichiers > Gestionnaire de fichiers**
2. Naviguez vers `/public_html/`
3. Supprimez le fichier `index.html` par défaut s'il existe
4. Uploadez **TOUS** les fichiers du projet MaickelSMM
5. Assurez-vous que la structure est :
   ```
   public_html/
   ├── admin/
   ├── api/
   ├── assets/
   ├── config/
   ├── includes/
   ├── index.php
   ├── login.php
   ├── register.php
   └── ... (tous les autres fichiers)
   ```

### 2.2 Via FTP (Alternative)
1. Utilisez FileZilla ou votre client FTP préféré
2. Connectez-vous avec vos identifiants Hostinger
3. Naviguez vers `/public_html/`
4. Uploadez tous les fichiers

## ⚙️ Étape 3 : Configuration

### 3.1 Modifier la Configuration Database
1. Ouvrez le fichier `/config/database.php`
2. Modifiez les constantes avec vos informations :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'u123456789_maickelsmm'); // Votre nom de base
   define('DB_USER', 'u123456789_maickel');    // Votre utilisateur
   define('DB_PASS', 'votre_mot_de_passe');    // Votre mot de passe
   ```

### 3.2 Modifier la Configuration Générale
1. Ouvrez `/config/config.php`
2. Modifiez l'URL du site :
   ```php
   define('SITE_URL', 'https://votre-domaine.com');
   ```

### 3.3 Permissions des Dossiers
Dans le File Manager, définissez les permissions :
- `assets/uploads/` : **755** ou **777**
- `assets/uploads/payments/` : **755** ou **777**
- `assets/uploads/banners/` : **755** ou **777**

## 🔐 Étape 4 : Sécurité

### 4.1 Protéger les Fichiers Sensibles
Le fichier `.htaccess` est déjà configuré pour :
- ✅ Bloquer l'accès direct aux fichiers `.sql`
- ✅ Protéger le dossier `config/`
- ✅ Sécuriser les uploads
- ✅ Headers de sécurité

### 4.2 Changer le Mot de Passe Admin
1. Connectez-vous à : `https://votre-domaine.com/admin/`
2. Email : `admin@maickelsmm.com`
3. Mot de passe : `password123`
4. **CHANGEZ IMMÉDIATEMENT** ce mot de passe !

## 📧 Étape 5 : Configuration Email (Optionnel)

### 5.1 SMTP Hostinger
1. Allez dans **Admin > Paramètres > Email**
2. Configurez avec les paramètres Hostinger :
   - **Serveur SMTP** : `smtp.hostinger.com`
   - **Port** : `587`
   - **Chiffrement** : `TLS`
   - **Utilisateur** : `votre-email@votre-domaine.com`
   - **Mot de passe** : `mot-de-passe-email`

## 🎯 Étape 6 : Configuration des Paiements

1. Connectez-vous à l'admin
2. Allez dans **Paramètres > Paiement**
3. Configurez vos numéros Mobile Money :
   - **MTN Money** : `+225XXXXXXXXX`
   - **Moov Money** : `+225XXXXXXXXX`
   - **Orange Money** : `+225XXXXXXXXX`

## ✅ Étape 7 : Test Final

### 7.1 Tests à Effectuer
1. **Page d'accueil** : `https://votre-domaine.com/`
2. **Inscription** : Créer un compte test
3. **Connexion admin** : `https://votre-domaine.com/admin/`
4. **Commande test** : Passer une commande
5. **Upload** : Tester l'upload de preuve de paiement

### 7.2 Vérifications
- ✅ Services affichés correctement
- ✅ Formulaire de commande fonctionnel
- ✅ Upload de fichiers opérationnel
- ✅ Panneau admin accessible
- ✅ Emails envoyés (si SMTP configuré)

## 🚨 Dépannage Hostinger

### Erreur 500 "Cette page ne fonctionne pas"

**Causes possibles :**
1. **Base de données incorrecte** - Vérifiez les identifiants dans `config/database.php`
2. **Permissions fichiers** - Assurez-vous que `assets/uploads/` a les bonnes permissions
3. **Fichier .htaccess** - Renommez temporairement `.htaccess` en `.htaccess_backup` pour tester
4. **Version PHP** - Vérifiez que vous utilisez PHP 7.4+ dans hPanel

**Solutions :**
```bash
# 1. Vérifier les logs d'erreur dans hPanel > Avancé > Logs d'erreur
# 2. Tester sans .htaccess
# 3. Vérifier les permissions
# 4. Tester la connexion DB
```

### Base de Données Non Accessible

1. Vérifiez le nom exact dans hPanel
2. Assurez-vous que l'utilisateur a tous les privilèges
3. Testez la connexion via phpMyAdmin

### Upload de Fichiers Échoue

1. Vérifiez les permissions : `chmod 755 assets/uploads/`
2. Augmentez les limites PHP dans hPanel si nécessaire

## 📞 Support

En cas de problème :
- 📧 **Email** : contact@maickelsmm.com
- 💬 **WhatsApp** : +225 07 12 34 56 78
- 📚 **Documentation** : Consultez le README.md

## 🎉 Félicitations !

Votre site MaickelSMM est maintenant installé et opérationnel sur Hostinger !

**URLs importantes :**
- **Site public** : `https://votre-domaine.com/`
- **Admin** : `https://votre-domaine.com/admin/`
- **Connexion** : `https://votre-domaine.com/login.php`

---

*Installation réalisée avec succès ? N'oubliez pas de personnaliser les paramètres dans l'admin !* 🚀