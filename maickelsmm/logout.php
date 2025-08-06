<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Déconnecter l'utilisateur
$auth->logout();

// Message de confirmation
setFlashMessage('success', 'Vous avez été déconnecté avec succès.');

// Redirection vers la page d'accueil
redirect('/');
?>