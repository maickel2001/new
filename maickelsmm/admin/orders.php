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
        redirect('/admin/orders.php');
    }
    
    $action = $_POST['action'] ?? '';
    $orderId = intval($_POST['order_id'] ?? 0);
    
    if ($action === 'update_status' && $orderId > 0) {
        $newStatus = cleanInput($_POST['status'] ?? '');
        $adminNote = cleanInput($_POST['admin_note'] ?? '');
        
        $validStatuses = ['pending', 'processing', 'in_progress', 'completed', 'cancelled', 'refunded'];
        
        if (in_array($newStatus, $validStatuses)) {
            $updated = updateOrderStatus($orderId, $newStatus, $adminNote);
            if ($updated) {
                // Log de l'action admin
                logAdminAction($user['id'], 'order_status_update', "Commande #$orderId: statut changé vers $newStatus");
                setFlashMessage('success', "Statut de la commande #$orderId mis à jour avec succès.");
            } else {
                setFlashMessage('error', 'Erreur lors de la mise à jour du statut.');
            }
        } else {
            setFlashMessage('error', 'Statut invalide.');
        }
        redirect('/admin/orders.php');
    }
    
    if ($action === 'delete_order' && $orderId > 0) {
        $deleted = $db->execute("DELETE FROM orders WHERE id = ?", [$orderId]);
        if ($deleted) {
            logAdminAction($user['id'], 'order_delete', "Commande #$orderId supprimée");
            setFlashMessage('success', "Commande #$orderId supprimée avec succès.");
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression.');
        }
        redirect('/admin/orders.php');
    }
}

// Filtres et pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$statusFilter = cleanInput($_GET['status'] ?? '');
$searchQuery = cleanInput($_GET['search'] ?? '');
$dateFrom = cleanInput($_GET['date_from'] ?? '');
$dateTo = cleanInput($_GET['date_to'] ?? '');

// Construction de la requête
$whereConditions = ['1=1'];
$params = [];

if (!empty($statusFilter)) {
    $whereConditions[] = 'o.status = ?';
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = '(o.id LIKE ? OR s.name LIKE ? OR COALESCE(u.first_name, o.guest_name) LIKE ? OR COALESCE(u.email, o.guest_email) LIKE ?)';
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($dateFrom)) {
    $whereConditions[] = 'DATE(o.created_at) >= ?';
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $whereConditions[] = 'DATE(o.created_at) <= ?';
    $params[] = $dateTo;
}

$whereClause = implode(' AND ', $whereConditions);

// Compter le total
$totalOrders = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    LEFT JOIN users u ON o.user_id = u.id
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalOrders / $limit);

