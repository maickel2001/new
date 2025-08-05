<?php
/**
 * API Create Order - MaickelSMM
 * Endpoint pour créer une nouvelle commande
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée', 405);
    }
    
    handleCreateOrder();
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Créer une nouvelle commande
 */
function handleCreateOrder() {
    try {
        // Récupérer les données POST
        $data = [
            'service_id' => $_POST['service_id'] ?? '',
            'quantity' => $_POST['quantity'] ?? '',
            'link' => $_POST['link'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? '',
            'guest_name' => $_POST['guest_name'] ?? '',
            'guest_email' => $_POST['guest_email'] ?? '',
            'guest_phone' => $_POST['guest_phone'] ?? ''
        ];
        
        // Si l'utilisateur est connecté, utiliser son ID
        if (isLoggedIn()) {
            $user = getCurrentUser();
            $data['user_id'] = $user['id'];
        }
        
        // Nettoyer les données
        $data = array_map('cleanInput', $data);
        
        // Valider les données
        $errors = validateOrderData($data);
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            return;
        }
        
        // Récupérer le service
        $service = getServiceById($data['service_id']);
        if (!$service) {
            throw new Exception('Service introuvable');
        }
        
        // Calculer le montant total
        $data['total_amount'] = calculateOrderTotal($data['service_id'], intval($data['quantity']));
        
        // Créer la commande
        $orderId = createOrder($data);
        
        if (!$orderId) {
            throw new Exception('Erreur lors de la création de la commande');
        }
        
        // Envoyer l'email de confirmation (si configuré)
        sendOrderConfirmationEmail($orderId, $data);
        
        // Réponse de succès
        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'total_amount' => $data['total_amount'],
            'formatted_amount' => formatPrice($data['total_amount']),
            'message' => 'Commande créée avec succès'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la création de la commande: ' . $e->getMessage());
    }
}

/**
 * Envoyer l'email de confirmation de commande
 */
function sendOrderConfirmationEmail($orderId, $orderData) {
    try {
        $order = getOrderById($orderId);
        if (!$order) return;
        
        $email = $orderData['guest_email'] ?? $order['email'] ?? null;
        if (!$email) return;
        
        $subject = "Confirmation de commande #$orderId - " . getSetting('site_name', 'MaickelSMM');
        
        $message = "
            <h2>Confirmation de votre commande</h2>
            <p>Bonjour " . ($orderData['guest_name'] ?? 'Client') . ",</p>
            <p>Votre commande a été créée avec succès. Voici les détails :</p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3>Détails de la commande</h3>
                <p><strong>Numéro de commande :</strong> #$orderId</p>
                <p><strong>Service :</strong> {$order['service_name']}</p>
                <p><strong>Quantité :</strong> " . number_format($orderData['quantity']) . "</p>
                <p><strong>Lien :</strong> {$orderData['link']}</p>
                <p><strong>Montant total :</strong> " . formatPrice($orderData['total_amount']) . "</p>
                <p><strong>Méthode de paiement :</strong> " . strtoupper($orderData['payment_method']) . "</p>
            </div>
            
            <h3>Instructions de paiement</h3>
            <p>" . getSetting('payment_instructions', 'Veuillez effectuer le paiement et uploader la preuve.') . "</p>
            
            <div style='background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                <h4>Numéros de paiement Mobile Money :</h4>";
        
        $paymentMethods = getPaymentMethods();
        foreach ($paymentMethods as $method => $number) {
            $message .= "<p><strong>" . strtoupper($method) . " :</strong> $number</p>";
        }
        
        $message .= "
            </div>
            
            <p><strong>Prochaines étapes :</strong></p>
            <ol>
                <li>Effectuez le paiement via Mobile Money</li>
                <li>Rendez-vous sur <a href='" . BASE_URL . "/order/$orderId'>cette page</a> pour uploader votre preuve de paiement</li>
                <li>Votre commande sera traitée dans les plus brefs délais</li>
            </ol>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter via WhatsApp : " . getSetting('contact_whatsapp', '') . "</p>
            
            <p>Merci de votre confiance !</p>
            <p>L'équipe " . getSetting('site_name', 'MaickelSMM') . "</p>
        ";
        
        sendEmail($email, $subject, $message);
        
    } catch (Exception $e) {
        error_log("Erreur envoi email confirmation: " . $e->getMessage());
    }
}

/**
 * Valider les données de commande (version API)
 */
function validateOrderData($data) {
    $errors = [];
    
    // Vérifier le service
    if (empty($data['service_id'])) {
        $errors[] = "Service requis";
    } else {
        $service = getServiceById($data['service_id']);
        if (!$service || $service['status'] !== 'active') {
            $errors[] = "Service invalide ou inactif";
        }
    }
    
    // Vérifier la quantité
    if (empty($data['quantity']) || !is_numeric($data['quantity'])) {
        $errors[] = "Quantité requise et doit être numérique";
    } elseif (isset($service)) {
        $quantity = intval($data['quantity']);
        if ($quantity < $service['min_quantity']) {
            $errors[] = "Quantité minimum: " . number_format($service['min_quantity']);
        }
        if ($quantity > $service['max_quantity']) {
            $errors[] = "Quantité maximum: " . number_format($service['max_quantity']);
        }
    }
    
    // Vérifier le lien
    if (empty($data['link'])) {
        $errors[] = "Lien requis";
    } elseif (!filter_var($data['link'], FILTER_VALIDATE_URL)) {
        $errors[] = "Lien invalide. Veuillez entrer une URL complète (ex: https://instagram.com/username)";
    }
    
    // Vérifier la méthode de paiement
    if (empty($data['payment_method'])) {
        $errors[] = "Méthode de paiement requise";
    } else {
        $paymentMethods = getPaymentMethods();
        if (!array_key_exists($data['payment_method'], $paymentMethods)) {
            $errors[] = "Méthode de paiement invalide";
        }
    }
    
    // Si commande invité (pas connecté), vérifier les informations
    if (empty($data['user_id'])) {
        if (empty($data['guest_name']) || strlen(trim($data['guest_name'])) < 2) {
            $errors[] = "Nom complet requis (minimum 2 caractères)";
        }
        if (empty($data['guest_email']) || !isValidEmail($data['guest_email'])) {
            $errors[] = "Email valide requis";
        }
        if (empty($data['guest_phone']) || strlen(trim($data['guest_phone'])) < 8) {
            $errors[] = "Numéro de téléphone valide requis";
        }
    }
    
    return $errors;
}
?>