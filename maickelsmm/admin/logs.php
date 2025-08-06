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
        redirect('/admin/logs.php');
    }
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'clear_logs') {
        $daysToKeep = intval($_POST['days_to_keep'] ?? 30);
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$daysToKeep days"));
        
        $deleted = $db->execute("DELETE FROM admin_logs WHERE created_at < ?", [$cutoffDate]);
        if ($deleted) {
            logAdminAction($user['id'], 'logs_cleanup', "Logs supprimés: plus anciens que $daysToKeep jours");
            setFlashMessage('success', "Logs anciens supprimés avec succès.");
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression des logs.');
        }
        redirect('/admin/logs.php');
    }
    
    if ($action === 'export_logs') {
        $dateFrom = cleanInput($_POST['export_date_from'] ?? '');
        $dateTo = cleanInput($_POST['export_date_to'] ?? '');
        
        $whereConditions = ['1=1'];
        $params = [];
        
        if (!empty($dateFrom)) {
            $whereConditions[] = 'DATE(al.created_at) >= ?';
            $params[] = $dateFrom;
        }
        
        if (!empty($dateTo)) {
            $whereConditions[] = 'DATE(al.created_at) <= ?';
            $params[] = $dateTo;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $logs = $db->fetchAll("
            SELECT al.*, u.first_name, u.last_name, u.email
            FROM admin_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE $whereClause
            ORDER BY al.created_at DESC
        ", $params);
        
        // Générer le CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="admin_logs_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes CSV
        fputcsv($output, [
            'Date/Heure',
            'Utilisateur',
            'Email',
            'Action',
            'Description',
            'Adresse IP',
            'User Agent'
        ]);
        
        // Données
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['created_at'],
                $log['first_name'] . ' ' . $log['last_name'],
                $log['email'],
                $log['action'],
                $log['description'],
                $log['ip_address'],
                $log['user_agent']
            ]);
        }
        
        fclose($output);
        exit;
    }
}

// Filtres et pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

$actionFilter = cleanInput($_GET['action'] ?? '');
$userFilter = intval($_GET['user'] ?? 0);
$dateFrom = cleanInput($_GET['date_from'] ?? '');
$dateTo = cleanInput($_GET['date_to'] ?? '');

// Construction de la requête
$whereConditions = ['1=1'];
$params = [];

if (!empty($actionFilter)) {
    $whereConditions[] = 'al.action LIKE ?';
    $params[] = "%$actionFilter%";
}

if ($userFilter > 0) {
    $whereConditions[] = 'al.user_id = ?';
    $params[] = $userFilter;
}

if (!empty($dateFrom)) {
    $whereConditions[] = 'DATE(al.created_at) >= ?';
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $whereConditions[] = 'DATE(al.created_at) <= ?';
    $params[] = $dateTo;
}

$whereClause = implode(' AND ', $whereConditions);

// Compter le total
$totalLogs = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM admin_logs al
    LEFT JOIN users u ON al.user_id = u.id
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalLogs / $limit);

