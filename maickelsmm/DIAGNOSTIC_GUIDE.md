# 🔧 Guide de Diagnostic - Erreur 500 MaickelSMM

## 📊 État Actuel
- ✅ **Base de données** : 100% fonctionnelle (19 services, 28 paramètres)
- ✅ **Configuration PHP** : Toutes extensions présentes
- ✅ **Permissions** : Dossiers uploads OK
- ❌ **Page d'accueil** : Erreur 500

## 🔍 Tests de Diagnostic à Effectuer

### **Test 1 : Page Ultra-Simple**
```
https://darkgoldenrod-turkey-940813.hostingersite.com/simple_test.php
```
**Objectif** : Vérifier si PHP fonctionne sans dépendances
**Si ça marche** : Le problème vient d'un fichier spécifique
**Si ça ne marche pas** : Problème serveur plus profond

### **Test 2 : Désactiver .htaccess**
1. **Renommer** `.htaccess` en `.htaccess_old`
2. **Renommer** `.htaccess_backup` en `.htaccess`
3. **Tester** la page d'accueil

**Objectif** : Vérifier si le problème vient des règles Apache
**Si ça marche** : Le problème vient du .htaccess original
**Si ça ne marche pas** : Le problème vient du code PHP

### **Test 3 : Version Simplifiée de l'Accueil**
```
https://darkgoldenrod-turkey-940813.hostingersite.com/index_simple.php
```
**Objectif** : Tester une version allégée de la page d'accueil
**Si ça marche** : Le problème vient d'une fonction spécifique dans index.php
**Si ça ne marche pas** : Problème avec les includes de base

### **Test 4 : Vérifier les Logs Hostinger**
1. **hPanel > Avancé > Logs d'erreur**
2. **Chercher** les erreurs récentes
3. **Noter** les messages d'erreur exacts

## 📋 Checklist de Diagnostic

### Étape 1 : Upload des Fichiers de Test
- [ ] `simple_test.php`
- [ ] `index_simple.php`
- [ ] `.htaccess_backup`

### Étape 2 : Tests Séquentiels
- [ ] Test 1 : `simple_test.php` → Résultat : ✅/❌
- [ ] Test 2 : Remplacer `.htaccess` → Résultat : ✅/❌
- [ ] Test 3 : `index_simple.php` → Résultat : ✅/❌
- [ ] Test 4 : Vérifier logs → Messages d'erreur :

### Étape 3 : Diagnostic Basé sur les Résultats

#### **Si simple_test.php fonctionne :**
- ✅ PHP est OK
- ✅ Base de données est OK
- ❌ Problème dans index.php ou .htaccess

#### **Si simple_test.php ne fonctionne pas :**
- ❌ Problème serveur ou configuration PHP
- Vérifier les logs d'erreur
- Contacter le support Hostinger si nécessaire

#### **Si .htaccess_backup résout le problème :**
- ❌ Le .htaccess original a des règles incompatibles
- Utiliser la version simplifiée
- Ajouter les règles progressivement

#### **Si index_simple.php fonctionne :**
- ❌ Problème dans une fonction spécifique de index.php
- Probablement `isMaintenanceMode()` ou `hasPermission()`
- Remplacer index.php par index_simple.php temporairement

## 🚀 Solutions par Scénario

### **Scénario A : Problème .htaccess**
1. Utiliser `.htaccess_backup` (version simplifiée)
2. Tester le site
3. Ajouter les règles une par une si nécessaire

### **Scénario B : Problème dans index.php**
1. Remplacer `index.php` par `index_simple.php`
2. Renommer `index_simple.php` en `index.php`
3. Site fonctionnel avec version simplifiée

### **Scénario C : Problème dans functions.php**
1. Vérifier la fonction `isMaintenanceMode()`
2. Vérifier la fonction `hasPermission()`
3. Corriger ou commenter les fonctions problématiques

### **Scénario D : Problème serveur**
1. Vérifier la version PHP (doit être 7.4+)
2. Vérifier les extensions PHP requises
3. Contacter le support Hostinger

## 📞 Support d'Urgence

Si aucun test ne fonctionne :
1. **Vérifier** que tous les fichiers sont bien uploadés
2. **Vérifier** les permissions des dossiers
3. **Vérifier** que la base de données est bien configurée
4. **Envoyer** les messages d'erreur exacts des logs

## 🎯 Objectif Final

Avoir un site 100% fonctionnel avec :
- ✅ Page d'accueil qui se charge
- ✅ 19 services SMM affichés
- ✅ Navigation par catégories
- ✅ Panneau admin accessible
- ✅ Système de commandes opérationnel

---

**Une fois le diagnostic terminé, supprimer ces fichiers de test :**
- `simple_test.php`
- `diagnose_tables.php`
- `rebuild_tables.php`
- `repair_database.php`
- `test.php`
- `DIAGNOSTIC_GUIDE.md`