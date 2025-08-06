# 🔐 Accès Administration - MaickelSMM

## 👤 Compte Admin Par Défaut

### Informations de Connexion
- **URL Admin** : `votre-site.com/admin/`
- **Email** : `admin@maickelsmm.com`
- **Mot de passe** : `password123`
- **Rôle** : Superadmin

## 🚀 Étapes pour Activer les Inscriptions

### 1. Se Connecter à l'Admin
1. Allez sur : `votre-site.com/admin/`
2. Connectez-vous avec les identifiants ci-dessus
3. Vous arriverez sur le tableau de bord admin

### 2. Activer les Inscriptions
1. Dans le menu admin, cliquez sur **"Paramètres"**
2. Allez dans l'onglet **"Général"**
3. Cherchez **"Autoriser les inscriptions"**
4. Cochez la case pour **ACTIVER** les inscriptions
5. Cliquez sur **"Sauvegarder"**

### 3. Vérifier l'Activation
- Allez sur `votre-site.com/register.php`
- La page d'inscription devrait maintenant fonctionner
- Plus de message "Les inscriptions sont fermées"

## ⚙️ Autres Paramètres Importants

### Dans l'onglet "Général"
- **Nom du site** : MaickelSMM
- **Description** : Personnalisez la description
- **Email de contact** : Votre vrai email

### Dans l'onglet "Paiement"
- **Numéros Mobile Money** : Ajoutez vos vrais numéros
- **Instructions de paiement** : Personnalisez les instructions

### Dans l'onglet "Email"
- **Configuration SMTP** : Pour envoyer des emails
- **Email d'expéditeur** : Votre email professionnel

## 🛡️ Sécurité - À FAIRE IMMÉDIATEMENT

### 1. Changer le Mot de Passe Admin
1. Dans l'admin, allez dans **"Utilisateurs"**
2. Trouvez le compte `admin@maickelsmm.com`
3. Cliquez sur **"Modifier"**
4. Changez le mot de passe pour quelque chose de sécurisé
5. Sauvegardez

### 2. Changer l'Email Admin
1. Remplacez `admin@maickelsmm.com` par votre vrai email
2. Cela vous permettra de recevoir les notifications

## 📱 Pages d'Authentification Disponibles

### Versions Simplifiées (Recommandées)
- **Connexion** : `login_simple.php`
- **Inscription** : `register_simple.php`

### Versions Originales
- **Connexion** : `login.php`
- **Inscription** : `register.php`

## 🔧 En Cas de Problème

### Si vous ne pouvez pas vous connecter
1. Utilisez `login_simple.php` au lieu de `login.php`
2. Vérifiez que la base de données contient bien l'utilisateur admin

### Si l'admin ne s'affiche pas
1. Vérifiez l'URL : `votre-site.com/admin/index.php`
2. Assurez-vous d'être connecté en tant qu'admin

### Test Rapide de l'Admin
Créez ce fichier pour vérifier l'utilisateur admin :

```php
<?php
// test_admin.php
require_once 'config/database.php';
$db = Database::getInstance();

$admin = $db->fetchOne("SELECT * FROM users WHERE email = 'admin@maickelsmm.com'");
if ($admin) {
    echo "✅ Utilisateur admin trouvé<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Rôle: " . $admin['role'] . "<br>";
    echo "Statut: " . $admin['status'] . "<br>";
    
    // Test du mot de passe
    if (password_verify('password123', $admin['password'])) {
        echo "✅ Mot de passe correct<br>";
    } else {
        echo "❌ Mot de passe incorrect<br>";
    }
} else {
    echo "❌ Utilisateur admin non trouvé<br>";
}
?>
```

## 📞 Support

Si vous avez des problèmes :
1. Testez d'abord avec `login_simple.php`
2. Vérifiez avec `test_admin.php`
3. Contactez-moi avec les détails de l'erreur

---

**🎯 OBJECTIF** : Activez les inscriptions dans Admin > Paramètres > Général