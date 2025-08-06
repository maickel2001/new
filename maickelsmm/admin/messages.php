<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/security.php';

// Vérifier l'authentification admin
$auth->requireAuth('admin');

$user = $auth->getCurrentUser();
$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';

$db = Database::getInstance();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Token de sécurité invalide.');
        redirect('/admin/messages.php');
    }
    
    $action = $_POST['action'] ?? '';
    $messageId = intval($_POST['message_id'] ?? 0);
    
    if ($action === 'mark_read' && $messageId > 0) {
        $updated = $db->execute("UPDATE contact_messages SET is_read = 1, updated_at = NOW() WHERE id = ?", [$messageId]);
        if ($updated) {
            logAdminAction($user['id'], 'message_mark_read', "Message #$messageId marqué comme lu");
            setFlashMessage('success', 'Message marqué comme lu.');
        } else {
            setFlashMessage('error', 'Erreur lors de la mise à jour du message.');
        }
        redirect('/admin/messages.php');
    }
    
    if ($action === 'mark_unread' && $messageId > 0) {
        $updated = $db->execute("UPDATE contact_messages SET is_read = 0, updated_at = NOW() WHERE id = ?", [$messageId]);
        if ($updated) {
            logAdminAction($user['id'], 'message_mark_unread', "Message #$messageId marqué comme non lu");
            setFlashMessage('success', 'Message marqué comme non lu.');
        } else {
            setFlashMessage('error', 'Erreur lors de la mise à jour du message.');
        }
        redirect('/admin/messages.php');
    }
    
    if ($action === 'delete_message' && $messageId > 0) {
        $deleted = $db->execute("DELETE FROM contact_messages WHERE id = ?", [$messageId]);
        if ($deleted) {
            logAdminAction($user['id'], 'message_delete', "Message #$messageId supprimé");
            setFlashMessage('success', 'Message supprimé avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression du message.');
        }
        redirect('/admin/messages.php');
    }
    
    if ($action === 'reply_message' && $messageId > 0) {
        $replySubject = cleanInput($_POST['reply_subject'] ?? '');
        $replyMessage = cleanInput($_POST['reply_message'] ?? '');
        
        if (!empty($replySubject) && !empty($replyMessage)) {
            // Récupérer les infos du message original
            $originalMessage = $db->fetchOne("SELECT * FROM contact_messages WHERE id = ?", [$messageId]);
            
            if ($originalMessage) {
                // Envoyer la réponse par email
                $emailSent = sendEmail(
                    $originalMessage['email'],
                    $replySubject,
                    $replyMessage,
                    ['Reply-To' => $settings['contact_email'] ?? '']
                );
                
                if ($emailSent) {
                    // Marquer le message comme lu et répondu
                    $db->execute("UPDATE contact_messages SET is_read = 1, replied_at = NOW(), updated_at = NOW() WHERE id = ?", [$messageId]);
                    
                    logAdminAction($user['id'], 'message_reply', "Réponse envoyée pour le message #$messageId");
                    setFlashMessage('success', 'Réponse envoyée avec succès.');
                } else {
                    setFlashMessage('error', 'Erreur lors de l\'envoi de la réponse.');
                }
            }
        } else {
            setFlashMessage('error', 'Veuillez remplir tous les champs de la réponse.');
        }
        redirect('/admin/messages.php');
    }
}

// Filtres et pagination
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$statusFilter = cleanInput($_GET['status'] ?? '');
$searchQuery = cleanInput($_GET['search'] ?? '');

// Construction de la requête
$whereConditions = ['1=1'];
$params = [];

if ($statusFilter === 'unread') {
    $whereConditions[] = 'is_read = 0';
} elseif ($statusFilter === 'read') {
    $whereConditions[] = 'is_read = 1';
} elseif ($statusFilter === 'replied') {
    $whereConditions[] = 'replied_at IS NOT NULL';
}

