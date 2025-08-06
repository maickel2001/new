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

// Récupérer l'ID de la commande
$orderId = intval($_GET['id'] ?? 0);
if (!$orderId) {
    header('Location: index.php');
    exit;
}

// Récupérer la commande
try {
    $order = $pdo->query("
        SELECT o.*, s.name as service_name, s.description as service_description,
               u.first_name, u.last_name, u.email as user_email
        FROM orders o 
        LEFT JOIN services s ON o.service_id = s.id 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = $orderId
    ")->fetch();
} catch (Exception $e) {
    $order = null;
}

if (!$order) {
    header('Location: index.php');
    exit;
}

// Vérifier les permissions d'accès
$canAccess = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $order['user_id']) {
    $canAccess = true; // Propriétaire de la commande
} elseif (!$order['user_id'] && $order['guest_email']) {
    // Commande d'invité - vérifier l'email dans l'URL
    $emailCheck = $_GET['email'] ?? '';
    if ($emailCheck === $order['guest_email']) {
        $canAccess = true;
    }
}

if (!$canAccess) {
    die('Accès non autorisé à cette commande.');
}

// Traitement de l'upload de preuve de paiement
if ($_POST && isset($_FILES['payment_proof'])) {
    $uploadDir = 'assets/uploads/payments/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $file = $_FILES['payment_proof'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    
    if (in_array($file['type'], $allowedTypes) && $file['size'] <= 5 * 1024 * 1024) { // 5MB max
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'payment_' . $orderId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Mettre à jour la commande
            $pdo->query("UPDATE orders SET payment_proof = '$filename', status = 'processing' WHERE id = $orderId");
            $success = 'Preuve de paiement uploadée avec succès !';
            
            // Recharger la commande
            $order = $pdo->query("
                SELECT o.*, s.name as service_name, s.description as service_description,
                       u.first_name, u.last_name, u.email as user_email
                FROM orders o 
                LEFT JOIN services s ON o.service_id = s.id 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE o.id = $orderId
            ")->fetch();
        } else {
            $error = 'Erreur lors de l\'upload du fichier.';
        }
    } else {
        $error = 'Type de fichier non autorisé ou fichier trop volumineux (max 5MB).';
    }
}

