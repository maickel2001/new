<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Gestion des requêtes OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = getDB();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1 
                GROUP BY c.id 
                ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>