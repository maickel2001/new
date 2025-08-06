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
        redirect('/admin/services.php');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_service') {
        $data = [
            'category_id' => intval($_POST['category_id'] ?? 0),
            'name' => cleanInput($_POST['name'] ?? ''),
            'description' => cleanInput($_POST['description'] ?? ''),
            'min_quantity' => intval($_POST['min_quantity'] ?? 100),
            'max_quantity' => intval($_POST['max_quantity'] ?? 10000),
            'price_per_1000' => floatval($_POST['price_per_1000'] ?? 0),
            'delivery_time' => cleanInput($_POST['delivery_time'] ?? '1-3 jours'),
            'guarantee' => ($_POST['guarantee'] ?? 'yes') === 'yes' ? 'yes' : 'no',
            'status' => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive',
            'sort_order' => intval($_POST['sort_order'] ?? 0)
        ];
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'Le nom du service est requis.';
        if (empty($data['description'])) $errors[] = 'La description est requise.';
        if ($data['category_id'] <= 0) $errors[] = 'Veuillez sélectionner une catégorie.';
        if ($data['price_per_1000'] <= 0) $errors[] = 'Le prix doit être supérieur à 0.';
        if ($data['min_quantity'] <= 0) $errors[] = 'La quantité minimum doit être supérieure à 0.';
        if ($data['max_quantity'] <= $data['min_quantity']) $errors[] = 'La quantité maximum doit être supérieure à la quantité minimum.';
        
        if (empty($errors)) {
            $created = $db->execute("
                INSERT INTO services (category_id, name, description, min_quantity, max_quantity, 
                                    price_per_1000, delivery_time, guarantee, status, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $data['category_id'], $data['name'], $data['description'], 
                $data['min_quantity'], $data['max_quantity'], $data['price_per_1000'],
                $data['delivery_time'], $data['guarantee'], $data['status'], $data['sort_order']
            ]);
            
            if ($created) {
                logAdminAction($user['id'], 'service_create', "Service créé: " . $data['name']);
                setFlashMessage('success', 'Service créé avec succès.');
            } else {
                setFlashMessage('error', 'Erreur lors de la création du service.');
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        redirect('/admin/services.php');
    }
    
    if ($action === 'update_service') {
        $serviceId = intval($_POST['service_id'] ?? 0);
        $data = [
            'category_id' => intval($_POST['category_id'] ?? 0),
            'name' => cleanInput($_POST['name'] ?? ''),
            'description' => cleanInput($_POST['description'] ?? ''),
            'min_quantity' => intval($_POST['min_quantity'] ?? 100),
            'max_quantity' => intval($_POST['max_quantity'] ?? 10000),
            'price_per_1000' => floatval($_POST['price_per_1000'] ?? 0),
            'delivery_time' => cleanInput($_POST['delivery_time'] ?? '1-3 jours'),
            'guarantee' => ($_POST['guarantee'] ?? 'yes') === 'yes' ? 'yes' : 'no',
            'status' => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive',
            'sort_order' => intval($_POST['sort_order'] ?? 0)
        ];
        
        $errors = [];
        if ($serviceId <= 0) $errors[] = 'Service invalide.';
        if (empty($data['name'])) $errors[] = 'Le nom du service est requis.';
        if (empty($data['description'])) $errors[] = 'La description est requise.';
        if ($data['category_id'] <= 0) $errors[] = 'Veuillez sélectionner une catégorie.';
        if ($data['price_per_1000'] <= 0) $errors[] = 'Le prix doit être supérieur à 0.';
        
        if (empty($errors)) {
            $updated = $db->execute("
                UPDATE services SET 
                    category_id = ?, name = ?, description = ?, min_quantity = ?, max_quantity = ?,
                    price_per_1000 = ?, delivery_time = ?, guarantee = ?, status = ?, sort_order = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $data['category_id'], $data['name'], $data['description'], 
                $data['min_quantity'], $data['max_quantity'], $data['price_per_1000'],
                $data['delivery_time'], $data['guarantee'], $data['status'], $data['sort_order'],
                $serviceId
            ]);
            
            if ($updated) {
                logAdminAction($user['id'], 'service_update', "Service modifié: " . $data['name']);
                setFlashMessage('success', 'Service mis à jour avec succès.');
            } else {
                setFlashMessage('error', 'Erreur lors de la mise à jour du service.');
            }
        } else {
            setFlashMessage('error', implode('<br>', $errors));
        }
        redirect('/admin/services.php');
    }
    
    if ($action === 'delete_service') {
        $serviceId = intval($_POST['service_id'] ?? 0);
        if ($serviceId > 0) {
            // Vérifier s'il y a des commandes liées
            $orderCount = $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE service_id = ?", [$serviceId])['count'];
            
            if ($orderCount > 0) {
                setFlashMessage('error', 'Impossible de supprimer ce service car il est lié à des commandes existantes.');
            } else {
                $deleted = $db->execute("DELETE FROM services WHERE id = ?", [$serviceId]);
                if ($deleted) {
                    logAdminAction($user['id'], 'service_delete', "Service supprimé: ID #$serviceId");
                    setFlashMessage('success', 'Service supprimé avec succès.');
                } else {
                    setFlashMessage('error', 'Erreur lors de la suppression du service.');
                }
            }
        }
        redirect('/admin/services.php');
    }
    
    if ($action === 'toggle_status') {
        $serviceId = intval($_POST['service_id'] ?? 0);
        if ($serviceId > 0) {
            $currentStatus = $db->fetchOne("SELECT status FROM services WHERE id = ?", [$serviceId]);
            if ($currentStatus) {
                $newStatus = $currentStatus['status'] === 'active' ? 'inactive' : 'active';
                $updated = $db->execute("UPDATE services SET status = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $serviceId]);
                
                if ($updated) {
                    logAdminAction($user['id'], 'service_status_toggle', "Service #$serviceId: statut changé vers $newStatus");
                    setFlashMessage('success', "Statut du service mis à jour: $newStatus");
                } else {
                    setFlashMessage('error', 'Erreur lors de la mise à jour du statut.');
                }
            }
        }
        redirect('/admin/services.php');
    }
}

