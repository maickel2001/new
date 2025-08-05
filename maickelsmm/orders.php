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

// Paramètres de pagination et filtres
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$statusFilter = cleanInput($_GET['status'] ?? '');
$searchQuery = cleanInput($_GET['search'] ?? '');

// Construire la requête avec filtres
$whereConditions = ['(o.user_id = ? OR o.guest_email = ?)'];
$params = [$user['id'], $user['email']];

if (!empty($statusFilter)) {
    $whereConditions[] = 'o.status = ?';
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = '(s.name LIKE ? OR o.id LIKE ?)';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

$whereClause = implode(' AND ', $whereConditions);

$db = Database::getInstance();

// Compter le total des commandes
$totalOrders = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalOrders / $limit);

// Récupérer les commandes
$orders = $db->fetchAll("
    SELECT o.*, s.name as service_name, s.category_id, c.name as category_name,
           p.status as payment_status, p.payment_method, p.payment_proof
    FROM orders o
    LEFT JOIN services s ON o.service_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    LEFT JOIN payments p ON o.id = p.order_id
    WHERE $whereClause
    ORDER BY o.created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Messages flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes commandes - <?= htmlspecialchars($siteName) ?></title>
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
            <a href="/dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="/orders.php" class="nav-item active">
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
                <i class="fas fa-shopping-cart"></i>
                Mes commandes
            </h1>
            <p>Gérez et suivez toutes vos commandes SMM</p>
        </div>

        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashMessage['type'] ?>">
                <i class="fas fa-<?= $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($flashMessage['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Filtres et recherche -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="search">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        value="<?= htmlspecialchars($searchQuery) ?>"
                        placeholder="ID commande ou nom du service..."
                    >
                </div>

                <div class="filter-group">
                    <label for="status">
                        <i class="fas fa-filter"></i>
                        Statut
                    </label>
                    <select id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>En attente</option>
                        <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>En traitement</option>
                        <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                        <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Terminé</option>
                        <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="/orders.php" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Résultats -->
        <div class="orders-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-list"></i>
                    <?= $totalOrders ?> commande<?= $totalOrders > 1 ? 's' : '' ?> trouvée<?= $totalOrders > 1 ? 's' : '' ?>
                </h2>
                <div class="section-actions">
                    <a href="/#services" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nouvelle commande
                    </a>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Aucune commande trouvée</h3>
                    <?php if (!empty($searchQuery) || !empty($statusFilter)): ?>
                        <p>Aucune commande ne correspond à vos critères de recherche.</p>
                        <a href="/orders.php" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Effacer les filtres
                        </a>
                    <?php else: ?>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a href="/#services" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Passer ma première commande
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Vue mobile : cartes -->
                <div class="orders-cards mobile-only">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">#<?= $order['id'] ?></span>
                                <span class="status status-<?= strtolower($order['status']) ?>">
                                    <?= getStatusLabel($order['status']) ?>
                                </span>
                            </div>
                            
                            <div class="order-content">
                                <div class="service-info">
                                    <h4><?= htmlspecialchars($order['service_name']) ?></h4>
                                    <p><?= htmlspecialchars($order['category_name']) ?></p>
                                </div>
                                
                                <div class="order-details">
                                    <div class="detail-item">
                                        <span class="label">Quantité:</span>
                                        <span class="value"><?= number_format($order['quantity']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Montant:</span>
                                        <span class="value"><?= formatPrice($order['total_amount']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Date:</span>
                                        <span class="value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="/order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-eye"></i>
                                    Voir détails
                                </a>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <button class="btn btn-sm btn-primary" onclick="reorderService(<?= $order['service_id'] ?>)">
                                        <i class="fas fa-redo"></i>
                                        Recommander
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Vue desktop : tableau -->
                <div class="orders-table-container desktop-only">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Paiement</th>
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
                                        <div class="payment-info">
                                            <?php if ($order['payment_method']): ?>
                                                <span class="payment-method"><?= strtoupper($order['payment_method']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($order['payment_proof']): ?>
                                                <i class="fas fa-check-circle text-success" title="Preuve fournie"></i>
                                            <?php else: ?>
                                                <i class="fas fa-clock text-warning" title="En attente de preuve"></i>
                                            <?php endif; ?>
                                        </div>
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        $queryParams = array_filter([
                            'search' => $searchQuery,
                            'status' => $statusFilter
                        ]);
                        $baseUrl = '/orders.php?' . http_build_query($queryParams);
                        ?>
                        
                        <?php if ($page > 1): ?>
                            <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>" class="btn btn-outline">
                                <i class="fas fa-chevron-left"></i>
                                Précédent
                            </a>
                        <?php endif; ?>

                        <div class="page-numbers">
                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="<?= $baseUrl ?>&page=<?= $i ?>" 
                                   class="btn <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>" class="btn btn-outline">
                                Suivant
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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

        // Soumission automatique du formulaire de filtre avec délai
        let filterTimeout;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>