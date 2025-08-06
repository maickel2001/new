<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$settings = getSettings();
$siteName = $settings['site_name'] ?? 'MaickelSMM';
$contactEmail = $settings['contact_email'] ?? 'contact@maickelsmm.com';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions d'utilisation - <?= htmlspecialchars($siteName) ?></title>
    <meta name="description" content="Conditions d'utilisation du service <?= htmlspecialchars($siteName) ?>. Consultez nos termes et conditions avant d'utiliser nos services SMM.">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-rocket"></i>
                <span><?= htmlspecialchars($siteName) ?></span>
            </div>
            
            <nav class="nav-menu">
                <a href="/" class="nav-link">Accueil</a>
                <a href="/#services" class="nav-link">Services</a>
                <a href="/contact.php" class="nav-link">Contact</a>
                <?php if ($auth->isLoggedIn()): ?>
                    <a href="/dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de bord
                    </a>
                <?php else: ?>
                    <a href="/login.php" class="nav-link">Connexion</a>
                    <a href="/register.php" class="btn btn-primary btn-sm">S'inscrire</a>
                <?php endif; ?>
            </nav>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Legal Content -->
    <section class="legal-section">
        <div class="container">
            <div class="legal-header">
                <h1>
                    <i class="fas fa-file-contract"></i>
                    Conditions d'utilisation
                </h1>
                <p>Dernière mise à jour : <?= date('d/m/Y') ?></p>
            </div>

            <div class="legal-content">
                <div class="legal-nav">
                    <h3>Sommaire</h3>
                    <ul>
                        <li><a href="#acceptation">1. Acceptation des conditions</a></li>
                        <li><a href="#services">2. Description des services</a></li>
                        <li><a href="#compte">3. Compte utilisateur</a></li>
                        <li><a href="#commandes">4. Commandes et paiements</a></li>
                        <li><a href="#utilisation">5. Utilisation acceptable</a></li>
                        <li><a href="#propriete">6. Propriété intellectuelle</a></li>
                        <li><a href="#responsabilite">7. Limitation de responsabilité</a></li>
                        <li><a href="#confidentialite">8. Confidentialité</a></li>
                        <li><a href="#modifications">9. Modifications</a></li>
                        <li><a href="#contact">10. Contact</a></li>
                    </ul>
                </div>

                <div class="legal-text">
                    <section id="acceptation">
                        <h2>1. Acceptation des conditions</h2>
                        <p>En accédant et en utilisant les services de <?= htmlspecialchars($siteName) ?> (ci-après "le Service"), vous acceptez d'être lié par ces conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.</p>
                        <p>Ces conditions constituent un accord légalement contraignant entre vous et <?= htmlspecialchars($siteName) ?>.</p>
                    </section>

                    <section id="services">
                        <h2>2. Description des services</h2>
                        <p><?= htmlspecialchars($siteName) ?> fournit des services de marketing sur les réseaux sociaux (SMM), incluant mais non limités à :</p>
                        <ul>
                            <li>Augmentation de followers, likes, vues et commentaires</li>
                            <li>Services pour diverses plateformes (Instagram, TikTok, YouTube, Facebook, etc.)</li>
                            <li>Services de marketing digital connexes</li>
                        </ul>
                        <p>Tous les services sont fournis "en l'état" et nous nous réservons le droit de modifier, suspendre ou interrompre tout service à tout moment.</p>
                    </section>

                    <section id="compte">
                        <h2>3. Compte utilisateur</h2>
                        <h3>3.1 Création de compte</h3>
                        <p>Pour utiliser certains services, vous devez créer un compte en fournissant des informations exactes et complètes.</p>
                        
                        <h3>3.2 Sécurité du compte</h3>
                        <p>Vous êtes responsable de maintenir la confidentialité de vos identifiants de connexion et de toutes les activités qui se produisent sous votre compte.</p>
                        
                        <h3>3.3 Suspension de compte</h3>
                        <p>Nous nous réservons le droit de suspendre ou de fermer votre compte en cas de violation de ces conditions.</p>
                    </section>

                    <section id="commandes">
                        <h2>4. Commandes et paiements</h2>
                        <h3>4.1 Processus de commande</h3>
                        <p>Les commandes sont traitées après confirmation du paiement et fourniture des informations nécessaires (liens de profil, etc.).</p>
                        
                        <h3>4.2 Paiements</h3>
                        <p>Les paiements s'effectuent via Mobile Money (MTN, Moov, Orange). Une preuve de paiement doit être fournie pour traiter la commande.</p>
                        
                        <h3>4.3 Remboursements</h3>
                        <p>Les remboursements sont accordés uniquement en cas de non-livraison du service dans les délais convenus, sous réserve d'évaluation au cas par cas.</p>
                        
                        <h3>4.4 Délais de livraison</h3>
                        <p>Les délais indiqués sont estimatifs. Nous nous efforçons de respecter ces délais mais ne garantissons pas la livraison à une date précise.</p>
                    </section>

                    <section id="utilisation">
                        <h2>5. Utilisation acceptable</h2>
                        <h3>5.1 Restrictions</h3>
                        <p>Vous vous engagez à ne pas utiliser nos services pour :</p>
                        <ul>
                            <li>Des activités illégales ou non autorisées</li>
                            <li>Violer les conditions d'utilisation des plateformes tierces</li>
                            <li>Diffuser du contenu offensant, discriminatoire ou nuisible</li>
                            <li>Tenter de contourner les mesures de sécurité du service</li>
                        </ul>
                        
                        <h3>5.2 Conformité aux plateformes</h3>
                        <p>Vous devez respecter les conditions d'utilisation des plateformes sur lesquelles nos services sont appliqués (Instagram, TikTok, etc.).</p>
                    </section>

                    <section id="propriete">
                        <h2>6. Propriété intellectuelle</h2>
                        <p>Tous les contenus, marques, logos et éléments du site <?= htmlspecialchars($siteName) ?> sont protégés par les droits de propriété intellectuelle.</p>
                        <p>Vous ne pouvez pas reproduire, distribuer ou modifier ces éléments sans autorisation écrite préalable.</p>
                    </section>

                    <section id="responsabilite">
                        <h2>7. Limitation de responsabilité</h2>
                        <h3>7.1 Exclusions</h3>
                        <p><?= htmlspecialchars($siteName) ?> ne peut être tenu responsable de :</p>
                        <ul>
                            <li>Dommages indirects ou consécutifs</li>
                            <li>Perte de profits ou d'opportunités commerciales</li>
                            <li>Actions des plateformes tierces (suspensions, suppressions de contenu)</li>
                            <li>Interruptions de service dues à des causes externes</li>
                        </ul>
                        
                        <h3>7.2 Limitation</h3>
                        <p>Notre responsabilité totale ne peut excéder le montant payé pour le service concerné.</p>
                    </section>

                    <section id="confidentialite">
                        <h2>8. Confidentialité</h2>
                        <p>La collecte et l'utilisation de vos données personnelles sont régies par notre <a href="/privacy.php">Politique de confidentialité</a>.</p>
                        <p>Nous nous engageons à protéger vos informations personnelles conformément aux réglementations en vigueur.</p>
                    </section>

                    <section id="modifications">
                        <h2>9. Modifications des conditions</h2>
                        <p>Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications prennent effet dès leur publication sur le site.</p>
                        <p>Il est de votre responsabilité de consulter régulièrement ces conditions pour rester informé des éventuelles modifications.</p>
                    </section>

                    <section id="contact">
                        <h2>10. Contact</h2>
                        <p>Pour toute question concernant ces conditions d'utilisation, vous pouvez nous contacter :</p>
                        <ul>
                            <li><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a></li>
                            <li><strong>Page de contact :</strong> <a href="/contact.php">Formulaire de contact</a></li>
                        </ul>
                    </section>

                    <div class="legal-footer">
                        <p><strong>Dernière mise à jour :</strong> <?= date('d/m/Y') ?></p>
                        <p>Ces conditions d'utilisation sont effectives à compter de cette date.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-rocket"></i>
                        <span><?= htmlspecialchars($siteName) ?></span>
                    </div>
                    <p>Votre partenaire de confiance pour tous vos besoins en marketing sur les réseaux sociaux.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/#services">Services</a></li>
                        <li><a href="/contact.php">Contact</a></li>
                        <li><a href="/terms.php">Conditions</a></li>
                        <li><a href="/privacy.php">Confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="/contact.php">Centre d'aide</a></li>
                        <li><a href="mailto:<?= htmlspecialchars($contactEmail) ?>">Email</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-toggle')?.addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Highlight current section in navigation
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.legal-nav a');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollY >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>