// Filtres et pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$categoryFilter = intval($_GET['category'] ?? 0);
$statusFilter = cleanInput($_GET['status'] ?? '');
$searchQuery = cleanInput($_GET['search'] ?? '');

// Construction de la requête
$whereConditions = ['1=1'];
$params = [];

if ($categoryFilter > 0) {
    $whereConditions[] = 's.category_id = ?';
    $params[] = $categoryFilter;
}

if (!empty($statusFilter)) {
    $whereConditions[] = 's.status = ?';
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = '(s.name LIKE ? OR s.description LIKE ?)';
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm]);
}

$whereClause = implode(' AND ', $whereConditions);

// Compter le total
$totalServices = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM services s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalServices / $limit);

// Récupérer les services
$services = $db->fetchAll("
    SELECT s.*, c.name as category_name, c.icon as category_icon,
           (SELECT COUNT(*) FROM orders WHERE service_id = s.id) as order_count
    FROM services s
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE $whereClause
    ORDER BY s.sort_order ASC, s.created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Récupérer les catégories pour les formulaires
$categories = getCategories();

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services - <?= htmlspecialchars($siteName) ?></title>
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
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .service-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .service-card.inactive {
            opacity: 0.6;
            border-color: var(--text-secondary);
        }
        
        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .service-title {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            line-height: 1.3;
        }
        
        .service-category {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .service-status {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .service-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .service-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .service-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .service-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .service-detail {
            text-align: center;
            padding: 0.75rem;
            background: var(--bg-primary);
            border-radius: 0.5rem;
        }
        
        .service-detail-value {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }
        
        .service-detail-label {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }
        
        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .service-orders {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        
        .service-guarantee {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }
        
        .guarantee-yes {
            color: var(--success);
        }
        
        .guarantee-no {
            color: var(--text-secondary);
        }
        
        .service-actions {
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
        
        .action-btn.view {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
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
        
        .create-service-btn {
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
        
        .create-service-btn:hover {
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
            max-width: 600px;
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
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .services-grid {
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
                    <a href="/admin/services.php" class="nav-link active">
                        <i class="fas fa-cogs"></i>
                        <span>Services</span>
                    </a>
                    <a href="/admin/categories.php" class="nav-link">
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
                    <i class="fas fa-cogs"></i>
                    Gestion des Services
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
            
            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" 
                                   placeholder="Nom du service...">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select id="category" name="category">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $categoryFilter == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Filtrer
                            </button>
                            <a href="/admin/services.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Services Grid -->
            <?php if (empty($services)): ?>
                <div class="empty-state" style="padding: 3rem; text-align: center;">
                    <i class="fas fa-cogs" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucun service</h3>
                    <p style="color: var(--text-secondary);">Aucun service ne correspond aux critères sélectionnés.</p>
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i>
                        Créer le premier service
                    </button>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-card <?= $service['status'] ?>">
                            <div class="service-header">
                                <div>
                                    <h3 class="service-title"><?= htmlspecialchars($service['name']) ?></h3>
                                    <div class="service-category">
                                        <i class="<?= htmlspecialchars($service['category_icon']) ?>"></i>
                                        <?= htmlspecialchars($service['category_name']) ?>
                                    </div>
                                </div>
                                <div class="service-status <?= $service['status'] ?>">
                                    <i class="fas fa-circle"></i>
                                    <?= ucfirst($service['status']) ?>
                                </div>
                            </div>
                            
                            <div class="service-description">
                                <?= htmlspecialchars($service['description']) ?>
                            </div>
                            
                            <div class="service-details">
                                <div class="service-detail">
                                    <div class="service-detail-value"><?= formatPrice($service['price_per_1000']) ?></div>
                                    <div class="service-detail-label">Prix / 1000</div>
                                </div>
                                <div class="service-detail">
                                    <div class="service-detail-value"><?= $service['delivery_time'] ?></div>
                                    <div class="service-detail-label">Livraison</div>
                                </div>
                                <div class="service-detail">
                                    <div class="service-detail-value"><?= number_format($service['min_quantity']) ?> - <?= number_format($service['max_quantity']) ?></div>
                                    <div class="service-detail-label">Quantité</div>
                                </div>
                                <div class="service-detail">
                                    <div class="service-detail-value"><?= $service['sort_order'] ?></div>
                                    <div class="service-detail-label">Ordre</div>
                                </div>
                            </div>
                            
                            <div class="service-meta">
                                <div class="service-orders">
                                    <i class="fas fa-shopping-cart"></i>
                                    <?= $service['order_count'] ?> commande<?= $service['order_count'] > 1 ? 's' : '' ?>
                                </div>
                                <div class="service-guarantee <?= $service['guarantee'] === 'yes' ? 'guarantee-yes' : 'guarantee-no' ?>">
                                    <i class="fas fa-<?= $service['guarantee'] === 'yes' ? 'shield-alt' : 'times' ?>"></i>
                                    <?= $service['guarantee'] === 'yes' ? 'Garantie' : 'Sans garantie' ?>
                                </div>
                            </div>
                            
                            <div class="service-actions">
                                <button class="action-btn edit" onclick="editService(<?= $service['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn toggle" onclick="toggleService(<?= $service['id'] ?>)">
                                    <i class="fas fa-<?= $service['status'] === 'active' ? 'eye-slash' : 'eye' ?>"></i>
                                </button>
                                <?php if ($service['order_count'] == 0): ?>
                                    <button class="action-btn delete" onclick="deleteService(<?= $service['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&<?= http_build_query($_GET) ?>">&laquo; Précédent</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= http_build_query($_GET) ?>">Suivant &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Bouton de création -->
    <button class="create-service-btn" onclick="openCreateModal()">
        <i class="fas fa-plus"></i>
    </button>
    
    <!-- Modal pour créer/éditer un service -->
    <div class="modal" id="service-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-title">Créer un service</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="service-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="create_service" id="form-action">
                <input type="hidden" name="service_id" id="service-id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="service-name">Nom du service *</label>
                        <input type="text" id="service-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-category">Catégorie *</label>
                        <select id="service-category" name="category_id" required>
                            <option value="">Sélectionner une catégorie</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="service-description">Description *</label>
                        <textarea id="service-description" name="description" rows="3" required 
                                  placeholder="Description détaillée du service..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-price">Prix par 1000 (FCFA) *</label>
                        <input type="number" id="service-price" name="price_per_1000" step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-delivery">Temps de livraison</label>
                        <input type="text" id="service-delivery" name="delivery_time" value="1-3 jours" 
                               placeholder="ex: 1-3 jours, 24h, Instantané">
                    </div>
                    
                    <div class="form-group">
                        <label for="service-min">Quantité minimum</label>
                        <input type="number" id="service-min" name="min_quantity" value="100" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-max">Quantité maximum</label>
                        <input type="number" id="service-max" name="max_quantity" value="10000" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-guarantee">Garantie</label>
                        <select id="service-guarantee" name="guarantee">
                            <option value="yes">Avec garantie</option>
                            <option value="no">Sans garantie</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-status">Statut</label>
                        <select id="service-status" name="status">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="service-order">Ordre d'affichage</label>
                        <input type="number" id="service-order" name="sort_order" value="0" min="0">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span id="submit-text">Créer le service</span>
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentServiceData = null;
        
        function openCreateModal() {
            document.getElementById('modal-title').textContent = 'Créer un service';
            document.getElementById('form-action').value = 'create_service';
            document.getElementById('submit-text').textContent = 'Créer le service';
            document.getElementById('service-form').reset();
            document.getElementById('service-modal').classList.add('active');
        }
        
        function editService(serviceId) {
            // Récupérer les données du service depuis les cartes
            const serviceCard = document.querySelector(`[data-service-id="${serviceId}"]`);
            
            // Pour simplifier, on va ouvrir le modal et laisser l'utilisateur remplir
            // Dans une vraie app, on ferait un appel AJAX pour récupérer les données
            document.getElementById('modal-title').textContent = 'Modifier le service';
            document.getElementById('form-action').value = 'update_service';
            document.getElementById('service-id').value = serviceId;
            document.getElementById('submit-text').textContent = 'Mettre à jour';
            document.getElementById('service-modal').classList.add('active');
        }
        
        function toggleService(serviceId) {
            if (confirm('Êtes-vous sûr de vouloir changer le statut de ce service ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="service_id" value="${serviceId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteService(serviceId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete_service">
                    <input type="hidden" name="service_id" value="${serviceId}">
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
        
        // Validation du formulaire
        document.getElementById('service-form').addEventListener('submit', function(e) {
            const minQty = parseInt(document.getElementById('service-min').value);
            const maxQty = parseInt(document.getElementById('service-max').value);
            
            if (maxQty <= minQty) {
                e.preventDefault();
                alert('La quantité maximum doit être supérieure à la quantité minimum.');
                return false;
            }
        });
        
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