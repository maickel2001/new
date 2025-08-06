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
    <title>Politique de confidentialité - <?= htmlspecialchars($siteName) ?></title>
    <meta name="description" content="Politique de confidentialité de <?= htmlspecialchars($siteName) ?>. Découvrez comment nous protégeons et utilisons vos données personnelles.">
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
                    <i class="fas fa-shield-alt"></i>
                    Politique de confidentialité
                </h1>
                <p>Dernière mise à jour : <?= date('d/m/Y') ?></p>
            </div>

            <div class="legal-content">
                <div class="legal-nav">
                    <h3>Sommaire</h3>
                    <ul>
                        <li><a href="#introduction">1. Introduction</a></li>
                        <li><a href="#donnees">2. Données collectées</a></li>
                        <li><a href="#utilisation">3. Utilisation des données</a></li>
                        <li><a href="#partage">4. Partage des données</a></li>
                        <li><a href="#cookies">5. Cookies et technologies</a></li>
                        <li><a href="#securite">6. Sécurité des données</a></li>
                        <li><a href="#conservation">7. Conservation des données</a></li>
                        <li><a href="#droits">8. Vos droits</a></li>
                        <li><a href="#modifications">9. Modifications</a></li>
                        <li><a href="#contact">10. Contact</a></li>
                    </ul>
                </div>

                <div class="legal-text">
                    <section id="introduction">
                        <h2>1. Introduction</h2>
                        <p><?= htmlspecialchars($siteName) ?> s'engage à protéger la confidentialité de vos données personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos informations.</p>
                        <p>En utilisant notre service, vous acceptez les pratiques décrites dans cette politique de confidentialité.</p>
                    </section>

                    <section id="donnees">
                        <h2>2. Données collectées</h2>
                        <h3>2.1 Informations que vous nous fournissez</h3>
                        <ul>
                            <li><strong>Informations de compte :</strong> nom, prénom, nom d'utilisateur, adresse email, numéro de téléphone</li>
                            <li><strong>Informations de commande :</strong> liens de profils sociaux, détails de commande, préférences</li>
                            <li><strong>Informations de paiement :</strong> preuves de paiement Mobile Money (sans données bancaires sensibles)</li>
                            <li><strong>Communications :</strong> messages de contact, correspondances avec le support</li>
                        </ul>
                        
                        <h3>2.2 Informations collectées automatiquement</h3>
                        <ul>
                            <li><strong>Données techniques :</strong> adresse IP, type de navigateur, système d'exploitation</li>
                            <li><strong>Données d'utilisation :</strong> pages visitées, temps passé sur le site, actions effectuées</li>
                            <li><strong>Données de localisation :</strong> localisation approximative basée sur l'adresse IP</li>
                        </ul>
                    </section>

                    <section id="utilisation">
                        <h2>3. Utilisation des données</h2>
                        <p>Nous utilisons vos données personnelles pour :</p>
                        <h3>3.1 Fourniture du service</h3>
                        <ul>
                            <li>Traiter et exécuter vos commandes</li>
                            <li>Gérer votre compte utilisateur</li>
                            <li>Fournir un support client</li>
                            <li>Communiquer sur l'état de vos commandes</li>
                        </ul>
                        
                        <h3>3.2 Amélioration du service</h3>
                        <ul>
                            <li>Analyser l'utilisation du site pour améliorer nos services</li>
                            <li>Développer de nouvelles fonctionnalités</li>
                            <li>Personnaliser votre expérience utilisateur</li>
                        </ul>
                        
                        <h3>3.3 Communication et marketing</h3>
                        <ul>
                            <li>Envoyer des notifications importantes sur le service</li>
                            <li>Informer sur les nouveaux services (avec votre consentement)</li>
                            <li>Répondre à vos demandes de contact</li>
                        </ul>
                        
                        <h3>3.4 Sécurité et conformité</h3>
                        <ul>
                            <li>Détecter et prévenir les fraudes</li>
                            <li>Assurer la sécurité de la plateforme</li>
                            <li>Respecter nos obligations légales</li>
                        </ul>
                    </section>

                    <section id="partage">
                        <h2>4. Partage des données</h2>
                        <p>Nous ne vendons jamais vos données personnelles. Nous pouvons partager vos informations uniquement dans les cas suivants :</p>
                        
                        <h3>4.1 Prestataires de services</h3>
                        <p>Nous pouvons partager des données avec des prestataires tiers qui nous aident à fournir nos services (hébergement, support technique, etc.). Ces prestataires sont tenus de protéger vos données.</p>
                        
                        <h3>4.2 Obligations légales</h3>
                        <p>Nous pouvons divulguer vos informations si la loi l'exige ou pour protéger nos droits légaux.</p>
                        
                        <h3>4.3 Transfert d'activité</h3>
                        <p>En cas de fusion, acquisition ou vente d'actifs, vos données pourraient être transférées au nouveau propriétaire.</p>
                    </section>

                    <section id="cookies">
                        <h2>5. Cookies et technologies similaires</h2>
                        <h3>5.1 Types de cookies utilisés</h3>
                        <ul>
                            <li><strong>Cookies essentiels :</strong> nécessaires au fonctionnement du site</li>
                            <li><strong>Cookies de performance :</strong> pour analyser l'utilisation du site</li>
                            <li><strong>Cookies de fonctionnalité :</strong> pour mémoriser vos préférences</li>
                        </ul>
                        
                        <h3>5.2 Gestion des cookies</h3>
                        <p>Vous pouvez contrôler et supprimer les cookies via les paramètres de votre navigateur. Cependant, désactiver certains cookies peut affecter le fonctionnement du site.</p>
                    </section>

                    <section id="securite">
                        <h2>6. Sécurité des données</h2>
                        <p>Nous mettons en place des mesures de sécurité techniques et organisationnelles pour protéger vos données :</p>
                        
                        <h3>6.1 Mesures techniques</h3>
                        <ul>
                            <li>Chiffrement SSL/TLS pour toutes les communications</li>
                            <li>Hachage sécurisé des mots de passe (bcrypt)</li>
                            <li>Protection contre les attaques XSS et CSRF</li>
                            <li>Surveillance et détection des intrusions</li>
                        </ul>
                        
                        <h3>6.2 Mesures organisationnelles</h3>
                        <ul>
                            <li>Accès limité aux données selon le principe du moindre privilège</li>
                            <li>Formation du personnel sur la protection des données</li>
                            <li>Audits réguliers de sécurité</li>
                            <li>Politiques de sécurité strictes</li>
                        </ul>
                    </section>

                    <section id="conservation">
                        <h2>7. Conservation des données</h2>
                        <p>Nous conservons vos données personnelles aussi longtemps que nécessaire pour :</p>
                        <ul>
                            <li>Fournir nos services</li>
                            <li>Respecter nos obligations légales</li>
                            <li>Résoudre les litiges</li>
                            <li>Faire respecter nos accords</li>
                        </ul>
                        
                        <h3>7.1 Durées de conservation</h3>
                        <ul>
                            <li><strong>Données de compte :</strong> tant que le compte est actif + 3 ans après fermeture</li>
                            <li><strong>Données de commande :</strong> 5 ans pour les obligations comptables</li>
                            <li><strong>Données de communication :</strong> 3 ans après la dernière interaction</li>
                            <li><strong>Logs techniques :</strong> 12 mois maximum</li>
                        </ul>
                    </section>

                    <section id="droits">
                        <h2>8. Vos droits</h2>
                        <p>Conformément aux réglementations sur la protection des données, vous disposez des droits suivants :</p>
                        
                        <h3>8.1 Droit d'accès</h3>
                        <p>Vous pouvez demander une copie des données personnelles que nous détenons sur vous.</p>
                        
                        <h3>8.2 Droit de rectification</h3>
                        <p>Vous pouvez demander la correction de données inexactes ou incomplètes.</p>
                        
                        <h3>8.3 Droit à l'effacement</h3>
                        <p>Vous pouvez demander la suppression de vos données dans certaines circonstances.</p>
                        
                        <h3>8.4 Droit à la limitation</h3>
                        <p>Vous pouvez demander la limitation du traitement de vos données.</p>
                        
                        <h3>8.5 Droit à la portabilité</h3>
                        <p>Vous pouvez demander la transmission de vos données dans un format structuré.</p>
                        
                        <h3>8.6 Droit d'opposition</h3>
                        <p>Vous pouvez vous opposer au traitement de vos données à des fins de marketing.</p>
                        
                        <h3>8.7 Exercice de vos droits</h3>
                        <p>Pour exercer ces droits, contactez-nous à <a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a>. Nous répondrons dans un délai de 30 jours.</p>
                    </section>

                    <section id="modifications">
                        <h2>9. Modifications de cette politique</h2>
                        <p>Nous pouvons modifier cette politique de confidentialité à tout moment. Les modifications importantes vous seront notifiées par email ou via une notification sur le site.</p>
                        <p>Nous vous encourageons à consulter régulièrement cette page pour rester informé de nos pratiques de confidentialité.</p>
                    </section>

                    <section id="contact">
                        <h2>10. Contact</h2>
                        <p>Pour toute question concernant cette politique de confidentialité ou le traitement de vos données personnelles, vous pouvez nous contacter :</p>
                        <ul>
                            <li><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a></li>
                            <li><strong>Page de contact :</strong> <a href="/contact.php">Formulaire de contact</a></li>
                        </ul>
                        
                        <h3>10.1 Délégué à la protection des données</h3>
                        <p>Si vous avez des préoccupations concernant le traitement de vos données personnelles, vous pouvez également contacter notre délégué à la protection des données à l'adresse : <a href="mailto:dpo@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com">dpo@<?= strtolower(str_replace(' ', '', $siteName)) ?>.com</a></p>
                    </section>

                    <div class="legal-footer">
                        <p><strong>Dernière mise à jour :</strong> <?= date('d/m/Y') ?></p>
                        <p>Cette politique de confidentialité est effective à compter de cette date.</p>
                        
                        <div class="privacy-summary">
                            <h4>En résumé :</h4>
                            <ul>
                                <li>✅ Nous protégeons vos données avec des mesures de sécurité avancées</li>
                                <li>✅ Nous ne vendons jamais vos informations personnelles</li>
                                <li>✅ Vous gardez le contrôle sur vos données</li>
                                <li>✅ Nous sommes transparents sur nos pratiques</li>
                                <li>✅ Nous respectons vos droits à la vie privée</li>
                            </ul>
                        </div>
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