// Récupérer les méthodes de paiement
try {
    $paymentMethods = [];
    $settings = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'payment_%'")->fetchAll();
    foreach ($settings as $setting) {
        $paymentMethods[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    $paymentMethods = [
        'payment_mtn' => '+225 XX XX XX XX',
        'payment_moov' => '+225 XX XX XX XX',
        'payment_orange' => '+225 XX XX XX XX'
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande #<?= $orderId ?> - MaickelSMM</title>
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
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .header h1 {
            font-size: 2.5rem;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 1rem;
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
        
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .detail-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 1rem;
            border-radius: 8px;
        }
        
        .detail-label {
            color: #b0b3c1;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .detail-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .payment-section {
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .payment-method {
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .payment-method:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .payment-number {
            font-weight: bold;
            font-size: 1.1rem;
            color: #00d4ff;
            margin-top: 0.5rem;
        }
        
        .upload-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #00d4ff, #ff6b6b);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #34d399;
            color: #34d399;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #f87171;
            color: #f87171;
        }
        
        .proof-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .proof-image:hover {
            transform: scale(1.05);
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .payment-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Commande #<?= $orderId ?></h1>
            <div class="order-status status-<?= $order['status'] ?>">
                <?php
                $statusLabels = [
                    'pending' => 'En attente de paiement',
                    'processing' => 'En cours de traitement',
                    'in_progress' => 'En cours d\'exécution',
                    'completed' => 'Terminée',
                    'cancelled' => 'Annulée'
                ];
                echo $statusLabels[$order['status']] ?? ucfirst($order['status']);
                ?>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Détails de la commande -->
        <div class="card">
            <h2 style="color: #00d4ff; margin-bottom: 1.5rem;">Détails de la commande</h2>
            
            <div class="order-details">
                <div class="detail-item">
                    <div class="detail-label">Service</div>
                    <div class="detail-value"><?= htmlspecialchars($order['service_name'] ?? 'Service #' . $order['service_id']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Quantité</div>
                    <div class="detail-value"><?= number_format($order['quantity']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Montant total</div>
                    <div class="detail-value"><?= number_format($order['total_amount']) ?> FCFA</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Date de commande</div>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Lien/URL</div>
                    <div class="detail-value" style="word-break: break-all; font-size: 0.9rem;">
                        <?= htmlspecialchars($order['target_url']) ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Méthode de paiement</div>
                    <div class="detail-value"><?= htmlspecialchars($order['payment_method']) ?></div>
                </div>
            </div>
        </div>

        <!-- Section paiement -->
        <?php if ($order['status'] === 'pending' || ($order['status'] === 'processing' && !$order['payment_proof'])): ?>
        <div class="card">
            <div class="payment-section">
                <h3 style="color: #00d4ff; margin-bottom: 1rem;">💳 Effectuer le paiement</h3>
                <p style="margin-bottom: 1rem;">
                    Envoyez exactement <strong><?= number_format($order['total_amount']) ?> FCFA</strong> 
                    via Mobile Money vers l'un des numéros ci-dessous :
                </p>
                
                <div class="payment-methods">
                    <?php if (!empty($paymentMethods['payment_mtn'])): ?>
                    <div class="payment-method" onclick="copyToClipboard('<?= $paymentMethods['payment_mtn'] ?>')">
                        <div>📱 MTN Money</div>
                        <div class="payment-number"><?= htmlspecialchars($paymentMethods['payment_mtn']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($paymentMethods['payment_moov'])): ?>
                    <div class="payment-method" onclick="copyToClipboard('<?= $paymentMethods['payment_moov'] ?>')">
                        <div>📱 Moov Money</div>
                        <div class="payment-number"><?= htmlspecialchars($paymentMethods['payment_moov']) ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($paymentMethods['payment_orange'])): ?>
                    <div class="payment-method" onclick="copyToClipboard('<?= $paymentMethods['payment_orange'] ?>')">
                        <div>📱 Orange Money</div>
                        <div class="payment-number"><?= htmlspecialchars($paymentMethods['payment_orange']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <p style="font-size: 0.9rem; color: #b0b3c1; margin-top: 1rem;">
                    💡 Cliquez sur un numéro pour le copier automatiquement
                </p>
            </div>
            
            <!-- Upload preuve de paiement -->
            <div class="upload-form">
                <h3 style="color: #00d4ff; margin-bottom: 1rem;">📎 Envoyer la preuve de paiement</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="payment_proof">Sélectionnez votre capture d'écran ou reçu (JPG, PNG, PDF - Max 5MB)</label>
                        <input type="file" id="payment_proof" name="payment_proof" 
                               accept="image/jpeg,image/jpg,image/png,application/pdf" required>
                    </div>
                    
                    <button type="submit" class="btn">Envoyer la preuve</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Preuve de paiement uploadée -->
        <div class="card">
            <h3 style="color: #00d4ff; margin-bottom: 1rem;">✅ Preuve de paiement</h3>
            
            <?php if ($order['payment_proof']): ?>
                <div style="text-align: center;">
                    <?php
                    $proofPath = 'assets/uploads/payments/' . $order['payment_proof'];
                    $fileExt = strtolower(pathinfo($order['payment_proof'], PATHINFO_EXTENSION));
                    ?>
                    
                    <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png'])): ?>
                        <img src="<?= $proofPath ?>" alt="Preuve de paiement" class="proof-image" 
                             onclick="window.open(this.src, '_blank')" style="max-width: 400px;">
                        <p style="margin-top: 1rem; color: #b0b3c1; font-size: 0.9rem;">
                            Cliquez sur l'image pour l'agrandir
                        </p>
                    <?php else: ?>
                        <p>📄 Fichier PDF uploadé : 
                           <a href="<?= $proofPath ?>" target="_blank" style="color: #00d4ff;">
                               Voir le document
                           </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="color: #b0b3c1;">Aucune preuve de paiement uploadée.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="back-link">
            <a href="index.php" class="btn btn-secondary">← Retour à l'accueil</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard_simple.php" class="btn btn-secondary">Tableau de bord</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Numéro copié : ' + text);
            });
        }
        
        // Auto-refresh de la page toutes les 30 secondes si en attente
        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
        setTimeout(function() {
            location.reload();
        }, 30000);
        <?php endif; ?>
    </script>
</body>
</html>