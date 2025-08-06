<?php
// Dashboard ultra-minimal - AUCUNE dépendance
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration DB - MODIFIEZ LE MOT DE PASSE
$DB_HOST = 'localhost';
$DB_NAME = 'u634930929_Ino';
$DB_USER = 'u634930929_Ino';
$DB_PASS = 'VotreMotDePasse'; // ⚠️ REMPLACEZ par votre mot de passe DB

try {
    $conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

// Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: login_minimal.php');
    exit;
}

// Récupérer utilisateur
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        session_destroy();
        header('Location: login_minimal.php');
        exit;
    }
} catch (Exception $e) {
    die("Erreur utilisateur: " . $e->getMessage());
}

// Statistiques utilisateur
$stats = ['total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0, 'total_spent' => 0];
try {
    $userId = $_SESSION['user_id'];
    $stats['total_orders'] = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $userId")->fetchColumn();
    $stats['pending_orders'] = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $userId AND status = 'pending'")->fetchColumn();
    $stats['completed_orders'] = $conn->query("SELECT COUNT(*) FROM orders WHERE user_id = $userId AND status = 'completed'")->fetchColumn();
    $stats['total_spent'] = $conn->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = $userId AND status != 'cancelled'")->fetchColumn();
} catch (Exception $e) {
    // Ignorer les erreurs
}

// Commandes récentes
$recentOrders = [];
try {
    $stmt = $conn->prepare("SELECT o.*, s.name as service_name FROM orders o LEFT JOIN services s ON o.service_id = s.id WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Ignorer les erreurs
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MaickelSMM</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        h1 {
            color: #00d4ff;
            margin-bottom: 10px;
        }
        .welcome {
            color: #b0b3c1;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #00d4ff;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #b0b3c1;
        }
        .section {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
        }
        .section-title {
            color: #00d4ff;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .order-item {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-info h4 {
            color: white;
            margin-bottom: 5px;
        }
        .order-info p {
            color: #b0b3c1;
            font-size: 0.9rem;
        }
        .order-status {
            padding: 5px 15px;
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
        .no-orders {
            text-align: center;
            color: #b0b3c1;
            padding: 40px;
        }
        .actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        .btn {
            padding: 15px 25px;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .nav-links {
            text-align: center;
            margin: 20px 0;
        }
        .nav-links a {
            color: #00d4ff;
            text-decoration: none;
            margin: 0 15px;
        }
        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏠 Tableau de Bord</h1>
            <div class="welcome">
                Bienvenue <?= htmlspecialchars($user['first_name']) ?> ! 
                Gérez vos commandes SMM depuis votre espace personnel.
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-links">
            <a href="index.php">🏠 Accueil</a>
            <a href="login_minimal.php?logout=1">🚪 Déconnexion</a>
            <?php if (in_array($user['role'] ?? '', ['admin', 'superadmin'])): ?>
                <a href="admin_minimal.php">⚙️ Admin</a>
            <?php endif; ?>
        </div>

        <!-- Statistiques -->
        <div class="stats">
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
        </div>

        <!-- Commandes récentes -->
        <div class="section">
            <h2 class="section-title">📋 Commandes Récentes</h2>
            
            <?php if (empty($recentOrders)): ?>
                <div class="no-orders">
                    <p>Aucune commande pour le moment.</p>
                    <p><a href="index.php#services" style="color: #00d4ff;">Passez votre première commande !</a></p>
                </div>
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
        </div>

        <!-- Actions rapides -->
        <div class="actions">
            <a href="index.php#services" class="btn">🛒 Nouvelle Commande</a>
            <a href="contact.php" class="btn btn-secondary">💬 Support Client</a>
        </div>
    </div>

    <?php
    // Déconnexion
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: login_minimal.php');
        exit;
    }
    ?>
</body>
</html>