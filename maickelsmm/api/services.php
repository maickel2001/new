<?php
/**
 * API Services - MaickelSMM
 * Endpoint pour récupérer les services et catégories
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetServices();
            break;
        default:
            throw new Exception('Méthode non autorisée', 405);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Récupérer les services et catégories
 */
function handleGetServices() {
    try {
        // Paramètres de requête
        $category_id = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';
        $limit = intval($_GET['limit'] ?? 100);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Récupérer les catégories
        $categories = getCategories(true);
        
        // Récupérer les services
        if ($category_id) {
            $services = getServicesByCategory($category_id, true);
        } elseif ($search) {
            $services = searchServices($search, $category_id);
        } else {
            $services = getAllServices(true);
        }
        
        // Appliquer la pagination si nécessaire
        if ($limit > 0) {
            $total = count($services);
            $services = array_slice($services, $offset, $limit);
        } else {
            $total = count($services);
        }
        
        // Formater les données
        $formattedServices = array_map('formatServiceForApi', $services);
        $formattedCategories = array_map('formatCategoryForApi', $categories);
        
        echo json_encode([
            'success' => true,
            'services' => $formattedServices,
            'categories' => $formattedCategories,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Erreur lors de la récupération des services: ' . $e->getMessage());
    }
}

/**
 * Formater un service pour l'API
 */
function formatServiceForApi($service) {
    return [
        'id' => intval($service['id']),
        'category_id' => intval($service['category_id']),
        'name' => $service['name'],
        'description' => $service['description'],
        'min_quantity' => intval($service['min_quantity']),
        'max_quantity' => intval($service['max_quantity']),
        'price_per_1000' => floatval($service['price_per_1000']),
        'delivery_time' => $service['delivery_time'],
        'guarantee' => $service['guarantee'],
        'status' => $service['status'],
        'category_name' => $service['category_name'] ?? '',
        'category_icon' => $service['category_icon'] ?? 'fas fa-star',
        'formatted_price' => formatPrice($service['price_per_1000']),
        'created_at' => $service['created_at'],
        'updated_at' => $service['updated_at']
    ];
}

/**
 * Formater une catégorie pour l'API
 */
function formatCategoryForApi($category) {
    return [
        'id' => intval($category['id']),
        'name' => $category['name'],
        'description' => $category['description'],
        'icon' => $category['icon'],
        'sort_order' => intval($category['sort_order']),
        'status' => $category['status'],
        'created_at' => $category['created_at']
    ];
}
?>