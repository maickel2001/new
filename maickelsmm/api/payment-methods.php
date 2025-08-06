<?php
/**
 * API Payment Methods - MaickelSMM
 * Endpoint pour récupérer les méthodes de paiement
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Méthode non autorisée', 405);
    }
    
    handleGetPaymentMethods();
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Récupérer les méthodes de paiement
 */
function handleGetPaymentMethods() {
    try {
        // Récupérer les méthodes de paiement depuis les paramètres
        $paymentMethods = getPaymentMethods();
        
        // Ajouter les instructions de paiement
        $paymentInstructions = getSetting('payment_instructions', 'Envoyez le montant exact via Mobile Money puis uploadez la preuve de paiement.');
        
        // Formater la réponse
        echo json_encode([
            'success' => true,
            'methods' => $paymentMethods,
            'instructions' => $paymentInstructions,
            'currency' => getSetting('currency', 'XOF'),
            'currency_symbol' => getSetting('currency_symbol', 'FCFA'),
            'min_amount' => getSetting('min_deposit', 1000)
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la récupération des méthodes de paiement: ' . $e->getMessage());
    }
}
?>