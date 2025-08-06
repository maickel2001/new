# 🗃️ Installation SQL - MaickelSMM

## 📋 **Script SQL Adapté Fourni**

**Fichier :** `database_adapte.sql`
- ✅ **Compatible** avec votre structure actuelle
- ❌ **SANS colonne `username`** - Authentification par email uniquement
- 🔧 **Optimisé** pour les fichiers minimaux
- 📊 **Données complètes** incluses

## 🚀 **Installation via phpMyAdmin**

### **Étape 1 : Accès phpMyAdmin**
1. **Panneau Hostinger** → **Bases de données**
2. Cliquez sur **"Accéder à phpMyAdmin"**
3. Sélectionnez votre base : **`u634930929_Ino`**

### **Étape 2 : Import du Script**
1. Cliquez sur l'onglet **"Importer"**
2. **"Choisir un fichier"** → Sélectionnez `database_adapte.sql`
3. **Format :** Laissez "SQL"
4. Cliquez **"Exécuter"**

### **Étape 3 : Vérification**
Après import, vous devriez voir :
- ✅ **8 tables** créées
- ✅ **Utilisateur admin** créé
- ✅ **Services d'exemple** ajoutés
- ✅ **Paramètres** configurés

## 🗂️ **Structure des Tables Créées**

### **1. `users` - Utilisateurs (SANS username)**
```sql
- id, email, password, first_name, last_name
- phone, role, status, email_verified
- created_at, updated_at
```

### **2. `categories` - Catégories de services**
```sql
- id, name, description, icon, sort_order
- status, created_at, updated_at
```

### **3. `services` - Services SMM**
```sql
- id, category_id, name, description
- price_per_1000, min_order, max_order
- status, sort_order, created_at, updated_at
```

### **4. `orders` - Commandes clients**
```sql
- id, user_id, service_id, quantity, link
- charge, total_amount, status, payment_method
- payment_proof, notes, created_at, updated_at
```

### **5. `settings` - Configuration site**
```sql
- id, setting_key, setting_value, description
- created_at, updated_at
```

### **6. `messages` - Support client**
```sql
- id, user_id, name, email, subject, message
- status, admin_reply, created_at, updated_at
```

### **7. `logs` - Journal système**
```sql
- id, user_id, action, description
- ip_address, user_agent, created_at
```

## 📊 **Données Incluses**

### **👤 Utilisateur Admin**
- **Email :** `admin@maickelsmm.com`
- **Mot de passe :** `password123`
- **Rôle :** `superadmin`
- **Status :** `active`

### **🏷️ Catégories (8)**
1. Instagram
2. Facebook  
3. YouTube
4. TikTok
5. Twitter
6. LinkedIn
7. Telegram
8. WhatsApp

### **🛍️ Services (30+)**
- **Instagram :** Followers, Likes, Vues Stories, Comments, Reels
- **Facebook :** Page Likes, Post Likes, Followers, Shares, Video Views
- **YouTube :** Views, Subscribers, Likes, Comments, Watch Time
- **TikTok :** Followers, Likes, Views, Shares, Comments
- **Twitter :** Followers, Likes, Retweets, Impressions
- **LinkedIn :** Connections, Post Likes, Followers
- **Telegram :** Members, Post Views
- **WhatsApp :** Group Members, Status Views

### **⚙️ Paramètres Système (20+)**
- Configuration du site (nom, description, mots-clés)
- Paramètres de paiement (Mobile Money, instructions)
- Informations de contact (email, téléphone, réseaux sociaux)
- Limites de commande (min/max)
- Activation des fonctionnalités

## 🔧 **Fonctionnalités Avancées**

### **📈 Vue Statistiques**
```sql
CREATE VIEW admin_stats AS ...
```
- Statistiques temps réel pour l'admin
- Nombre d'utilisateurs, commandes, revenus

### **🚀 Index de Performance**
- Optimisation des requêtes fréquentes
- Index sur status, dates, relations

### **📝 Triggers de Logs**
- Logs automatiques des connexions
- Tracking des nouvelles commandes
- Historique des actions importantes

## ✅ **Après Installation**

### **1. Testez la Configuration**
```
votre-site.com/detect_db_config.php
```

### **2. Vérifiez les Tables**
```
votre-site.com/check_users_table.php
```

### **3. Connectez-vous**
```
votre-site.com/login_minimal.php
```
- Email : `admin@maickelsmm.com`
- Mot de passe : `password123`

### **4. Accédez à l'Admin**
```
votre-site.com/admin_minimal.php
```

## 🔒 **Sécurité Post-Installation**

### **Actions Obligatoires :**
1. **Changez le mot de passe admin** immédiatement
2. **Modifiez l'email admin** si souhaité
3. **Configurez les vrais numéros** Mobile Money
4. **Ajustez les prix** selon vos tarifs

### **Dans l'Admin :**
1. **Paramètres** → **Général** → Modifiez les informations
2. **Utilisateurs** → **Admin** → Changez le mot de passe
3. **Services** → Ajustez les prix et descriptions
4. **Paramètres** → **Paiements** → Configurez Mobile Money

## 🎯 **Compatibilité**

Ce script SQL est **100% compatible** avec :
- ✅ `login_minimal.php`
- ✅ `admin_minimal.php`
- ✅ `dashboard_minimal.php`
- ✅ `check_users_table.php`
- ✅ Tous les fichiers du projet

## 🆘 **Dépannage**

### **Erreur d'Import :**
- Vérifiez que la base `u634930929_Ino` existe
- Assurez-vous d'avoir les droits d'écriture
- Contactez le support Hostinger si nécessaire

### **Tables non créées :**
- Exécutez le script par parties (copier-coller)
- Vérifiez les logs d'erreur phpMyAdmin

### **Données manquantes :**
- Re-exécutez seulement la partie INSERT
- Vérifiez que l'admin existe avec `check_users_table.php`

**Avec ce script SQL, votre site MaickelSMM sera 100% opérationnel !** 🚀