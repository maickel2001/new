/**
 * MaickelSMM - JavaScript principal
 * Fonctionnalités interactives et logique client
 * 
 * @author MaickelSMM Team
 * @version 1.0
 */

// Variables globales
let currentServices = [];
let filteredServices = [];
let currentCategory = 'all';

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialisation principale de l'application
 */
function initializeApp() {
    // Initialiser la navigation mobile
    initMobileMenu();
    
    // Initialiser les filtres de services
    initServiceFilters();
    
    // Initialiser les modales
    initModals();
    
    // Initialiser les formulaires
    initForms();
    
    // Initialiser les messages flash
    initFlashMessages();
    
    // Initialiser les animations
    initAnimations();
    
    // Charger les services
    loadServices();
    
    // Initialiser les calculateurs de prix
    initPriceCalculators();
}

/**
 * Navigation mobile
 */
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            
            // Changer l'icône
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
        
        // Fermer le menu lors du clic sur un lien
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                }
            });
        });
    }
}

/**
 * Filtres de services
 */
function initServiceFilters() {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const searchInput = document.querySelector('#service-search');
    
    // Filtres par catégorie
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour l'état actif
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrer les services
            currentCategory = this.dataset.category;
            filterServices();
        });
    });
    
    // Recherche de services
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            filterServices(this.value);
        }, 300));
    }
}

/**
 * Filtrer les services
 */
function filterServices(searchTerm = '') {
    let filtered = currentServices;
    
    // Filtrer par catégorie
    if (currentCategory !== 'all') {
        filtered = filtered.filter(service => service.category_id == currentCategory);
    }
    
    // Filtrer par recherche
    if (searchTerm.trim()) {
        const term = searchTerm.toLowerCase();
        filtered = filtered.filter(service => 
            service.name.toLowerCase().includes(term) ||
            service.description.toLowerCase().includes(term) ||
            service.category_name.toLowerCase().includes(term)
        );
    }
    
    filteredServices = filtered;
    renderServices(filtered);
}

/**
 * Charger les services via AJAX
 */