// Récupérer les commandes
$orders = $db->fetchAll("
    SELECT o.*, s.name as service_name, s.category_id, c.name as category_name,
           COALESCE(u.first_name, o.guest_name) as customer_name,
           COALESCE(u.email, o.guest_email) as customer_email,
           u.id as user_id,
           p.payment_method, p.payment_proof, p.status as payment_status
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE $whereClause
    ORDER BY o.created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Statistiques pour les filtres
$statusStats = $db->fetchAll("
    SELECT status, COUNT(*) as count
    FROM orders
    GROUP BY status
    ORDER BY count DESC
");

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - <?= htmlspecialchars($siteName) ?></title>
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
        
        .filters-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .status-badge.all {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .status-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .status-badge.processing {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        .status-badge.in_progress {
            background: rgba(139, 69, 19, 0.1);
            color: #8b4513;
            border: 1px solid rgba(139, 69, 19, 0.2);
        }
        
        .status-badge.completed {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .status-badge.cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .status-badge.refunded {
            background: rgba(168, 85, 247, 0.1);
            color: #a855f7;
            border: 1px solid rgba(168, 85, 247, 0.2);
        }
        
        .status-badge.active {
            background: var(--primary-color);
            color: white;
        }
        
        .orders-table {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .orders-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .orders-table th {
            background: var(--bg-primary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .orders-table td {
            color: var(--text-primary);
        }
        
        .order-id {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .customer-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .customer-name {
            font-weight: 500;
        }
        
        .customer-email {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .service-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .service-name {
            font-weight: 500;
        }
        
        .service-details {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .order-amount {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .order-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .action-btn {
            padding: 0.25rem 0.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .action-btn.view {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .action-btn.edit {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            text-decoration: none;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .pagination a:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .pagination .current {
            background: var(--primary-color);
            color: white;
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
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-title {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .close-modal {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                font-size: 0.9rem;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body class="admin-page">
    <div class="admin-layout">
        <!-- Sidebar (copié du dashboard) -->
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
                    <a href="/admin/orders.php" class="nav-link active">
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
                    <i class="fas fa-shopping-cart"></i>
                    Gestion des Commandes
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
                    <?= htmlspecialchars($flashMessage['message']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" 
                                   placeholder="ID, service, client...">
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>En attente</option>
                                <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>En traitement</option>
                                <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Terminé</option>
                                <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                                <option value="refunded" <?= $statusFilter === 'refunded' ? 'selected' : '' ?>>Remboursé</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="date_from">Date début</label>
                            <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_to">Date fin</label>
                            <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <div class="status-badges">
                            <a href="/admin/orders.php" class="status-badge all <?= empty($statusFilter) ? 'active' : '' ?>">
                                <i class="fas fa-list"></i>
                                Toutes (<?= $totalOrders ?>)
                            </a>
                            <?php foreach ($statusStats as $stat): ?>
                                <a href="/admin/orders.php?status=<?= $stat['status'] ?>" 
                                   class="status-badge <?= $stat['status'] ?> <?= $statusFilter === $stat['status'] ? 'active' : '' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $stat['status'])) ?> (<?= $stat['count'] ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Filtrer
                            </button>
                            <a href="/admin/orders.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Table des commandes -->
            <div class="orders-table">
                <div class="table-header">
                    <h2 class="table-title">
                        <i class="fas fa-list"></i>
                        Commandes (<?= $totalOrders ?>)
                    </h2>
                </div>
                
                <div class="table-container">
                    <?php if (empty($orders)): ?>
                        <div class="empty-state" style="padding: 3rem; text-align: center;">
                            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucune commande</h3>
                            <p style="color: var(--text-secondary);">Aucune commande ne correspond aux critères sélectionnés.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Quantité</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <span class="order-id">#<?= $order['id'] ?></span>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <div class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></div>
                                                <div class="customer-email"><?= htmlspecialchars($order['customer_email']) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="service-info">
                                                <div class="service-name"><?= htmlspecialchars($order['service_name']) ?></div>
                                                <div class="service-details"><?= htmlspecialchars($order['category_name']) ?></div>
                                            </div>
                                        </td>
                                        <td><?= number_format($order['quantity']) ?></td>
                                        <td>
                                            <span class="order-amount"><?= formatPrice($order['total_amount']) ?></span>
                                        </td>
                                        <td>
                                            <span class="order-status <?= $order['status'] ?>">
                                                <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($order['created_at']) ?></td>
                                        <td>
                                            <div class="order-actions">
                                                <button class="action-btn view" onclick="viewOrder(<?= $order['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn edit" onclick="editOrder(<?= $order['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn delete" onclick="deleteOrder(<?= $order['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            
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
    
    <!-- Modal pour éditer le statut -->
    <div class="modal" id="edit-order-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modifier la commande</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="edit-order-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" id="edit-order-id">
                
                <div class="form-group">
                    <label for="edit-status">Nouveau statut</label>
                    <select id="edit-status" name="status" required>
                        <option value="pending">En attente</option>
                        <option value="processing">En traitement</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="cancelled">Annulé</option>
                        <option value="refunded">Remboursé</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="admin-note">Note admin (optionnel)</label>
                    <textarea id="admin-note" name="admin_note" rows="3" 
                              placeholder="Commentaire sur le changement de statut..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Mettre à jour
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Gestion des modals
        function viewOrder(orderId) {
            window.open(`/order.php?id=${orderId}`, '_blank');
        }
        
        function editOrder(orderId) {
            document.getElementById('edit-order-id').value = orderId;
            document.getElementById('edit-order-modal').classList.add('active');
        }
        
        function deleteOrder(orderId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete_order">
                    <input type="hidden" name="order_id" value="${orderId}">
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