if (!empty($searchQuery)) {
    $whereConditions[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)';
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = implode(' AND ', $whereConditions);

// Compter le total
$totalMessages = $db->fetchOne("
    SELECT COUNT(*) as count
    FROM contact_messages
    WHERE $whereClause
", $params)['count'];

$totalPages = ceil($totalMessages / $limit);

// Récupérer les messages
$messages = $db->fetchAll("
    SELECT *
    FROM contact_messages
    WHERE $whereClause
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
", $params);

// Statistiques pour les filtres
$statusStats = [
    'total' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'],
    'unread' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'],
    'read' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 1")['count'],
    'replied' => $db->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE replied_at IS NOT NULL")['count']
];

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages de Contact - <?= htmlspecialchars($siteName) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-primary);
        }
        
        .admin-sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .messages-grid {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .message-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .message-card.unread {
            border-left: 4px solid var(--primary-color);
            background: rgba(99, 102, 241, 0.02);
        }
        
        .message-card.replied {
            border-left: 4px solid var(--success);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .message-sender {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .sender-name {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .sender-email {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .sender-phone {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .message-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }
        
        .message-date {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        
        .message-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .message-status.unread {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }
        
        .message-status.read {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }
        
        .message-status.replied {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .message-subject {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }
        
        .message-content {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-height: 150px;
            overflow: hidden;
            position: relative;
        }
        
        .message-content.expanded {
            max-height: none;
        }
        
        .message-fade {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, var(--bg-secondary));
            pointer-events: none;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-btn.reply {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .action-btn.mark-read {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }
        
        .action-btn.mark-unread {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }
        
        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .stats-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .stat-badge.active {
            background: var(--primary-color);
            color: white;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--bg-secondary);
            border-radius: 1rem;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .expand-btn {
            background: none;
            border: none;
            color: var(--primary-color);
            cursor: pointer;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 1024px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .message-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .message-meta {
                align-items: flex-start;
            }
        }
    </style>
</head>
<body class="admin-page">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-rocket"></i>
                    <span><?= htmlspecialchars($siteName) ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <a href="/admin/" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/admin/orders.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Catalogue</div>
                    <a href="/admin/services.php" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>Services</span>
                    </a>
                    <a href="/admin/categories.php" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>Catégories</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Utilisateurs</div>
                    <a href="/admin/users.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Clients</span>
                    </a>
                    <a href="/admin/messages.php" class="nav-link active">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                        <?php if ($statusStats['unread'] > 0): ?>
                            <span class="nav-badge"><?= $statusStats['unread'] ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Configuration</div>
                    <a href="/admin/settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="/admin/pages.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Système</div>
                    <a href="/admin/logs.php" class="nav-link">
                        <i class="fas fa-list-alt"></i>
                        <span>Logs</span>
                    </a>
                    <a href="/" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Voir le site</span>
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>
                    <i class="fas fa-envelope"></i>
                    Messages de Contact
                    <?php if ($statusStats['unread'] > 0): ?>
                        <span class="badge badge-primary"><?= $statusStats['unread'] ?></span>
                    <?php endif; ?>
                </h1>
                
                <div class="admin-user">
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="user-role"><?= ucfirst($user['role']) ?></div>
                    </div>
                    <a href="/logout.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashMessage['type'] ?>">
                    <i class="fas fa-<?= $flashMessage['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($flashMessage['message']) ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistiques -->
            <div class="stats-badges">
                <a href="/admin/messages.php" class="stat-badge <?= empty($statusFilter) ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    Tous (<?= $statusStats['total'] ?>)
                </a>
                <a href="/admin/messages.php?status=unread" class="stat-badge <?= $statusFilter === 'unread' ? 'active' : '' ?>">
                    <i class="fas fa-envelope-open"></i>
                    Non lus (<?= $statusStats['unread'] ?>)
                </a>
                <a href="/admin/messages.php?status=read" class="stat-badge <?= $statusFilter === 'read' ? 'active' : '' ?>">
                    <i class="fas fa-envelope-open-text"></i>
                    Lus (<?= $statusStats['read'] ?>)
                </a>
                <a href="/admin/messages.php?status=replied" class="stat-badge <?= $statusFilter === 'replied' ? 'active' : '' ?>">
                    <i class="fas fa-reply"></i>
                    Répondus (<?= $statusStats['replied'] ?>)
                </a>
            </div>
            
            <!-- Filtres -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filters-grid">
                        <div class="form-group">
                            <label for="search">Rechercher</label>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($searchQuery) ?>" 
                                   placeholder="Nom, email, sujet, message...">
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <div class="filter-buttons">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                Rechercher
                            </button>
                            <a href="/admin/messages.php" class="btn btn-outline">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Messages -->
            <?php if (empty($messages)): ?>
                <div class="empty-state" style="padding: 3rem; text-align: center;">
                    <i class="fas fa-envelope" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-primary); margin-bottom: 0.5rem;">Aucun message</h3>
                    <p style="color: var(--text-secondary);">Aucun message ne correspond aux critères sélectionnés.</p>
                </div>
            <?php else: ?>
                <div class="messages-grid">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card <?= $message['is_read'] ? ($message['replied_at'] ? 'replied' : 'read') : 'unread' ?>">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="sender-name"><?= htmlspecialchars($message['name']) ?></div>
                                    <div class="sender-email">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($message['email']) ?>
                                    </div>
                                    <?php if (!empty($message['phone'])): ?>
                                        <div class="sender-phone">
                                            <i class="fas fa-phone"></i>
                                            <?= htmlspecialchars($message['phone']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="message-meta">
                                    <div class="message-date"><?= formatDate($message['created_at']) ?></div>
                                    <div class="message-status <?= $message['is_read'] ? ($message['replied_at'] ? 'replied' : 'read') : 'unread' ?>">
                                        <i class="fas fa-<?= $message['replied_at'] ? 'reply' : ($message['is_read'] ? 'envelope-open' : 'envelope') ?>"></i>
                                        <?= $message['replied_at'] ? 'Répondu' : ($message['is_read'] ? 'Lu' : 'Non lu') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            
                            <div class="message-content" id="content-<?= $message['id'] ?>">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                                <?php if (strlen($message['message']) > 300): ?>
                                    <div class="message-fade"></div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (strlen($message['message']) > 300): ?>
                                <button class="expand-btn" onclick="toggleExpand(<?= $message['id'] ?>)">
                                    <span id="expand-text-<?= $message['id'] ?>">Voir plus</span>
                                    <i class="fas fa-chevron-down" id="expand-icon-<?= $message['id'] ?>"></i>
                                </button>
                            <?php endif; ?>
                            
                            <div class="message-actions">
                                <button class="action-btn reply" onclick="openReplyModal(<?= $message['id'] ?>, '<?= htmlspecialchars($message['email']) ?>', '<?= htmlspecialchars($message['subject']) ?>')">
                                    <i class="fas fa-reply"></i>
                                    Répondre
                                </button>
                                
                                <?php if ($message['is_read']): ?>
                                    <button class="action-btn mark-unread" onclick="markMessage(<?= $message['id'] ?>, 'mark_unread')">
                                        <i class="fas fa-envelope"></i>
                                        Non lu
                                    </button>
                                <?php else: ?>
                                    <button class="action-btn mark-read" onclick="markMessage(<?= $message['id'] ?>, 'mark_read')">
                                        <i class="fas fa-envelope-open"></i>
                                        Marquer lu
                                    </button>
                                <?php endif; ?>
                                
                                <button class="action-btn delete" onclick="deleteMessage(<?= $message['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&<?= http_build_query($_GET) ?>">&laquo; Précédent</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&<?= http_build_query($_GET) ?>">Suivant &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Modal de réponse -->
    <div class="modal" id="reply-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Répondre au message</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            
            <form method="POST" id="reply-form">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="reply_message">
                <input type="hidden" name="message_id" id="reply-message-id">
                
                <div class="form-group">
                    <label for="reply-to">À</label>
                    <input type="email" id="reply-to" readonly>
                </div>
                
                <div class="form-group">
                    <label for="reply-subject">Sujet</label>
                    <input type="text" id="reply-subject" name="reply_subject" required>
                </div>
                
                <div class="form-group">
                    <label for="reply-message">Message</label>
                    <textarea id="reply-message" name="reply_message" rows="8" required 
                              placeholder="Votre réponse..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer
                    </button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openReplyModal(messageId, email, subject) {
            document.getElementById('reply-message-id').value = messageId;
            document.getElementById('reply-to').value = email;
            document.getElementById('reply-subject').value = 'Re: ' + subject;
            document.getElementById('reply-modal').classList.add('active');
        }
        
        function markMessage(messageId, action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="action" value="${action}">
                <input type="hidden" name="message_id" value="${messageId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function deleteMessage(messageId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="delete_message">
                    <input type="hidden" name="message_id" value="${messageId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function toggleExpand(messageId) {
            const content = document.getElementById('content-' + messageId);
            const text = document.getElementById('expand-text-' + messageId);
            const icon = document.getElementById('expand-icon-' + messageId);
            
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                text.textContent = 'Voir plus';
                icon.className = 'fas fa-chevron-down';
            } else {
                content.classList.add('expanded');
                text.textContent = 'Voir moins';
                icon.className = 'fas fa-chevron-up';
            }
        }
        
        function closeModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }
        
        // Auto-hide flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
        
        // Fermer modal en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                closeModal();
            }
        });
    </script>
</body>
</html>