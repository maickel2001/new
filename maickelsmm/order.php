<?php
/**
 * Page de commande - MaickelSMM
 * Affichage des détails de commande et upload de preuve de paiement
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Récupérer l'ID de la commande depuis l'URL
$orderId = $_GET['id'] ?? null;
if (!$orderId || !is_numeric($orderId)) {
    setFlashMessage(MSG_ERROR, 'Commande introuvable');
    redirect('/');
}

// Récupérer la commande
$order = getOrderById($orderId);
if (!$order) {
    setFlashMessage(MSG_ERROR, 'Commande introuvable');
    redirect('/');
}

// Vérifier les permissions (propriétaire ou admin)
$canViewOrder = false;
if (isLoggedIn()) {
    $user = getCurrentUser();
    $canViewOrder = ($user['id'] == $order['user_id']) || hasPermission(ROLE_ADMIN);
} else {
    // Pour les commandes invités, permettre l'accès avec l'email
    $canViewOrder = !empty($order['guest_email']);
}

if (!$canViewOrder) {
    setFlashMessage(MSG_ERROR, 'Accès non autorisé');
    redirect('/');
}

// Traitement de l'upload de preuve de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_proof'])) {
    try {
        if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Veuillez sélectionner un fichier valide');
        }
        
        $filename = uploadPaymentProof($_FILES['payment_proof'], $orderId);
        
        // Mettre à jour le statut de la commande si elle était en attente
        if ($order['status'] === 'pending') {
            updateOrderStatus($orderId, 'processing', 'Preuve de paiement reçue');
        }
        
        setFlashMessage(MSG_SUCCESS, 'Preuve de paiement uploadée avec succès. Votre commande va être traitée.');
        redirect("/order.php?id=$orderId");
        
    } catch (Exception $e) {
        setFlashMessage(MSG_ERROR, $e->getMessage());
    }
}

$pageTitle = "Commande #$orderId - " . getSetting('site_name', 'MaickelSMM');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Messages Flash -->
    <div class="flash-messages">
        <?php foreach (getFlashMessages() as $message): ?>
            <div class="flash-message flash-<?= $message['type'] ?>">
                <i class="fas fa-<?= $message['type'] === 'success' ? 'check-circle' : ($message['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <span><?= htmlspecialchars($message['message']) ?></span>
                <button class="flash-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo">
                    <i class="fas fa-rocket"></i>
                    <?= getSetting('site_name', 'MaickelSMM') ?>
                </a>
                
                <ul class="nav-menu">
                    <li><a href="/" class="nav-link">
                        <i class="fas fa-home"></i> Accueil
                    </a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/dashboard/" class="nav-link">
                            <i class="fas fa-user"></i> Dashboard
                        </a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="order-page">
                <!-- En-tête de la commande -->
                <div class="order-header">
                    <div class="order-title">
                        <h1>Commande #<?= $orderId ?></h1>
                        <div class="order-status">
                            <?php
                            $statusClass = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'error',
                                'refunded' => 'error'
                            ][$order['status']] ?? 'info';
                            
                            $statusText = [
                                'pending' => 'En attente',
                                'processing' => 'En cours',
                                'completed' => 'Terminé',
                                'cancelled' => 'Annulé',
                                'refunded' => 'Remboursé'
                            ][$order['status']] ?? 'Inconnu';
                            ?>
                            <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                        </div>
                    </div>
                    <div class="order-date">
                        <i class="fas fa-calendar"></i>
                        Créée le <?= formatDate($order['created_at']) ?>
                    </div>
                </div>

                <div class="grid grid-2">
                    <!-- Détails de la commande -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                Détails de la commande
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="order-details">
                                <div class="detail-row">
                                    <span class="detail-label">Service :</span>
                                    <span class="detail-value">
                                        <i class="<?= $order['category_icon'] ?>"></i>
                                        <?= htmlspecialchars($order['service_name']) ?>
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Catégorie :</span>
                                    <span class="detail-value"><?= htmlspecialchars($order['category_name']) ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Quantité :</span>
                                    <span class="detail-value"><?= number_format($order['quantity']) ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Lien :</span>
                                    <span class="detail-value">
                                        <a href="<?= htmlspecialchars($order['link']) ?>" target="_blank" class="link-external">
                                            <?= htmlspecialchars(truncateText($order['link'], 50)) ?>
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Montant total :</span>
                                    <span class="detail-value total-amount"><?= formatPrice($order['total_amount']) ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="detail-label">Méthode de paiement :</span>
                                    <span class="detail-value"><?= strtoupper($order['payment_method']) ?></span>
                                </div>
                                
                                <?php if ($order['notes']): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Notes :</span>
                                    <span class="detail-value"><?= htmlspecialchars($order['notes']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Section paiement -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">
                                <i class="fas fa-credit-card"></i>
                                Paiement
                            </h2>
                        </div>
                        <div class="card-body">
                            <?php if ($order['status'] === 'pending' || ($order['status'] === 'processing' && !$order['payment_proof'])): ?>
                                <!-- Instructions de paiement -->
                                <div class="payment-instructions">
                                    <h3>Instructions de paiement</h3>
                                    <p><?= getSetting('payment_instructions', 'Envoyez le montant exact via Mobile Money puis uploadez la preuve de paiement.') ?></p>
                                    
                                    <div class="payment-methods">
                                        <h4>Numéros Mobile Money :</h4>
                                        <?php foreach (getPaymentMethods() as $method => $number): ?>
                                        <div class="payment-method">
                                            <strong><?= strtoupper($method) ?> :</strong>
                                            <span class="phone-number" onclick="copyToClipboard('<?= $number ?>')"><?= $number ?></span>
                                            <button class="btn-copy" onclick="copyToClipboard('<?= $number ?>')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="amount-to-pay">
                                        <strong>Montant à envoyer : <?= formatPrice($order['total_amount']) ?></strong>
                                    </div>
                                </div>

                                <!-- Formulaire d'upload -->
                                <?php if (!$order['payment_proof']): ?>
                                <div class="upload-section">
                                    <h3>Uploader la preuve de paiement</h3>
                                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                                        <div class="form-group">
                                            <label class="form-label" for="payment_proof">
                                                Preuve de paiement (Image JPG, PNG ou PDF) *
                                            </label>
                                            <input type="file" 
                                                   class="form-control form-file" 
                                                   id="payment_proof" 
                                                   name="payment_proof" 
                                                   accept="image/*,.pdf" 
                                                   required>
                                            <div class="form-help">
                                                Formats acceptés : JPG, PNG, PDF (max 5MB)
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="upload_proof" class="btn btn-primary btn-lg">
                                            <i class="fas fa-upload"></i>
                                            Envoyer la preuve de paiement
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <!-- Preuve de paiement uploadée -->
                                <div class="payment-completed">
                                    <?php if ($order['payment_proof']): ?>
                                        <div class="proof-uploaded">
                                            <i class="fas fa-check-circle text-success"></i>
                                            <h3>Preuve de paiement reçue</h3>
                                            <p>Votre preuve de paiement a été reçue et votre commande est en cours de traitement.</p>
                                            
                                            <div class="proof-preview">
                                                <img src="<?= UPLOADS_URL ?>/payments/<?= htmlspecialchars($order['payment_proof']) ?>" 
                                                     alt="Preuve de paiement" 
                                                     class="proof-image"
                                                     onclick="showImageModal(this.src)">
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] === 'completed'): ?>
                                        <div class="order-completed">
                                            <i class="fas fa-trophy text-success"></i>
                                            <h3>Commande terminée !</h3>
                                            <p>Votre commande a été traitée avec succès. Merci de votre confiance !</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="order-actions">
                    <a href="/" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Retour à l'accueil
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="/dashboard/" class="btn btn-primary">
                            <i class="fas fa-user"></i>
                            Mon dashboard
                        </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?= str_replace(['+', ' '], '', getSetting('contact_whatsapp', '')) ?>?text=Bonjour, j'ai une question concernant ma commande #<?= $orderId ?>" 
                       class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        Contacter le support
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal pour afficher l'image -->
    <div class="modal" id="image-modal">
        <div class="modal-content image-modal-content">
            <div class="modal-header">
                <h2>Preuve de paiement</h2>
                <button class="modal-close" onclick="closeModal('image-modal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <img id="modal-image" src="" alt="Preuve de paiement" class="modal-image">
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    
    <script>
        // Afficher l'image en modal
        function showImageModal(src) {
            document.getElementById('modal-image').src = src;
            showModal('image-modal');
        }
        
        // Copier dans le presse-papiers
        async function copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                showSuccess('Numéro copié dans le presse-papiers');
            } catch (err) {
                // Fallback pour les navigateurs plus anciens
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showSuccess('Numéro copié dans le presse-papiers');
            }
        }
        
        // Auto-refresh du statut toutes les 30 secondes si la commande est en cours
        <?php if (in_array($order['status'], ['pending', 'processing'])): ?>
        setInterval(function() {
            location.reload();
        }, 30000);
        <?php endif; ?>
    </script>

    <!-- CSS spécifique à la page -->
    <style>
        .order-page {
            max-width: 1000px;
            margin: 0 auto;
            padding: var(--spacing-xl) 0;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }
        
        .order-title {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .order-title h1 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .order-date {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }
        
        .order-details {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--text-muted);
        }
        
        .detail-value {
            text-align: right;
            color: var(--text-primary);
        }
        
        .total-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .link-external {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            color: var(--primary-color);
        }
        
        .payment-instructions {
            margin-bottom: var(--spacing-xl);
        }
        
        .payment-methods {
            background: var(--bg-secondary);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin: var(--spacing-lg) 0;
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-sm);
        }
        
        .phone-number {
            font-family: monospace;
            background: var(--bg-tertiary);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .phone-number:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-copy {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .btn-copy:hover {
            color: var(--primary-color);
            background: var(--bg-tertiary);
        }
        
        .amount-to-pay {
            background: var(--accent-color);
            color: white;
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            text-align: center;
            font-size: 1.125rem;
        }
        
        .upload-section {
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--border-color);
        }
        
        .form-help {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: var(--spacing-xs);
        }
        
        .payment-completed {
            text-align: center;
        }
        
        .proof-uploaded {
            margin-bottom: var(--spacing-xl);
        }
        
        .proof-preview {
            margin-top: var(--spacing-lg);
        }
        
        .proof-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: contain;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .proof-image:hover {
            transform: scale(1.05);
        }
        
        .order-completed {
            color: var(--success);
        }
        
        .order-actions {
            display: flex;
            justify-content: center;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-2xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--border-color);
        }
        
        .image-modal-content {
            max-width: 90vw;
            max-height: 90vh;
        }
        
        .modal-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }
        
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                gap: var(--spacing-md);
                text-align: center;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-xs);
            }
            
            .detail-value {
                text-align: left;
            }
            
            .payment-method {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-sm);
            }
            
            .order-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</body>
</html>