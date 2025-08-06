<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Vérifier l'authentification admin
$auth->requireAuth('admin');

$user = $auth->getCurrentUser();
$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';

$db = Database::getInstance();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Token de sécurité invalide.');
        redirect('/admin/categories.php');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_category') {
        $data = [
            'name' => cleanInput($_POST['name'] ?? ''),
            'description' => cleanInput($_POST['description'] ?? ''),
            'icon' => cleanInput($_POST['icon'] ?? 'fas fa-cog'),
            'color' => cleanInput($_POST['color'] ?? '#6366f1'),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive'
        ];
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Le nom de la catégorie est requis.';
        if (empty($data['description'])) $errors[] = 'La description est requise.';
        
        // Vérifier l'unicité du nom
        $existing = $db->fetchOne("SELECT id FROM categories WHERE name = ?", [$data['name']]);
        if ($existing) $errors[] = 'Une catégorie avec ce nom existe déjà.';
        
        if (empty($errors)) {
            $created = $db->execute("
                INSERT INTO categories (name, description, icon, color, sort_order, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                $data['name'], $data['description'], $data['icon'], 
                $data['color'], $data['sort_order'], $data['status']
            ]);
            
            if ($created) {
                logAdminAction($user['id'], 'category_create', "Catégorie créée: " . $data['name']);
                setFlashMessage('success', 'Catégorie créée avec succès.');
            } else {
                setFlashMessage('error', 'Erreur lors de la création de la catégorie.');
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        redirect('/admin/categories.php');
    }
    
    if ($action === 'update_category') {
        $categoryId = intval($_POST['category_id'] ?? 0);
        $data = [
            'name' => cleanInput($_POST['name'] ?? ''),
            'description' => cleanInput($_POST['description'] ?? ''),
            'icon' => cleanInput($_POST['icon'] ?? 'fas fa-cog'),
            'color' => cleanInput($_POST['color'] ?? '#6366f1'),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'status' => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive'
        ];
        
        $errors = [];
        if ($categoryId <= 0) $errors[] = 'Catégorie invalide.';
        if (empty($data['name'])) $errors[] = 'Le nom de la catégorie est requis.';
        if (empty($data['description'])) $errors[] = 'La description est requise.';
        
        // Vérifier l'unicité du nom (sauf pour la catégorie actuelle)
        $existing = $db->fetchOne("SELECT id FROM categories WHERE name = ? AND id != ?", [$data['name'], $categoryId]);
        if ($existing) $errors[] = 'Une catégorie avec ce nom existe déjà.';
        
        if (empty($errors)) {
            $updated = $db->execute("
                UPDATE categories SET 
                    name = ?, description = ?, icon = ?, color = ?, sort_order = ?, status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $data['name'], $data['description'], $data['icon'], 
                $data['color'], $data['sort_order'], $data['status'], $categoryId
            ]);
            
            if ($updated) {
                logAdminAction($user['id'], 'category_update', "Catégorie modifiée: " . $data['name']);
                setFlashMessage('success', 'Catégorie mise à jour avec succès.');
            } else {
                setFlashMessage('error', 'Erreur lors de la mise à jour de la catégorie.');
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        redirect('/admin/categories.php');
    }
    
    if ($action === 'delete_category') {
        $categoryId = intval($_POST['category_id'] ?? 0);
        if ($categoryId > 0) {
            // Vérifier s'il y a des services liés
            $serviceCount = $db->fetchOne("SELECT COUNT(*) as count FROM services WHERE category_id = ?", [$categoryId])['count'];
            
            if ($serviceCount > 0) {
                setFlashMessage('error', "Impossible de supprimer cette catégorie car elle contient $serviceCount service(s).");
            } else {
                $category = $db->fetchOne("SELECT name FROM categories WHERE id = ?", [$categoryId]);
                $deleted = $db->execute("DELETE FROM categories WHERE id = ?", [$categoryId]);
                
                if ($deleted) {
                    logAdminAction($user['id'], 'category_delete', "Catégorie supprimée: " . $category['name']);
                    setFlashMessage('success', 'Catégorie supprimée avec succès.');
                } else {
                    setFlashMessage('error', 'Erreur lors de la suppression de la catégorie.');
                }
            }
        }
        redirect('/admin/categories.php');
    }
    
    if ($action === 'toggle_status') {
        $categoryId = intval($_POST['category_id'] ?? 0);
        if ($categoryId > 0) {
            $currentStatus = $db->fetchOne("SELECT status, name FROM categories WHERE id = ?", [$categoryId]);
            if ($currentStatus) {
                $newStatus = $currentStatus['status'] === 'active' ? 'inactive' : 'active';
                $updated = $db->execute("UPDATE categories SET status = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $categoryId]);
                
                if ($updated) {
                    logAdminAction($user['id'], 'category_status_toggle', "Catégorie {$currentStatus['name']}: statut changé vers $newStatus");
                    setFlashMessage('success', "Statut de la catégorie mis à jour: $newStatus");
                } else {
                    setFlashMessage('error', 'Erreur lors de la mise à jour du statut.');
                }
            }
        }
        redirect('/admin/categories.php');
    }
}

// Récupérer les catégories avec statistiques
$categories = $db->fetchAll("
    SELECT c.*, 
           (SELECT COUNT(*) FROM services WHERE category_id = c.id) as service_count,
           (SELECT COUNT(*) FROM services WHERE category_id = c.id AND status = 'active') as active_services
    FROM categories c
    ORDER BY c.sort_order ASC, c.created_at DESC
");

$flashMessage = getFlashMessage();

// Icônes disponibles
$availableIcons = [
    'fas fa-heart' => 'Cœur',
    'fab fa-instagram' => 'Instagram',
    'fab fa-tiktok' => 'TikTok',
    'fab fa-youtube' => 'YouTube',
    'fab fa-facebook' => 'Facebook',
    'fab fa-twitter' => 'Twitter',
    'fab fa-linkedin' => 'LinkedIn',
    'fab fa-snapchat' => 'Snapchat',
    'fab fa-spotify' => 'Spotify',
    'fab fa-twitch' => 'Twitch',
    'fab fa-pinterest' => 'Pinterest',
    'fab fa-reddit' => 'Reddit',
    'fab fa-telegram' => 'Telegram',
    'fab fa-whatsapp' => 'WhatsApp',
    'fas fa-bullhorn' => 'Marketing',
    'fas fa-chart-line' => 'Analytics',
    'fas fa-star' => 'Premium',
    'fas fa-cog' => 'Général'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-primary);
        }
        
        .admin-sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .category-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .category-card.inactive {
            opacity: 0.6;
            border-color: var(--text-secondary);
        }
        
        .category-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .category-info {
            flex: 1;
        }
        
        .category-name {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .category-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .category-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .category-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .category-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }
        
        .category-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--bg-primary);
            border-radius: 0.5rem;
        }
        
        .category-stat {
            text-align: center;
        }
        
        .stat-value {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }
        
        .category-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .category-order {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        
        .category-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        
        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            min-width: 35px;
            text-align: center;
        }
        
        .action-btn.edit {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .action-btn.toggle {
            background: rgba(139, 69, 19, 0.1);
            color: #8b4513;
        }
        
        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .action-btn:hover {
            transform: translateY(-1px);
        }
        
        .create-category-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
            z-index: 50;
        }
        
        .create-category-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.4);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--bg-secondary);
            border-radius: 1rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .icon-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .icon-option {
            width: 40px;
            height: 40px;
            border: 2px solid var(--border-color);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--bg-primary);
        }
        
        .icon-option:hover {
            border-color: var(--primary-color);
        }
        
        .icon-option.selected {
            border-color: var(--primary-color);
            background: rgba(99, 102, 241, 0.1);
        }
        
        .color-input {
            width: 100%;
            height: 40px;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .categories-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="admin-page">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-rocket"></i>
                    <span><?= htmlspecialchars($siteName) ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <a href="/admin/" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/orders.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Catalogue</div>
                    <a href="/admin/services.php" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>Services</span>
                    </a>
                    <a href="/admin/categories.php" class="nav-link active">
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Utilisateurs</div>
                    <a href="/admin/users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Clients</span>
                    </a>
                    <a href="/admin/messages.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Configuration</div>
                    <a href="/admin/settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="/admin/pages.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Système</div>
                    <a href="/admin/logs.php" class="nav-link">
                        <i class="fas fa-list-alt"></i>
                        <span>Logs</span>
                    </a>
                    <a href="/" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>
                    <i class="fas fa-tags"></i>
                    Gestion des Catégories
                </h1>
                
                <div class="admin-user">
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="user-role"><?= ucfirst($user['role']) ?></div>
                    </div>
                    <a href="/logout.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashMessage['type'] ?>">
                    <i class="fas fa-<?= $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= $flashMessage['message'] ?>
                </div>
            <?php endif; ?>
            
            <!-- Categories Grid -->
            <?php if (empty($categories)): ?>
                <div class="empty-state" style="padding: 3rem; text-align: center;">
                    <i class="fas fa-tags" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucune catégorie</h3>
                    <p style="color: var(--text-secondary);">Créez votre première catégorie pour organiser vos services.</p>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i>
                        Créer la première catégorie
                    </button>
                </div>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card <?= $category['status'] ?>">
                            <div class="category-header">
                                <div class="category-icon" style="background-color: <?= htmlspecialchars($category['color']) ?>">
                                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                </div>
                                <div class="category-info">
                                    <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                                    <div class="category-status <?= $category['status'] ?>">
                                        <i class="fas fa-circle"></i>
                                        <?= ucfirst($category['status']) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="category-description">
                                <?= htmlspecialchars($category['description']) ?>
                            </div>
                            
                            <div class="category-stats">
                                <div class="category-stat">
                                    <div class="stat-value"><?= $category['service_count'] ?></div>
                                    <div class="stat-label">Services</div>
                                </div>
                                <div class="category-stat">
                                    <div class="stat-value"><?= $category['active_services'] ?></div>
                                    <div class="stat-label">Actifs</div>
                                </div>
                                <div class="category-stat">
                                    <div class="stat-value"><?= $category['sort_order'] ?></div>
                                    <div class="stat-label">Ordre</div>
                                </div>
                            </div>
                            
                            <div class="category-meta">
                                <div class="category-order">
                                    Créé le <?= formatDate($category['created_at']) ?>
                                </div>
                            </div>
                            
                            <div class="category-actions">
                                <button class="action-btn edit" onclick="editCategory(<?= $category['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn toggle" onclick="toggleCategory(<?= $category['id'] ?>)">
                                    <i class="fas fa-<?= $category['status'] === 'active' ? 'eye-slash' : 'eye' ?>"></i>
                                </button>
                                <?php if ($category['service_count'] == 0): ?>
                                    <button class="action-btn delete" onclick="deleteCategory(<?= $category['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Bouton de création -->
    <button class="create-category-btn" onclick="openCreateModal()">
        <i class="fas fa-plus"></i>
    </button>
    
    <!-- Modal pour créer/éditer une catégorie -->
    <div class="modal" id="category-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Créer une catégorie</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="category-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="create_category" id="form-action">
                <input type="hidden" name="category_id" id="category-id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="category-name">Nom de la catégorie *</label>
                        <input type="text" id="category-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category-status">Statut</label>
                        <select id="category-status" name="status">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="category-description">Description *</label>
                        <textarea id="category-description" name="description" rows="3" required 
                                  placeholder="Description de la catégorie..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Icône</label>
                        <input type="hidden" id="selected-icon" name="icon" value="fas fa-cog">
                        <div class="icon-selector">
                            <?php foreach ($availableIcons as $iconClass => $iconName): ?>
                                <div class="icon-option" data-icon="<?= $iconClass ?>" title="<?= $iconName ?>">
                                    <i class="<?= $iconClass ?>"></i>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category-color">Couleur</label>
                        <input type="color" id="category-color" name="color" value="#6366f1" class="color-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="category-order">Ordre d'affichage</label>
                        <input type="number" id="category-order" name="sort_order" value="0" min="0">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span id="submit-text">Créer la catégorie</span>
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openCreateModal() {
            document.getElementById('modal-title').textContent = 'Créer une catégorie';
            document.getElementById('form-action').value = 'create_category';
            document.getElementById('submit-text').textContent = 'Créer la catégorie';
            document.getElementById('category-form').reset();
            document.getElementById('selected-icon').value = 'fas fa-cog';
            updateIconSelection();
            document.getElementById('category-modal').classList.add('active');
        }
        
        function editCategory(categoryId) {
            // Pour simplifier, on va ouvrir le modal et laisser l'utilisateur remplir
            // Dans une vraie app, on ferait un appel AJAX pour récupérer les données
            document.getElementById('modal-title').textContent = 'Modifier la catégorie';
            document.getElementById('form-action').value = 'update_category';
            document.getElementById('category-id').value = categoryId;
            document.getElementById('submit-text').textContent = 'Mettre à jour';
            document.getElementById('category-modal').classList.add('active');
        }
        
        function toggleCategory(categoryId) {
            if (confirm('Êtes-vous sûr de vouloir changer le statut de cette catégorie ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="category_id" value="${categoryId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCategory(categoryId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete_category">
                    <input type="hidden" name="category_id" value="${categoryId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }
        
        // Gestion de la sélection d'icône
        document.addEventListener('DOMContentLoaded', function() {
            const iconOptions = document.querySelectorAll('.icon-option');
            const selectedIconInput = document.getElementById('selected-icon');
            
            iconOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const iconClass = this.getAttribute('data-icon');
                    selectedIconInput.value = iconClass;
                    updateIconSelection();
                });
            });
            
            updateIconSelection();
        });
        
        function updateIconSelection() {
            const selectedIcon = document.getElementById('selected-icon').value;
            document.querySelectorAll('.icon-option').forEach(option => {
                option.classList.remove('selected');
                if (option.getAttribute('data-icon') === selectedIcon) {
                    option.classList.add('selected');
                }
            });
        }
        
        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
        
        // Fermer modal en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal();
            }
        });
    </script>
</body>
</html>