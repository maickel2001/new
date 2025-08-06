<?php
/**
 * Fonctions principales - MaickelSMM
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/config.php';

/**
 * Obtenir tous les paramètres du site
 */
function getSettings() {
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT setting_key, setting_value, setting_type FROM settings WHERE 1");
    
    $result = [];
    foreach ($settings as $setting) {
        $value = $setting['setting_value'];
        
        // Convertir selon le type
        switch ($setting['setting_type']) {
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'number':
                $value = is_numeric($value) ? (float) $value : 0;
                break;
            case 'json':
                $value = json_decode($value, true) ?: [];
                break;
        }
        
        $result[$setting['setting_key']] = $value;
    }
    
    return $result;
}

/**
 * Obtenir un paramètre spécifique
 */
function getSetting($key, $default = null) {
    $db = Database::getInstance();
    $setting = $db->fetchOne("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?", [$key]);
    
    if (!$setting) {
        return $default;
    }
    
    $value = $setting['setting_value'];
    
    switch ($setting['setting_type']) {
        case 'boolean':
            return (bool) $value;
        case 'number':
            return is_numeric($value) ? (float) $value : 0;
        case 'json':
            return json_decode($value, true) ?: [];
        default:
            return $value;
    }
}

/**
 * Mettre à jour un paramètre
 */
function updateSetting($key, $value, $type = 'text') {
    $db = Database::getInstance();
    
    if ($type === 'json') {
        $value = json_encode($value);
    } elseif ($type === 'boolean') {
        $value = $value ? '1' : '0';
    }
    
    $existing = $db->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
    
    if ($existing) {
        return $db->execute("UPDATE settings SET setting_value = ?, setting_type = ?, updated_at = NOW() WHERE setting_key = ?", 
                           [$value, $type, $key]);
    } else {
        return $db->execute("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)", 
                           [$key, $value, $type]);
    }
}

/**
 * Obtenir toutes les catégories actives
 */
function getCategories($active_only = true) {
    $db = Database::getInstance();
    $where = $active_only ? "WHERE is_active = 1" : "";
    return $db->fetchAll("SELECT * FROM categories $where ORDER BY sort_order ASC, name ASC");
}

/**
 * Obtenir une catégorie par ID
 */
function getCategoryById($id) {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
}

/**
 * Obtenir les services d'une catégorie
 */