// Récupérer les logs
$logs = $db->fetchAll("
    SELECT al.*, u.first_name, u.last_name, u.email, u.role
    FROM admin_logs al
    LEFT JOIN users u ON al.user_id = u.id
    WHERE $whereClause
    ORDER BY al.created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Statistiques
$stats = [
    'total' => $db->fetchOne("SELECT COUNT(*) as count FROM admin_logs")['count'],
    'today' => $db->fetchOne("SELECT COUNT(*) as count FROM admin_logs WHERE DATE(created_at) = CURDATE()")['count'],
    'week' => $db->fetchOne("SELECT COUNT(*) as count FROM admin_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['count'],
    'month' => $db->fetchOne("SELECT COUNT(*) as count FROM admin_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")['count']
];

// Actions les plus fréquentes
$topActions = $db->fetchAll("
    SELECT action, COUNT(*) as count
    FROM admin_logs
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY action
    ORDER BY count DESC
    LIMIT 10
");

// Utilisateurs les plus actifs
$topUsers = $db->fetchAll("
    SELECT u.first_name, u.last_name, u.email, COUNT(*) as count
    FROM admin_logs al
    LEFT JOIN users u ON al.user_id = u.id
    WHERE al.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY al.user_id
    ORDER BY count DESC
    LIMIT 10
");

// Liste des utilisateurs admin pour le filtre
$adminUsers = $db->fetchAll("
    SELECT id, first_name, last_name, email
    FROM users
    WHERE role IN ('admin', 'superadmin')
    ORDER BY first_name, last_name
");

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs d'Administration - <?= htmlspecialchars($siteName) ?></title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-value {
            color: var(--text-primary);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .logs-table {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .logs-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .logs-table th,
        .logs-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logs-table th {
            background: var(--bg-primary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .logs-table td {
            color: var(--text-primary);
        }
        
        .log-datetime {
            color: var(--text-secondary);
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        .log-user {
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
        
        .log-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }
        
        .log-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            max-width: 300px;
            word-wrap: break-word;
        }
        
        .log-ip {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-family: monospace;
        }
        
        .insights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .insight-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .insight-title {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .insight-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .insight-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: var(--bg-primary);
            border-radius: 0.5rem;
        }
        
        .insight-name {
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        
        .insight-count {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .tools-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .tools-title {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tools-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .tool-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.5rem;
        }
        
        .tool-card h4 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .tool-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
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
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .insights-grid,
            .tools-grid {
                grid-template-columns: 1fr;
            }
            
            .logs-table th,
            .logs-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
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
                    <a href="/admin/logs.php" class="nav-link active">
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
                    <i class="fas fa-list-alt"></i>
                    Logs d'Administration
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
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($stats['total']) ?></div>
                    <div class="stat-label">Total des logs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($stats['today']) ?></div>
                    <div class="stat-label">Aujourd'hui</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($stats['week']) ?></div>
                    <div class="stat-label">Cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($stats['month']) ?></div>
                    <div class="stat-label">Ce mois</div>
                </div>
            </div>
            
            <!-- Insights -->
            <div class="insights-grid">
                <div class="insight-card">
                    <h3 class="insight-title">
                        <i class="fas fa-chart-bar"></i>
                        Actions les plus fréquentes (30j)
                    </h3>
                    <div class="insight-list">
                        <?php foreach ($topActions as $action): ?>
                            <div class="insight-item">
                                <span class="insight-name"><?= htmlspecialchars($action['action']) ?></span>
                                <span class="insight-count"><?= $action['count'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="insight-card">
                    <h3 class="insight-title">
                        <i class="fas fa-users"></i>
                        Utilisateurs les plus actifs (30j)
                    </h3>
                    <div class="insight-list">
                        <?php foreach ($topUsers as $userData): ?>
                            <div class="insight-item">
                                <span class="insight-name"><?= htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']) ?></span>
                                <span class="insight-count"><?= $userData['count'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Outils -->
            <div class="tools-section">
                <h2 class="tools-title">
                    <i class="fas fa-tools"></i>
                    Outils de gestion
                </h2>
                
                <div class="tools-grid">
                    <div class="tool-card">
                        <h4>Exporter les logs</h4>
                        <p>Exportez les logs dans une période donnée au format CSV.</p>
                        <button class="btn btn-primary btn-sm" onclick="openExportModal()">
                            <i class="fas fa-download"></i>
                            Exporter
                        </button>
                    </div>
                    
                    <div class="tool-card">
                        <h4>Nettoyer les logs</h4>
                        <p>Supprimez les anciens logs pour libérer de l'espace.</p>
                        <button class="btn btn-warning btn-sm" onclick="openCleanupModal()">
                            <i class="fas fa-trash-alt"></i>
                            Nettoyer
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="action">Action</label>
                            <input type="text" id="action" name="action" value="<?= htmlspecialchars($actionFilter) ?>" 
                                   placeholder="Filtrer par action...">
                        </div>
                        
                        <div class="form-group">
                            <label for="user">Utilisateur</label>
                            <select id="user" name="user">
                                <option value="">Tous les utilisateurs</option>
                                <?php foreach ($adminUsers as $adminUser): ?>
                                    <option value="<?= $adminUser['id'] ?>" <?= $userFilter == $adminUser['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($adminUser['first_name'] . ' ' . $adminUser['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
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
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Filtrer
                            </button>
                            <a href="/admin/logs.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Table des logs -->
            <div class="logs-table">
                <div class="table-header">
                    <h2 class="table-title">
                        <i class="fas fa-list"></i>
                        Logs d'activité (<?= $totalLogs ?>)
                    </h2>
                </div>
                
                <div class="table-container">
                    <?php if (empty($logs)): ?>
                        <div class="empty-state" style="padding: 3rem; text-align: center;">
                            <i class="fas fa-list-alt" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucun log</h3>
                            <p style="color: var(--text-secondary);">Aucun log ne correspond aux critères sélectionnés.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Date/Heure</th>
                                    <th>Utilisateur</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td>
                                            <div class="log-datetime"><?= formatDate($log['created_at']) ?></div>
                                        </td>
                                        <td>
                                            <div class="log-user">
                                                <div class="user-name"><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></div>
                                                <div class="user-email"><?= htmlspecialchars($log['email']) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="log-action">
                                                <i class="fas fa-cog"></i>
                                                <?= htmlspecialchars($log['action']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="log-description"><?= htmlspecialchars($log['description']) ?></div>
                                        </td>
                                        <td>
                                            <div class="log-ip"><?= htmlspecialchars($log['ip_address']) ?></div>
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
    
    <!-- Modal Export -->
    <div class="modal" id="export-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Exporter les logs</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="export-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="export_logs">
                
                <div class="form-group">
                    <label for="export-date-from">Date début (optionnel)</label>
                    <input type="date" id="export-date-from" name="export_date_from">
                </div>
                
                <div class="form-group">
                    <label for="export-date-to">Date fin (optionnel)</label>
                    <input type="date" id="export-date-to" name="export_date_to">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i>
                        Télécharger CSV
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Cleanup -->
    <div class="modal" id="cleanup-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nettoyer les logs</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="cleanup-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="clear_logs">
                
                <div class="form-group">
                    <label for="days-to-keep">Conserver les logs des derniers (jours)</label>
                    <input type="number" id="days-to-keep" name="days_to_keep" value="30" min="1" max="365" required>
                    <small style="color: var(--text-secondary);">Les logs plus anciens seront supprimés définitivement.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-trash-alt"></i>
                        Supprimer les anciens logs
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openExportModal() {
            document.getElementById('export-modal').classList.add('active');
        }
        
        function openCleanupModal() {
            document.getElementById('cleanup-modal').classList.add('active');
        }
        
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }
        
        // Confirmation pour le nettoyage
        document.getElementById('cleanup-form').addEventListener('submit', function(e) {
            const days = document.getElementById('days-to-keep').value;
            if (!confirm(`Êtes-vous sûr de vouloir supprimer tous les logs de plus de ${days} jours ? Cette action est irréversible.`)) {
                e.preventDefault();
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