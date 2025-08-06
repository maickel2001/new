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

// Récupérer les statistiques globales
$db = Database::getInstance();

// Statistiques des commandes
$orderStats = $db->fetchOne("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
        SUM(total_amount) as total_revenue,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_amount ELSE 0 END) as today_revenue
    FROM orders
");

// Statistiques des utilisateurs
$userStats = $db->fetchOne("
    SELECT 
        COUNT(*) as total_users,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_registrations
    FROM users 
    WHERE role = 'user'
");

// Statistiques des services
$serviceStats = $db->fetchOne("
    SELECT 
        COUNT(*) as total_services,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_services
    FROM services
");

// Messages de contact non lus
$unreadMessages = $db->fetchOne("
    SELECT COUNT(*) as count 
    FROM contact_messages 
    WHERE status = 'unread'
")['count'];

// Commandes récentes
$recentOrders = $db->fetchAll("
    SELECT o.*, s.name as service_name, c.name as category_name,
           COALESCE(u.first_name, o.guest_name) as customer_name,
           COALESCE(u.email, o.guest_email) as customer_email
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Revenus par mois (12 derniers mois)
$monthlyRevenue = $db->fetchAll("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total_amount) as revenue,
        COUNT(*) as orders
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        AND status IN ('completed', 'in_progress')
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
");

// Messages flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - <?= htmlspecialchars($siteName) ?></title>
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
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-primary);
            font-size: 1.25rem;
            font-weight: bold;
        }
        
        .sidebar-brand i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-section-title {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .nav-badge {
            background: var(--error);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            margin-left: auto;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-header h1 {
            color: var(--text-primary);
            margin: 0;
            font-size: 2rem;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-role {
            color: var(--text-secondary);
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .stat-title {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-change {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .stat-change.positive {
            color: var(--success);
        }
        
        .stat-change.negative {
            color: var(--error);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        .dashboard-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-title {
            color: var(--text-primary);
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }
        
        .recent-orders-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-primary);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .order-item:hover {
            background: rgba(99, 102, 241, 0.05);
        }
        
        .order-avatar {
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
        
        .order-info {
            flex: 1;
        }
        
        .order-customer {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .order-service {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .order-meta {
            text-align: right;
        }
        
        .order-amount {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .order-time {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .quick-stat {
            text-align: center;
            padding: 1rem;
            background: var(--bg-primary);
            border-radius: 0.5rem;
        }
        
        .quick-stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-primary);
        }
        
        .quick-stat-label {
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.open {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-overview {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
                    <a href="/admin/" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/orders.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes</span>
                        <?php if ($orderStats['pending_orders'] > 0): ?>
                            <span class="nav-badge"><?= $orderStats['pending_orders'] ?></span>
                        <?php endif; ?>
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
                        <?php if ($unreadMessages > 0): ?>
                            <span class="nav-badge"><?= $unreadMessages ?></span>
                        <?php endif; ?>
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
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
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
            
            <!-- Stats Overview -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Commandes totales</h3>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($orderStats['total_orders']) ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <?= $orderStats['today_orders'] ?> aujourd'hui
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Revenus totaux</h3>
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= formatPrice($orderStats['total_revenue']) ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <?= formatPrice($orderStats['today_revenue']) ?> aujourd'hui
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Clients actifs</h3>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($userStats['active_users']) ?></div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i>
                        <?= $userStats['today_registrations'] ?> nouveaux
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Services actifs</h3>
                        <div class="stat-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?= number_format($serviceStats['active_services']) ?></div>
                    <div class="stat-change">
                        sur <?= number_format($serviceStats['total_services']) ?> total
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Orders -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-clock"></i>
                            Commandes récentes
                        </h2>
                        <a href="/admin/orders.php" class="btn btn-outline btn-sm">
                            Voir tout
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <?php if (empty($recentOrders)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <h3>Aucune commande</h3>
                            <p>Aucune commande récente à afficher.</p>
                        </div>
                    <?php else: ?>
                        <div class="recent-orders-list">
                            <?php foreach ($recentOrders as $order): ?>
                                <div class="order-item">
                                    <div class="order-avatar">
                                        <?= strtoupper(substr($order['customer_name'], 0, 2)) ?>
                                    </div>
                                    <div class="order-info">
                                        <div class="order-customer"><?= htmlspecialchars($order['customer_name']) ?></div>
                                        <div class="order-service"><?= htmlspecialchars($order['service_name']) ?></div>
                                    </div>
                                    <div class="order-meta">
                                        <div class="order-amount"><?= formatPrice($order['total_amount']) ?></div>
                                        <div class="order-time"><?= timeAgo($order['created_at']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Stats -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Statuts des commandes
                        </h2>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="quick-stat">
                            <div class="quick-stat-value"><?= $orderStats['pending_orders'] ?></div>
                            <div class="quick-stat-label">En attente</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value"><?= $orderStats['processing_orders'] ?></div>
                            <div class="quick-stat-label">En traitement</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value"><?= $orderStats['in_progress_orders'] ?></div>
                            <div class="quick-stat-label">En cours</div>
                        </div>
                        <div class="quick-stat">
                            <div class="quick-stat-value"><?= $orderStats['completed_orders'] ?></div>
                            <div class="quick-stat-label">Terminées</div>
                        </div>
                    </div>
                    
                    <?php if ($unreadMessages > 0): ?>
                        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <a href="/admin/messages.php" class="btn btn-primary btn-full">
                                <i class="fas fa-envelope"></i>
                                <?= $unreadMessages ?> message<?= $unreadMessages > 1 ? 's' : '' ?> non lu<?= $unreadMessages > 1 ? 's' : '' ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('open');
        }
        
        // Auto-refresh dashboard every 5 minutes
        setTimeout(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>