async function loadServices() {
    try {
        showLoading('.services-grid');
        
        const response = await fetch('/api/services.php');
        const data = await response.json();
        
        if (data.success) {
            currentServices = data.services;
            filteredServices = data.services;
            renderServices(data.services);
            renderCategories(data.categories);
        } else {
            showError('Erreur lors du chargement des services');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur de connexion');
    } finally {
        hideLoading('.services-grid');
    }
}

/**
 * Afficher les services
 */
function renderServices(services) {
    const servicesGrid = document.querySelector('.services-grid');
    if (!servicesGrid) return;
    
    if (services.length === 0) {
        servicesGrid.innerHTML = `
            <div class="col-span-full text-center py-8">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Aucun service trouvé</p>
            </div>
        `;
        return;
    }
    
    servicesGrid.innerHTML = services.map(service => `
        <div class="card service-card slide-up" data-service-id="${service.id}">
            <div class="service-icon">
                <i class="${service.category_icon}"></i>
            </div>
            <h3 class="service-name">${service.name}</h3>
            <p class="service-description">${truncateText(service.description, 100)}</p>
            <div class="service-price">${formatPrice(service.price_per_1000)} / 1000</div>
            <div class="service-details">
                <span>Min: ${service.min_quantity}</span>
                <span>Max: ${service.max_quantity}</span>
                <span>Délai: ${service.delivery_time}</span>
            </div>
            ${service.guarantee === 'yes' ? '<div class="service-guarantee"><i class="fas fa-shield-alt"></i> Garantie</div>' : ''}
            <div class="card-footer">
                <button class="btn btn-primary btn-sm" onclick="openOrderModal(${service.id})">
                    <i class="fas fa-shopping-cart"></i> Commander
                </button>
            </div>
        </div>
    `).join('');
    
    // Animer l'apparition des cartes
    animateCards();
}

/**
 * Afficher les catégories
 */
function renderCategories(categories) {
    const categoriesNav = document.querySelector('.categories-nav');
    if (!categoriesNav) return;
    
    const allButton = `<button class="category-btn active" data-category="all">Tous les services</button>`;
    const categoryButtons = categories.map(category => `
        <button class="category-btn" data-category="${category.id}">
            <i class="${category.icon}"></i> ${category.name}
        </button>
    `).join('');
    
    categoriesNav.innerHTML = allButton + categoryButtons;
    
    // Réinitialiser les événements
    initServiceFilters();
}

/**
 * Ouvrir la modale de commande
 */
function openOrderModal(serviceId) {
    const service = currentServices.find(s => s.id == serviceId);
    if (!service) return;
    
    const modal = document.querySelector('#order-modal');
    if (!modal) {
        createOrderModal(service);
        return;
    }
    
    populateOrderModal(service);
    showModal('order-modal');
}

/**
 * Créer la modale de commande
 */
function createOrderModal(service) {
    const modalHtml = `
        <div class="modal" id="order-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Commander un service</h2>
                    <button class="modal-close" onclick="closeModal('order-modal')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="order-form">
                        <input type="hidden" id="service-id" name="service_id">
                        
                        <div class="service-info mb-4">
                            <h3 id="modal-service-name"></h3>
                            <p id="modal-service-description"></p>
                            <div class="service-price" id="modal-service-price"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="quantity">Quantité *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                            <div class="form-error" id="quantity-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="link">Lien du profil/publication *</label>
                            <input type="url" class="form-control" id="link" name="link" required 
                                   placeholder="https://instagram.com/username">
                            <div class="form-error" id="link-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="payment-method">Méthode de paiement *</label>
                            <select class="form-control form-select" id="payment-method" name="payment_method" required>
                                <option value="">Choisir une méthode</option>
                            </select>
                            <div class="form-error" id="payment-method-error"></div>
                        </div>
                        
                        <div id="guest-info" class="guest-section">
                            <h4>Informations de contact</h4>
                            <div class="form-group">
                                <label class="form-label" for="guest-name">Nom complet *</label>
                                <input type="text" class="form-control" id="guest-name" name="guest_name">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="guest-email">Email *</label>
                                <input type="email" class="form-control" id="guest-email" name="guest_email">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="guest-phone">Téléphone *</label>
                                <input type="tel" class="form-control" id="guest-phone" name="guest_phone">
                            </div>
                        </div>
                        
                        <div class="order-summary">
                            <div class="d-flex justify-between items-center">
                                <span>Total à payer:</span>
                                <span class="service-price" id="total-price">0 FCFA</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg w-full">
                                <i class="fas fa-shopping-cart"></i> Passer la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    populateOrderModal(service);
    initOrderForm();
    showModal('order-modal');
}

/**
 * Remplir la modale de commande
 */
function populateOrderModal(service) {
    document.getElementById('service-id').value = service.id;
    document.getElementById('modal-service-name').textContent = service.name;
    document.getElementById('modal-service-description').textContent = service.description;
    document.getElementById('modal-service-price').textContent = `${formatPrice(service.price_per_1000)} / 1000`;
    
    const quantityInput = document.getElementById('quantity');
    quantityInput.min = service.min_quantity;
    quantityInput.max = service.max_quantity;
    quantityInput.placeholder = `Min: ${service.min_quantity}, Max: ${service.max_quantity}`;
    
    // Charger les méthodes de paiement
    loadPaymentMethods();
}

/**
 * Charger les méthodes de paiement
 */
async function loadPaymentMethods() {
    try {
        const response = await fetch('/api/payment-methods.php');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('payment-method');
            select.innerHTML = '<option value="">Choisir une méthode</option>';
            
            Object.entries(data.methods).forEach(([key, value]) => {
                select.innerHTML += `<option value="${key}">${key.toUpperCase()}: ${value}</option>`;
            });
        }
    } catch (error) {
        console.error('Erreur chargement méthodes paiement:', error);
    }
}

/**
 * Initialiser le formulaire de commande
 */
function initOrderForm() {
    const form = document.getElementById('order-form');
    const quantityInput = document.getElementById('quantity');
    const totalPriceElement = document.getElementById('total-price');
    
    // Calculer le prix en temps réel
    quantityInput.addEventListener('input', function() {
        const serviceId = document.getElementById('service-id').value;
        const service = currentServices.find(s => s.id == serviceId);
        const quantity = parseInt(this.value) || 0;
        
        if (service && quantity > 0) {
            const total = (service.price_per_1000 / 1000) * quantity;
            totalPriceElement.textContent = formatPrice(total);
        } else {
            totalPriceElement.textContent = '0 FCFA';
        }
    });
    
    // Soumettre le formulaire
    form.addEventListener('submit', handleOrderSubmit);
}

/**
 * Gérer la soumission de commande
 */
async function handleOrderSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitButton = e.target.querySelector('button[type="submit"]');
    
    try {
        // Désactiver le bouton
        submitButton.disabled = true;
        submitButton.innerHTML = '<div class="loading"></div> Traitement...';
        
        // Effacer les erreurs précédentes
        clearFormErrors();
        
        const response = await fetch('/api/create-order.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal('order-modal');
            showSuccess('Commande créée avec succès! Vous allez être redirigé vers la page de paiement.');
            
            // Rediriger vers la page de paiement
            setTimeout(() => {
                window.location.href = `/order/${data.order_id}`;
            }, 2000);
        } else {
            if (data.errors) {
                showFormErrors(data.errors);
            } else {
                showError(data.message || 'Erreur lors de la création de la commande');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur de connexion');
    } finally {
        // Réactiver le bouton
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-shopping-cart"></i> Passer la commande';
    }
}

/**
 * Calculateurs de prix
 */
function initPriceCalculators() {
    const calculators = document.querySelectorAll('.price-calculator');
    
    calculators.forEach(calculator => {
        const serviceSelect = calculator.querySelector('.service-select');
        const quantityInput = calculator.querySelector('.quantity-input');
        const priceDisplay = calculator.querySelector('.price-display');
        
        function updatePrice() {
            const serviceId = serviceSelect.value;
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (serviceId && quantity > 0) {
                const service = currentServices.find(s => s.id == serviceId);
                if (service) {
                    const total = (service.price_per_1000 / 1000) * quantity;
                    priceDisplay.textContent = formatPrice(total);
                }
            } else {
                priceDisplay.textContent = '0 FCFA';
            }
        }
        
        serviceSelect.addEventListener('change', updatePrice);
        quantityInput.addEventListener('input', updatePrice);
    });
}

/**
 * Modales
 */
function initModals() {
    // Fermer les modales en cliquant sur l'arrière-plan
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            closeModal(e.target.id);
        }
    });
    
    // Fermer les modales avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                closeModal(activeModal.id);
            }
        }
    });
}

/**
 * Afficher une modale
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Fermer une modale
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Formulaires
 */
function initForms() {
    // Validation en temps réel
    const forms = document.querySelectorAll('form[data-validate="true"]');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

/**
 * Valider un champ
 */
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    let isValid = true;
    let errorMessage = '';
    
    // Champ requis
    if (required && !value) {
        isValid = false;
        errorMessage = 'Ce champ est requis';
    }
    
    // Validation par type
    if (value && !isValid) {
        switch (type) {
            case 'email':
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Email invalide';
                }
                break;
            case 'url':
                if (!/^https?:\/\/.+/.test(value)) {
                    isValid = false;
                    errorMessage = 'URL invalide';
                }
                break;
            case 'number':
                const min = field.getAttribute('min');
                const max = field.getAttribute('max');
                const numValue = parseInt(value);
                
                if (min && numValue < parseInt(min)) {
                    isValid = false;
                    errorMessage = `Minimum: ${min}`;
                }
                if (max && numValue > parseInt(max)) {
                    isValid = false;
                    errorMessage = `Maximum: ${max}`;
                }
                break;
        }
    }
    
    // Afficher l'erreur
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

/**
 * Afficher une erreur de champ
 */
function showFieldError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.parentNode.querySelector('.form-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

/**
 * Effacer l'erreur d'un champ
 */
function clearFieldError(field) {
    field.classList.remove('error');
    const errorElement = field.parentNode.querySelector('.form-error');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

/**
 * Afficher les erreurs de formulaire
 */
function showFormErrors(errors) {
    errors.forEach(error => {
        if (typeof error === 'object' && error.field) {
            const field = document.querySelector(`[name="${error.field}"]`);
            if (field) {
                showFieldError(field, error.message);
            }
        } else {
            showError(error);
        }
    });
}

/**
 * Effacer toutes les erreurs de formulaire
 */
function clearFormErrors() {
    const errorElements = document.querySelectorAll('.form-error');
    errorElements.forEach(element => {
        element.textContent = '';
    });
    
    const errorFields = document.querySelectorAll('.form-control.error');
    errorFields.forEach(field => {
        field.classList.remove('error');
    });
}

/**
 * Messages flash
 */
function initFlashMessages() {
    // Auto-fermeture des messages flash
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
        
        // Fermeture manuelle
        const closeBtn = message.querySelector('.flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                message.style.opacity = '0';
                setTimeout(() => {
                    message.remove();
                }, 300);
            });
        }
    });
}

/**
 * Afficher un message de succès
 */
function showSuccess(message) {
    showFlashMessage(message, 'success');
}

/**
 * Afficher un message d'erreur
 */
function showError(message) {
    showFlashMessage(message, 'error');
}

/**
 * Afficher un message d'avertissement
 */
function showWarning(message) {
    showFlashMessage(message, 'warning');
}

/**
 * Afficher un message d'information
 */
function showInfo(message) {
    showFlashMessage(message, 'info');
}

/**
 * Afficher un message flash
 */
function showFlashMessage(message, type) {
    const container = document.querySelector('.flash-messages') || createFlashContainer();
    
    const messageElement = document.createElement('div');
    messageElement.className = `flash-message flash-${type}`;
    messageElement.innerHTML = `
        <i class="fas fa-${getFlashIcon(type)}"></i>
        <span>${message}</span>
        <button class="flash-close" style="background: none; border: none; color: inherit; margin-left: auto;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(messageElement);
    
    // Auto-fermeture
    setTimeout(() => {
        messageElement.style.opacity = '0';
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    }, 5000);
    
    // Fermeture manuelle
    const closeBtn = messageElement.querySelector('.flash-close');
    closeBtn.addEventListener('click', () => {
        messageElement.style.opacity = '0';
        setTimeout(() => {
            messageElement.remove();
        }, 300);
    });
}

/**
 * Créer le conteneur des messages flash
 */
function createFlashContainer() {
    const container = document.createElement('div');
    container.className = 'flash-messages';
    document.body.appendChild(container);
    return container;
}

/**
 * Obtenir l'icône pour un type de message
 */
function getFlashIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Animations
 */
function initAnimations() {
    // Observer pour les animations au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    // Observer les éléments à animer
    const animatedElements = document.querySelectorAll('.slide-up, .fade-in-scroll');
    animatedElements.forEach(el => observer.observe(el));
}

/**
 * Animer les cartes
 */
function animateCards() {
    const cards = document.querySelectorAll('.service-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });
}

/**
 * Utilitaires
 */

/**
 * Debounce pour limiter les appels de fonction
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Formater un prix
 */
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price) + ' FCFA';
}

/**
 * Tronquer un texte
 */
function truncateText(text, length) {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

/**
 * Afficher un indicateur de chargement
 */
function showLoading(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.innerHTML = `
            <div class="text-center py-8">
                <div class="loading" style="width: 40px; height: 40px; margin: 0 auto;"></div>
                <p class="mt-4 text-gray-500">Chargement...</p>
            </div>
        `;
    }
}

/**
 * Masquer l'indicateur de chargement
 */
function hideLoading(selector) {
    // Le contenu sera remplacé par les données chargées
}

/**
 * Copier du texte dans le presse-papiers
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showSuccess('Copié dans le presse-papiers');
    } catch (err) {
        console.error('Erreur copie:', err);
        showError('Erreur lors de la copie');
    }
}

/**
 * Valider une URL
 */
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

/**
 * Obtenir les paramètres de l'URL
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params) {
        result[key] = value;
    }
    return result;
}

/**
 * Smooth scroll vers un élément
 */
function scrollToElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Export pour utilisation globale
window.MaickelSMM = {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showModal,
    closeModal,
    formatPrice,
    copyToClipboard,
    scrollToElement
};