function getServicesByCategory($category_id, $active_only = true) {
    $db = Database::getInstance();
    $where = $active_only ? "AND s.status = 'active'" : "";
    
    return $db->fetchAll("
        SELECT s.*, c.name as category_name 
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        WHERE s.category_id = ? $where 
        ORDER BY s.sort_order ASC, s.name ASC
    ", [$category_id]);
}

/**
 * Obtenir tous les services actifs
 */
function getAllServices($active_only = true) {
    $db = Database::getInstance();
    $where = $active_only ? "WHERE s.status = 'active' AND c.is_active = 1" : "";
    
    return $db->fetchAll("
        SELECT s.*, c.name as category_name, c.icon as category_icon
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        $where
        ORDER BY c.sort_order ASC, s.sort_order ASC, s.name ASC
    ");
}

/**
 * Obtenir un service par ID
 */
function getServiceById($id) {
    $db = Database::getInstance();
    return $db->fetchOne("
        SELECT s.*, c.name as category_name, c.icon as category_icon
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        WHERE s.id = ?
    ", [$id]);
}

/**
 * Rechercher des services
 */
function searchServices($query, $category_id = null) {
    $db = Database::getInstance();
    $params = ["%$query%", "%$query%"];
    $where = "WHERE (s.name LIKE ? OR s.description LIKE ?) AND s.status = 'active'";
    
    if ($category_id) {
        $where .= " AND s.category_id = ?";
        $params[] = $category_id;
    }
    
    return $db->fetchAll("
        SELECT s.*, c.name as category_name, c.icon as category_icon
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        $where
        ORDER BY s.name ASC
    ", $params);
}

/**
 * Créer une nouvelle commande
 */
function createOrder($data) {
    $db = Database::getInstance();
    
    try {
        $db->beginTransaction();
        
        // Insérer la commande
        $query = "INSERT INTO orders (user_id, service_id, quantity, link, total_amount, payment_method, guest_name, guest_email, guest_phone, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $params = [
            $data['user_id'] ?? null,
            $data['service_id'],
            $data['quantity'],
            $data['link'],
            $data['total_amount'],
            $data['payment_method'],
            $data['guest_name'] ?? null,
            $data['guest_email'] ?? null,
            $data['guest_phone'] ?? null
        ];
        
        $db->execute($query, $params);
        $order_id = $db->lastInsertId();
        
        // Créer l'enregistrement de paiement
        $payment_query = "INSERT INTO payments (order_id, user_id, amount, currency, payment_method, status, created_at) 
                         VALUES (?, ?, ?, 'XOF', ?, 'pending', NOW())";
        
        $payment_params = [
            $order_id,
            $data['user_id'] ?? null,
            $data['total_amount'],
            $data['payment_method']
        ];
        
        $db->execute($payment_query, $payment_params);
        
        $db->commit();
        return $order_id;
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

/**
 * Obtenir une commande par ID
 */
function getOrderById($id, $user_id = null) {
    $db = Database::getInstance();
    $params = [$id];
    $where = "WHERE o.id = ?";
    
    if ($user_id) {
        $where .= " AND (o.user_id = ? OR o.user_id IS NULL)";
        $params[] = $user_id;
    }
    
    return $db->fetchOne("
        SELECT o.*, s.name as service_name, s.description as service_description,
               c.name as category_name, c.icon as category_icon,
               p.payment_proof, p.transaction_id
        FROM orders o
        JOIN services s ON o.service_id = s.id
        JOIN categories c ON s.category_id = c.id
        LEFT JOIN payments p ON o.id = p.order_id
        $where
    ", $params);
}

/**
 * Obtenir les commandes d'un utilisateur
 */
function getUserOrders($user_id, $limit = null, $offset = 0) {
    $db = Database::getInstance();
    $limit_clause = $limit ? "LIMIT $limit OFFSET $offset" : "";
    
    return $db->fetchAll("
        SELECT o.*, s.name as service_name, s.description as service_description,
               c.name as category_name, c.icon as category_icon
        FROM orders o
        JOIN services s ON o.service_id = s.id
        JOIN categories c ON s.category_id = c.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
        $limit_clause
    ", [$user_id]);
}

/**
 * Mettre à jour le statut d'une commande
 */
function updateOrderStatus($order_id, $status, $notes = null) {
    $db = Database::getInstance();
    $params = [$status, $order_id];
    $query = "UPDATE orders SET status = ?, updated_at = NOW()";
    
    if ($notes) {
        $query .= ", notes = ?";
        array_splice($params, 1, 0, [$notes]);
    }
    
    $query .= " WHERE id = ?";
    
    return $db->execute($query, $params);
}

/**
 * Uploader une preuve de paiement
 */
function uploadPaymentProof($file, $order_id) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new Exception("Aucun fichier uploadé");
    }
    
    // Vérifier la taille
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception("Le fichier est trop volumineux (max 5MB)");
    }
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
        throw new Exception("Type de fichier non autorisé");
    }
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir(PAYMENT_PROOF_PATH)) {
        mkdir(PAYMENT_PROOF_PATH, 0755, true);
    }
    
    // Générer un nom unique
    $filename = 'payment_' . $order_id . '_' . time() . '.' . $extension;
    $filepath = PAYMENT_PROOF_PATH . '/' . $filename;
    
    // Déplacer le fichier
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("Erreur lors de l'upload du fichier");
    }
    
    // Mettre à jour la base de données
    $db = Database::getInstance();
    $db->execute("UPDATE orders SET payment_proof = ? WHERE id = ?", [$filename, $order_id]);
    $db->execute("UPDATE payments SET payment_proof = ? WHERE order_id = ?", [$filename, $order_id]);
    
    return $filename;
}

/**
 * Obtenir les statistiques du dashboard
 */
function getDashboardStats($user_id = null) {
    $db = Database::getInstance();
    
    if ($user_id) {
        // Statistiques utilisateur
        $stats = [
            'total_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE user_id = ?", [$user_id])['count'],
            'pending_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status = 'pending'", [$user_id])['count'],
            'completed_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status = 'completed'", [$user_id])['count'],
            'total_spent' => $db->fetchOne("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = ? AND status != 'cancelled'", [$user_id])['total']
        ];
    } else {
        // Statistiques admin
        $stats = [
            'total_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders")['count'],
            'pending_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")['count'],
            'processing_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE status = 'processing'")['count'],
            'completed_orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE status = 'completed'")['count'],
            'total_revenue' => $db->fetchOne("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'completed'")['total'],
            'total_users' => $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'],
            'total_services' => $db->fetchOne("SELECT COUNT(*) as count FROM services WHERE status = 'active'")['count'],
            'recent_orders' => $db->fetchAll("
                SELECT o.*, s.name as service_name, u.username, u.email
                FROM orders o
                JOIN services s ON o.service_id = s.id
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT 10
            ")
        ];
    }
    
    return $stats;
}

/**
 * Envoyer un email
 */
function sendEmail($to, $subject, $message, $headers = []) {
    $default_headers = [
        'From' => getSetting('contact_email', ADMIN_EMAIL),
        'Reply-To' => getSetting('contact_email', ADMIN_EMAIL),
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Mailer' => 'MaickelSMM v' . SITE_VERSION
    ];
    
    $headers = array_merge($default_headers, $headers);
    $header_string = '';
    
    foreach ($headers as $key => $value) {
        $header_string .= "$key: $value\r\n";
    }
    
    return mail($to, $subject, $message, $header_string);
}

/**
 * Logger une action admin
 */
function logAdminAction($user_id, $action, $details = null) {
    $db = Database::getInstance();
    return $db->execute("INSERT INTO admin_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())", 
                       [$user_id, $action, $details, getClientIP()]);
}

/**
 * Obtenir les logs admin
 */
function getAdminLogs($limit = 50, $offset = 0) {
    $db = Database::getInstance();
    return $db->fetchAll("
        SELECT l.*, u.username, u.email
        FROM admin_logs l
        JOIN users u ON l.user_id = u.id
        ORDER BY l.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
}

/**
 * Nettoyer les anciens logs
 */
function cleanOldLogs($days = 90) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM admin_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$days]);
}

