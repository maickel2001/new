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
        redirect('/admin/users.php');
    }
    
    $action = $_POST['action'] ?? '';
    $userId = intval($_POST['user_id'] ?? 0);
    
    if ($action === 'toggle_status' && $userId > 0) {
        // Empêcher la désactivation de son propre compte
        if ($userId == $user['id']) {
            setFlashMessage('error', 'Vous ne pouvez pas modifier votre propre statut.');
            redirect('/admin/users.php');
        }
        
        $currentUser = $db->fetchOne("SELECT status, first_name, last_name FROM users WHERE id = ?", [$userId]);
        if ($currentUser) {
            $newStatus = $currentUser['status'] === 'active' ? 'blocked' : 'active';
            $updated = $db->execute("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $userId]);
            
            if ($updated) {
                $userName = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
                logAdminAction($user['id'], 'user_status_toggle', "Utilisateur $userName (#$userId): statut changé vers $newStatus");
                setFlashMessage('success', "Statut de l'utilisateur mis à jour: $newStatus");
            } else {
                setFlashMessage('error', 'Erreur lors de la mise à jour du statut.');
            }
        }
        redirect('/admin/users.php');
    }
    
    if ($action === 'delete_user' && $userId > 0) {
        // Empêcher la suppression de son propre compte
        if ($userId == $user['id']) {
            setFlashMessage('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/admin/users.php');
        }
        
        // Vérifier s'il y a des commandes liées
        $orderCount = $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE user_id = ?", [$userId])['count'];
        
        if ($orderCount > 0) {
            setFlashMessage('error', 'Impossible de supprimer cet utilisateur car il a des commandes associées.');
        } else {
            $currentUser = $db->fetchOne("SELECT first_name, last_name FROM users WHERE id = ?", [$userId]);
            $deleted = $db->execute("DELETE FROM users WHERE id = ?", [$userId]);
            
            if ($deleted) {
                $userName = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
                logAdminAction($user['id'], 'user_delete', "Utilisateur supprimé: $userName (#$userId)");
                setFlashMessage('success', 'Utilisateur supprimé avec succès.');
            } else {
                setFlashMessage('error', 'Erreur lors de la suppression de l\'utilisateur.');
            }
        }
        redirect('/admin/users.php');
    }
    
    if ($action === 'verify_email' && $userId > 0) {
        $updated = $db->execute("UPDATE users SET email_verified = 1, email_verification_token = NULL, updated_at = NOW() WHERE id = ?", [$userId]);
        
        if ($updated) {
            logAdminAction($user['id'], 'user_email_verify', "Email vérifié manuellement pour l'utilisateur #$userId");
            setFlashMessage('success', 'Email vérifié avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la vérification de l\'email.');
        }
        redirect('/admin/users.php');
    }
    
    if ($action === 'change_role' && $userId > 0) {
        $newRole = cleanInput($_POST['role'] ?? '');
        $validRoles = ['user', 'admin', 'superadmin'];
        
        // Empêcher la modification de son propre rôle sauf si superadmin
        if ($userId == $user['id'] && $user['role'] !== 'superadmin') {
            setFlashMessage('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            redirect('/admin/users.php');
        }
        
        // Seul un superadmin peut créer d'autres superadmins
        if ($newRole === 'superadmin' && $user['role'] !== 'superadmin') {
            setFlashMessage('error', 'Seul un superadmin peut créer d\'autres superadmins.');
            redirect('/admin/users.php');
        }
        
        if (in_array($newRole, $validRoles)) {
            $currentUser = $db->fetchOne("SELECT first_name, last_name, role FROM users WHERE id = ?", [$userId]);
            $updated = $db->execute("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?", [$newRole, $userId]);
            
            if ($updated) {
                $userName = $currentUser['first_name'] . ' ' . $currentUser['last_name'];
                logAdminAction($user['id'], 'user_role_change', "Rôle changé pour $userName (#$userId): {$currentUser['role']} → $newRole");
                setFlashMessage('success', "Rôle de l'utilisateur mis à jour: $newRole");
            } else {
                setFlashMessage('error', 'Erreur lors de la mise à jour du rôle.');
            }
        } else {
            setFlashMessage('error', 'Rôle invalide.');
        }
        redirect('/admin/users.php');
    }
}

// Filtres et pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$roleFilter = cleanInput($_GET['role'] ?? '');
$statusFilter = cleanInput($_GET['status'] ?? '');
$searchQuery = cleanInput($_GET['search'] ?? '');

// Construction de la requête
$whereConditions = ['1=1'];
$params = [];

if (!empty($roleFilter)) {
    $whereConditions[] = 'role = ?';
    $params[] = $roleFilter;
}

if (!empty($statusFilter)) {
    $whereConditions[] = 'status = ?';
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)';
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = implode(' AND ', $whereConditions);

// Compter le total
$totalUsers = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM users
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalUsers / $limit);

