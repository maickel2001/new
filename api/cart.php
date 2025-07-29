<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';
session_start();

// Gestion des requêtes OPTIONS (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = getDB();
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Get session ID for guest users
    $sessionId = session_id();
    $userId = $_SESSION['user_id'] ?? null;
    
    switch ($method) {
        case 'GET':
            handleGetCart($db, $userId, $sessionId);
            break;
        case 'POST':
            handleAddToCart($db, $userId, $sessionId, $input);
            break;
        case 'PUT':
            handleUpdateCart($db, $userId, $sessionId, $input);
            break;
        case 'DELETE':
            handleRemoveFromCart($db, $userId, $sessionId, $input);
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

function handleGetCart($db, $userId, $sessionId) {
    $sql = "SELECT c.*, p.name, p.price, p.image, p.stock_status 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE ";
    
    if ($userId) {
        $sql .= "c.user_id = :user_id";
        $params = ['user_id' => $userId];
    } else {
        $sql .= "c.session_id = :session_id";
        $params = ['session_id' => $sessionId];
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();
    
    $total = 0;
    foreach ($items as &$item) {
        $item['price'] = (float)$item['price'];
        $item['total'] = $item['price'] * $item['quantity'];
        $total += $item['total'];
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'items' => $items,
            'total' => $total,
            'count' => count($items)
        ]
    ]);
}

function handleAddToCart($db, $userId, $sessionId, $input) {
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if (!$productId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID produit requis']);
        return;
    }
    
    // Check if product exists and is active
    $stmt = $db->prepare("SELECT id, name, price, stock_status FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Produit non trouvé']);
        return;
    }
    
    if ($product['stock_status'] === 'out_of_stock') {
        http_response_code(400);
        echo json_encode(['error' => 'Produit en rupture de stock']);
        return;
    }
    
    // Check if item already in cart
    $checkSql = "SELECT id, quantity FROM cart WHERE product_id = :product_id AND ";
    if ($userId) {
        $checkSql .= "user_id = :user_id";
        $checkParams = ['product_id' => $productId, 'user_id' => $userId];
    } else {
        $checkSql .= "session_id = :session_id";
        $checkParams = ['product_id' => $productId, 'session_id' => $sessionId];
    }
    
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute($checkParams);
    $existingItem = $checkStmt->fetch();
    
    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + $quantity;
        $updateSql = "UPDATE cart SET quantity = :quantity, updated_at = NOW() WHERE id = :id";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute(['quantity' => $newQuantity, 'id' => $existingItem['id']]);
    } else {
        // Insert new item
        $insertSql = "INSERT INTO cart (user_id, session_id, product_id, quantity) VALUES (:user_id, :session_id, :product_id, :quantity)";
        $insertStmt = $db->prepare($insertSql);
        $insertStmt->execute([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Produit ajouté au panier'
    ]);
}

function handleUpdateCart($db, $userId, $sessionId, $input) {
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if (!$productId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID produit requis']);
        return;
    }
    
    $sql = "UPDATE cart SET quantity = :quantity, updated_at = NOW() 
            WHERE product_id = :product_id AND ";
    
    if ($userId) {
        $sql .= "user_id = :user_id";
        $params = ['quantity' => $quantity, 'product_id' => $productId, 'user_id' => $userId];
    } else {
        $sql .= "session_id = :session_id";
        $params = ['quantity' => $quantity, 'product_id' => $productId, 'session_id' => $sessionId];
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Panier mis à jour'
    ]);
}

function handleRemoveFromCart($db, $userId, $sessionId, $input) {
    $productId = $input['product_id'] ?? null;
    
    if (!$productId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID produit requis']);
        return;
    }
    
    $sql = "DELETE FROM cart WHERE product_id = :product_id AND ";
    
    if ($userId) {
        $sql .= "user_id = :user_id";
        $params = ['product_id' => $productId, 'user_id' => $userId];
    } else {
        $sql .= "session_id = :session_id";
        $params = ['product_id' => $productId, 'session_id' => $sessionId];
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Produit retiré du panier'
    ]);
}
?>