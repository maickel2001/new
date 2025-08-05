<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// Vérifier l'authentification
$auth->requireAuth();

$user = $auth->getCurrentUser();
$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';

// Récupérer les statistiques du client
$stats = getDashboardStats($user['id']);

// Récupérer les commandes récentes
$db = Database::getInstance();
$recentOrders = $db->fetchAll("
    SELECT o.*, s.name as service_name, s.category_id, c.name as category_name,
           p.status as payment_status, p.payment_method
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE o.user_id = ? OR o.guest_email = ?
    ORDER BY o.created_at DESC
    LIMIT 5
", [$user['id'], $user['email']]);

// Messages flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body class="dashboard-page">
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="nav-brand">
            <i class="fas fa-rocket"></i>
            <span><?= htmlspecialchars($siteName) ?></span>
        </div>
        
        <div class="nav-menu">
            <a href="/dashboard.php" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="/orders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Mes commandes</span>
            </a>
            <a href="/profile.php" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Mon profil</span>
            </a>
            <a href="/" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
        </div>

        <div class="nav-user">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                    <span class="user-role"><?= ucfirst($user['role']) ?></span>
                </div>
            </div>
            <div class="user-actions">
                <a href="/logout.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="dashboard-main">
        <div class="dashboard-header">
            <h1>
                <i class="fas fa-tachometer-alt"></i>
                Bienvenue, <?= htmlspecialchars($user['first_name']) ?> !
            </h1>
            <p>Gérez vos commandes SMM et suivez vos statistiques</p>
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
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                    <p>Commandes totales</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['pending_orders'] ?? 0) ?></h3>
                    <p>En attente</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($stats['completed_orders'] ?? 0) ?></h3>
                    <p>Terminées</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3><?= formatPrice($stats['total_spent'] ?? 0) ?></h3>
                    <p>Total dépensé</p>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <h2>
                <i class="fas fa-bolt"></i>
                Actions rapides
            </h2>
            <div class="actions-grid">
                <a href="/#services" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3>Nouvelle commande</h3>
                    <p>Parcourir nos services SMM</p>
                </a>

                <a href="/orders.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3>Mes commandes</h3>
                    <p>Voir toutes mes commandes</p>
                </a>

                <a href="/profile.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Mon profil</h3>
                    <p>Modifier mes informations</p>
                </a>

                <a href="/contact.php" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Support</h3>
                    <p>Contacter notre équipe</p>
                </a>
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="recent-orders">
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Commandes récentes
                </h2>
                <a href="/orders.php" class="btn btn-outline btn-sm">
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($recentOrders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Aucune commande</h3>
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="/#services" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Passer ma première commande
                    </a>
                </div>
            <?php else: ?>
                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>
                                        <span class="order-id">#<?= $order['id'] ?></span>
                                    </td>
                                    <td>
                                        <div class="service-info">
                                            <strong><?= htmlspecialchars($order['service_name']) ?></strong>
                                            <small><?= htmlspecialchars($order['category_name']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="quantity"><?= number_format($order['quantity']) ?></span>
                                    </td>
                                    <td>
                                        <span class="amount"><?= formatPrice($order['total_amount']) ?></span>
                                    </td>
                                    <td>
                                        <span class="status status-<?= strtolower($order['status']) ?>">
                                            <?= getStatusLabel($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="date"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                            <a href="/order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i>
                                                Voir
                                            </a>
                                            <?php if ($order['status'] === 'completed'): ?>
                                                <button class="btn btn-sm btn-primary" onclick="reorderService(<?= $order['service_id'] ?>)">
                                                    <i class="fas fa-redo"></i>
                                                    Recommander
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Fonction pour recommander un service
        function reorderService(serviceId) {
            window.location.href = `/#services?service=${serviceId}&reorder=1`;
        }

        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Navigation active
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navItems = document.querySelectorAll('.nav-item');
            
            navItems.forEach(item => {
                if (item.getAttribute('href') === currentPath) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>