// Récupérer les utilisateurs
$users = $db->fetchAll("
    SELECT u.*,
           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
           (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id AND status IN ('completed', 'in_progress')) as total_spent
    FROM users u
    WHERE $whereClause
    ORDER BY u.created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Statistiques pour les filtres
$roleStats = $db->fetchAll("
    SELECT role, COUNT(*) as count
    FROM users
    GROUP BY role
    ORDER BY count DESC
");

$statusStats = $db->fetchAll("
    SELECT status, COUNT(*) as count
    FROM users
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
    <title>Gestion des Utilisateurs - <?= htmlspecialchars($siteName) ?></title>
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
        
        .users-table {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .users-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .users-table th {
            background: var(--bg-primary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .users-table td {
            color: var(--text-primary);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .user-name {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .user-email {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        
        .user-role {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .user-role.user {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }
        
        .user-role.admin {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .user-role.superadmin {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .user-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .user-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .user-status.blocked {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .user-status.pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .user-stats {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            text-align: center;
        }
        
        .user-stat-value {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .user-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        
        .email-verification {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }
        
        .email-verified {
            color: var(--success);
        }
        
        .email-unverified {
            color: var(--warning);
        }
        
        .user-actions {
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
        
        .action-btn.toggle {
            background: rgba(139, 69, 19, 0.1);
            color: #8b4513;
        }
        
        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .action-btn.verify {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .stats-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .stat-badge.active {
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
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
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
                    <a href="/admin/categories.php" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Utilisateurs</div>
                    <a href="/admin/users.php" class="nav-link active">
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
                    <i class="fas fa-users"></i>
                    Gestion des Utilisateurs
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
            
            <!-- Statistiques -->
            <div class="stats-badges">
                <a href="/admin/users.php" class="stat-badge <?= empty($roleFilter) && empty($statusFilter) ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    Tous (<?= $totalUsers ?>)
                </a>
                <?php foreach ($roleStats as $stat): ?>
                    <a href="/admin/users.php?role=<?= $stat['role'] ?>" 
                       class="stat-badge <?= $roleFilter === $stat['role'] ? 'active' : '' ?>">
                        <i class="fas fa-<?= $stat['role'] === 'user' ? 'user' : ($stat['role'] === 'admin' ? 'user-tie' : 'crown') ?>"></i>
                        <?= ucfirst($stat['role']) ?> (<?= $stat['count'] ?>)
                    </a>
                <?php endforeach; ?>
                <?php foreach ($statusStats as $stat): ?>
                    <a href="/admin/users.php?status=<?= $stat['status'] ?>" 
                       class="stat-badge <?= $statusFilter === $stat['status'] ? 'active' : '' ?>">
                        <i class="fas fa-<?= $stat['status'] === 'active' ? 'check-circle' : 'times-circle' ?>"></i>
                        <?= ucfirst($stat['status']) ?> (<?= $stat['count'] ?>)
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" 
                                   placeholder="Nom, email, username...">
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Rôle</label>
                            <select id="role" name="role">
                                <option value="">Tous les rôles</option>
                                <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="superadmin" <?= $roleFilter === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Actif</option>
                                <option value="blocked" <?= $statusFilter === 'blocked' ? 'selected' : '' ?>>Bloqué</option>
                                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>En attente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Filtrer
                            </button>
                            <a href="/admin/users.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Table des utilisateurs -->
            <div class="users-table">
                <div class="table-header">
                    <h2 class="table-title">
                        <i class="fas fa-list"></i>
                        Utilisateurs (<?= $totalUsers ?>)
                    </h2>
                </div>
                
                <div class="table-container">
                    <?php if (empty($users)): ?>
                        <div class="empty-state" style="padding: 3rem; text-align: center;">
                            <i class="fas fa-users" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucun utilisateur</h3>
                            <p style="color: var(--text-secondary);">Aucun utilisateur ne correspond aux critères sélectionnés.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Email</th>
                                    <th>Commandes</th>
                                    <th>Total dépensé</th>
                                    <th>Inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $userData): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1)) ?>
                                                </div>
                                                <div class="user-details">
                                                    <div class="user-name"><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></div>
                                                    <div class="user-email">@<?= htmlspecialchars($userData['username']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="user-role <?= $userData['role'] ?>">
                                                <i class="fas fa-<?= $userData['role'] === 'user' ? 'user' : ($userData['role'] === 'admin' ? 'user-tie' : 'crown') ?>"></i>
                                                <?= ucfirst($userData['role']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="user-status <?= $userData['status'] ?>">
                                                <i class="fas fa-circle"></i>
                                                <?= ucfirst($userData['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="email-verification">
                                                <?= htmlspecialchars($userData['email']) ?>
                                                <?php if ($userData['email_verified']): ?>
                                                    <i class="fas fa-check-circle email-verified" title="Email vérifié"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-exclamation-circle email-unverified" title="Email non vérifié"></i>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-stats">
                                                <div class="user-stat-value"><?= $userData['order_count'] ?></div>
                                                <div class="user-stat-label">commandes</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-stats">
                                                <div class="user-stat-value"><?= formatPrice($userData['total_spent'] ?? 0) ?></div>
                                                <div class="user-stat-label">dépensé</div>
                                            </div>
                                        </td>
                                        <td><?= formatDate($userData['created_at']) ?></td>
                                        <td>
                                            <div class="user-actions">
                                                <button class="action-btn edit" onclick="editUser(<?= $userData['id'] ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if (!$userData['email_verified']): ?>
                                                    <button class="action-btn verify" onclick="verifyEmail(<?= $userData['id'] ?>)">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($userData['id'] != $user['id']): ?>
                                                    <button class="action-btn toggle" onclick="toggleUser(<?= $userData['id'] ?>)">
                                                        <i class="fas fa-<?= $userData['status'] === 'active' ? 'ban' : 'check' ?>"></i>
                                                    </button>
                                                    <?php if ($userData['order_count'] == 0): ?>
                                                        <button class="action-btn delete" onclick="deleteUser(<?= $userData['id'] ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
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
    
    <!-- Modal pour modifier un utilisateur -->
    <div class="modal" id="edit-user-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modifier l'utilisateur</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="edit-user-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="user_id" id="edit-user-id">
                
                <div class="form-group">
                    <label for="edit-role">Nouveau rôle</label>
                    <select id="edit-role" name="role" required>
                        <option value="user">Utilisateur</option>
                        <option value="admin">Admin</option>
                        <?php if ($user['role'] === 'superadmin'): ?>
                            <option value="superadmin">Super Admin</option>
                        <?php endif; ?>
                    </select>
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
        function editUser(userId) {
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-user-modal').classList.add('active');
        }
        
        function toggleUser(userId) {
            if (confirm('Êtes-vous sûr de vouloir changer le statut de cet utilisateur ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteUser(userId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function verifyEmail(userId) {
            if (confirm('Marquer cet email comme vérifié ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="verify_email">
                    <input type="hidden" name="user_id" value="${userId}">
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