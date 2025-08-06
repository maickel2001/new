<?php
session_start();

// Configuration DB
$host = 'localhost';
$dbname = 'u634930929_Ino';
$username = 'u634930929_Ino';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur DB: " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login_ultra_simple.php');
    exit;
}

// Récupérer les infos utilisateur
$user = $pdo->query("SELECT * FROM users WHERE id = " . intval($_SESSION['user_id']))->fetch();
if (!$user) {
    session_destroy();
    header('Location: login_ultra_simple.php');
    exit;
}

// Statistiques utilisateur
try {
    $stats = [];
    $stats['total_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id = " . intval($_SESSION['user_id']))->fetchColumn();
    $stats['pending_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id = " . intval($_SESSION['user_id']) . " AND status = 'pending'")->fetchColumn();
    $stats['completed_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id = " . intval($_SESSION['user_id']) . " AND status = 'completed'")->fetchColumn();
    $stats['total_spent'] = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = " . intval($_SESSION['user_id']) . " AND status != 'cancelled'")->fetchColumn();
} catch (Exception $e) {
    $stats = ['total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0, 'total_spent' => 0];
}

// Commandes récentes
try {
    $recentOrders = $pdo->query("
        SELECT o.*, s.name as service_name 
        FROM orders o 
        LEFT JOIN services s ON o.service_id = s.id 
        WHERE o.user_id = " . intval($_SESSION['user_id']) . " 
        ORDER BY o.created_at DESC 
        LIMIT 5
    ")->fetchAll();
} catch (Exception $e) {
    $recentOrders = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - MaickelSMM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
        }
        
        .header {
            background: rgba(26, 26, 46, 0.95);
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .user-menu {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .user-menu a {
            color: #b0b3c1;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .user-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #00d4ff;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .welcome {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .welcome h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .welcome p {
            color: #b0b3c1;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #00d4ff;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #b0b3c1;
            font-size: 1rem;
        }
        
        .section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #00d4ff;
        }
        
        .order-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-info h4 {
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .order-info p {
            color: #b0b3c1;
            font-size: 0.9rem;
        }
        
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
        
        .status-processing {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }
        
        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        
        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">MaickelSMM</div>
            <nav class="user-menu">
                <span>Bonjour, <?= htmlspecialchars($user['first_name']) ?></span>
                <a href="orders.php">Mes Commandes</a>
                <a href="profile.php">Profil</a>
                <a href="index.php">Accueil</a>
                <a href="logout.php">Déconnexion</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <section class="welcome">
            <h1>Tableau de Bord</h1>
            <p>Bienvenue <?= htmlspecialchars($user['first_name']) ?> ! Gérez vos commandes SMM depuis votre espace personnel.</p>
        </section>

        <!-- Statistics -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($stats['total_orders']) ?></div>
                <div class="stat-label">Commandes totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($stats['pending_orders']) ?></div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($stats['completed_orders']) ?></div>
                <div class="stat-label">Terminées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= number_format($stats['total_spent']) ?> FCFA</div>
                <div class="stat-label">Total dépensé</div>
            </div>
        </section>

        <!-- Recent Orders -->
        <section class="section">
            <h2 class="section-title">Commandes Récentes</h2>
            
            <?php if (empty($recentOrders)): ?>
                <p style="color: #b0b3c1; text-align: center; padding: 2rem;">
                    Aucune commande pour le moment. 
                    <a href="index.php#services" style="color: #00d4ff;">Commandez maintenant !</a>
                </p>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                <div class="order-item">
                    <div class="order-info">
                        <h4><?= htmlspecialchars($order['service_name'] ?? 'Service #' . $order['service_id']) ?></h4>
                        <p>
                            Quantité: <?= number_format($order['quantity']) ?> | 
                            Montant: <?= number_format($order['total_amount']) ?> FCFA | 
                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                    <div class="order-status status-<?= $order['status'] ?>">
                        <?php
                        $statusLabels = [
                            'pending' => 'En attente',
                            'processing' => 'En cours',
                            'in_progress' => 'En traitement',
                            'completed' => 'Terminé',
                            'cancelled' => 'Annulé'
                        ];
                        echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Quick Actions -->
        <section class="actions">
            <a href="index.php#services" class="btn">Nouvelle Commande</a>
            <a href="orders.php" class="btn btn-secondary">Toutes mes Commandes</a>
            <a href="profile.php" class="btn btn-secondary">Modifier mon Profil</a>
            <a href="contact.php" class="btn btn-secondary">Support Client</a>
        </section>
    </main>
</body>
</html>