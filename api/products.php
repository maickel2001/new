<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Gestion des requêtes OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = getDB();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetProducts($db);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}

function handleGetProducts($db) {
    $category = $_GET['category'] ?? '';
    $featured = $_GET['featured'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 20);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1";
    
    $params = [];
    
    if ($category) {
        $sql .= " AND c.slug = :category";
        $params['category'] = $category;
    }
    
    if ($featured) {
        $sql .= " AND p.is_featured = 1";
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    // Decode JSON fields
    foreach ($products as &$product) {
        $product['features'] = json_decode($product['features'] ?? '[]', true);
        $product['specifications'] = json_decode($product['specifications'] ?? '[]', true);
        $product['price'] = (float)$product['price'];
        $product['original_price'] = $product['original_price'] ? (float)$product['original_price'] : null;
        $product['rating'] = (float)$product['rating'];
    }
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1";
    if ($category) {
        $countSql .= " AND c.slug = :category";
    }
    if ($featured) {
        $countSql .= " AND p.is_featured = 1";
    }
    if ($search) {
        $countSql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    }
    
    $countStmt = $db->prepare($countSql);
    foreach ($params as $key => $value) {
        if ($key !== 'limit' && $key !== 'offset') {
            $countStmt->bindValue(':' . $key, $value);
        }
    }
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $products,
        'total' => (int)$total,
        'limit' => $limit,
        'offset' => $offset
    ]);
}
?>