/**
 * Vérifier si le site est en maintenance
 */
function isMaintenanceMode() {
    return getSetting('maintenance_mode', false);
}

/**
 * Obtenir les méthodes de paiement
 */
function getPaymentMethods() {
    return getSetting('payment_methods', []);
}

/**
 * Calculer le prix total d'une commande
 */
function calculateOrderTotal($service_id, $quantity) {
    $service = getServiceById($service_id);
    if (!$service) {
        throw new Exception("Service introuvable");
    }
    
    if ($quantity < $service['min_quantity'] || $quantity > $service['max_quantity']) {
        throw new Exception("Quantité invalide");
    }
    
    return ($service['price_per_1000'] / 1000) * $quantity;
}

/**
 * Valider les données d'une commande
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
        if ($data['quantity'] < $service['min_quantity']) {
            $errors[] = "Quantité minimum: " . $service['min_quantity'];
        }
        if ($data['quantity'] > $service['max_quantity']) {
            $errors[] = "Quantité maximum: " . $service['max_quantity'];
        }
    }
    
    // Vérifier le lien
    if (empty($data['link'])) {
        $errors[] = "Lien requis";
    } elseif (!filter_var($data['link'], FILTER_VALIDATE_URL)) {
        $errors[] = "Lien invalide";
    }
    
    // Vérifier la méthode de paiement
    if (empty($data['payment_method'])) {
        $errors[] = "Méthode de paiement requise";
    } else {
        $payment_methods = getPaymentMethods();
        if (!array_key_exists($data['payment_method'], $payment_methods)) {
            $errors[] = "Méthode de paiement invalide";
        }
    }
    
    // Si commande invité, vérifier les informations
    if (empty($data['user_id'])) {
        if (empty($data['guest_name'])) {
            $errors[] = "Nom requis";
        }
        if (empty($data['guest_email']) || !isValidEmail($data['guest_email'])) {
            $errors[] = "Email valide requis";
        }
        if (empty($data['guest_phone'])) {
            $errors[] = "Téléphone requis";
        }
    }
    
    return $errors;
}

/**
 * Générer un numéro de commande unique
 */
function generateOrderNumber() {
    return 'MSM' . date('Ymd') . rand(1000, 9999);
}

/**
 * Obtenir les pages du site
 */
function getPages($active_only = true) {
    $db = Database::getInstance();
    $where = $active_only ? "WHERE status = 'active'" : "";
    return $db->fetchAll("SELECT * FROM pages $where ORDER BY title ASC");
}

/**
 * Obtenir une page par slug
 */
function getPageBySlug($slug) {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM pages WHERE slug = ? AND status = 'active'", [$slug]);
}

/**
 * Créer ou mettre à jour une page
 */
function savePage($data) {
    $db = Database::getInstance();
    
    if (isset($data['id']) && $data['id']) {
        // Mise à jour
        return $db->execute("UPDATE pages SET title = ?, slug = ?, content = ?, meta_description = ?, meta_keywords = ?, status = ?, updated_at = NOW() WHERE id = ?",
                           [$data['title'], $data['slug'], $data['content'], $data['meta_description'], $data['meta_keywords'], $data['status'], $data['id']]);
    } else {
        // Création
        return $db->execute("INSERT INTO pages (title, slug, content, meta_description, meta_keywords, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())",
                           [$data['title'], $data['slug'], $data['content'], $data['meta_description'], $data['meta_keywords'], $data['status']]);
    }
}

/**
 * Supprimer une page
 */
function deletePage($id) {
    $db = Database::getInstance();
    return $db->execute("DELETE FROM pages WHERE id = ?", [$id]);
}

/**
 * Enregistrer un message de contact
 */
function saveContactMessage($data) {
    $db = Database::getInstance();
    return $db->execute("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())",
                       [$data['name'], $data['email'], $data['subject'], $data['message']]);
}

/**
 * Obtenir les messages de contact
 */
function getContactMessages($status = null, $limit = 50, $offset = 0) {
    $db = Database::getInstance();
    $where = $status ? "WHERE status = '$status'" : "";
    return $db->fetchAll("SELECT * FROM contact_messages $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
}

/**
 * Marquer un message comme lu
 */
function markMessageAsRead($id) {
    $db = Database::getInstance();
    return $db->execute("UPDATE contact_messages SET status = 'read' WHERE id = ?", [$id]);
}

?>