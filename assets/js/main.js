// Main JavaScript file for CREE 2GK

// DOM Elements
const userMenuBtn = document.getElementById('userMenuBtn');
const userMenu = document.getElementById('userMenu');
const searchInput = document.getElementById('searchInput');
const categoriesGrid = document.getElementById('categoriesGrid');
const productsGrid = document.getElementById('productsGrid');

// Categories data
const categories = [
    {
        name: 'Cartes Gaming',
        description: 'Steam, Epic Games, PlayStation, Xbox',
        icon: 'ri-gamepad-line',
        link: 'cartes-gaming.html',
        gradient: 'from-purple-500 to-blue-500',
        count: '250+ produits'
    },
    {
        name: 'Streaming & Divertissement',
        description: 'Netflix, Spotify, YouTube Premium',
        icon: 'ri-play-circle-line',
        link: 'streaming.html',
        gradient: 'from-red-500 to-pink-500',
        count: '50+ services'
    },
    {
        name: 'Logiciels & Outils',
        description: 'Windows, Office, Adobe, Antivirus',
        icon: 'ri-computer-line',
        link: 'logiciels.html',
        gradient: 'from-green-500 to-blue-500',
        count: '180+ licences'
    },
    {
        name: 'Cartes Prépayées',
        description: 'Amazon, iTunes, Google Play',
        icon: 'ri-gift-2-line',
        link: 'cartes-prepayees.html',
        gradient: 'from-orange-500 to-yellow-500',
        count: '100+ cartes'
    },
    {
        name: 'Cryptomonnaies',
        description: 'Bitcoin, Ethereum, codes crypto',
        icon: 'ri-bit-coin-line',
        link: 'crypto.html',
        gradient: 'from-yellow-500 to-orange-500',
        count: '20+ devises'
    },
    {
        name: 'VPN & Sécurité',
        description: 'NordVPN, ExpressVPN, antivirus',
        icon: 'ri-shield-check-line',
        link: 'vpn-securite.html',
        gradient: 'from-indigo-500 to-purple-500',
        count: '30+ solutions'
    }
];

// Products data
const products = {
    gaming: [
        {
            id: 1,
            name: 'Carte Steam 50€',
            price: '50.00',
            originalPrice: '52.00',
            image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+50€',
            badge: 'PROMO',
            badgeColor: 'bg-red-500',
            delivery: 'Instantané',
            rating: 4.9,
            stock: 'En stock'
        },
        {
            id: 2,
            name: 'PlayStation Plus 12 mois',
            price: '59.99',
            originalPrice: '69.99',
            image: 'https://via.placeholder.com/300x200/003087/ffffff?text=PS+Plus+12m',
            badge: 'POPULAIRE',
            badgeColor: 'bg-blue-500',
            delivery: '5-10 min',
            rating: 4.8,
            stock: 'En stock'
        },
        {
            id: 3,
            name: 'Xbox Game Pass Ultimate 3 mois',
            price: '29.99',
            originalPrice: '34.99',
            image: 'https://via.placeholder.com/300x200/107c10/ffffff?text=Xbox+GPU+3m',
            badge: 'NOUVEAU',
            badgeColor: 'bg-green-500',
            delivery: 'Instantané',
            rating: 4.7,
            stock: 'Stock limité'
        },
        {
            id: 4,
            name: 'Riot Points 10€ (League of Legends)',
            price: '10.00',
            originalPrice: '10.00',
            image: 'https://via.placeholder.com/300x200/c89b3c/ffffff?text=Riot+Points+10€',
            badge: null,
            badgeColor: '',
            delivery: 'Instantané',
            rating: 4.9,
            stock: 'En stock'
        }
    ],
    streaming: [
        {
            id: 5,
            name: 'Netflix Premium 6 mois',
            price: '89.99',
            originalPrice: '95.94',
            image: 'https://via.placeholder.com/300x200/e50914/ffffff?text=Netflix+6m',
            badge: 'ÉCONOMIE',
            badgeColor: 'bg-green-500',
            delivery: '2-5 min',
            rating: 4.8,
            stock: 'En stock'
        },
        {
            id: 6,
            name: 'Spotify Premium 12 mois',
            price: '99.99',
            originalPrice: '119.88',
            image: 'https://via.placeholder.com/300x200/1db954/ffffff?text=Spotify+12m',
            badge: 'BEST SELLER',
            badgeColor: 'bg-purple-500',
            delivery: 'Manuel',
            rating: 4.7,
            stock: 'En stock'
        },
        {
            id: 7,
            name: 'YouTube Premium 3 mois',
            price: '32.99',
            originalPrice: '35.97',
            image: 'https://via.placeholder.com/300x200/ff0000/ffffff?text=YouTube+3m',
            badge: 'PROMO',
            badgeColor: 'bg-red-500',
            delivery: '1-2h',
            rating: 4.6,
            stock: 'En stock'
        },
        {
            id: 8,
            name: 'Disney+ 12 mois',
            price: '89.90',
            originalPrice: '89.90',
            image: 'https://via.placeholder.com/300x200/113ccf/ffffff?text=Disney%2B+12m',
            badge: null,
            badgeColor: '',
            delivery: 'Manuel',
            rating: 4.5,
            stock: 'En stock'
        }
    ],
    software: [
        {
            id: 9,
            name: 'Microsoft Office 2021 Pro',
            price: '49.99',
            originalPrice: '439.99',
            image: 'https://via.placeholder.com/300x200/0078d4/ffffff?text=Office+2021',
            badge: 'MEGA PROMO',
            badgeColor: 'bg-red-600',
            delivery: 'Instantané',
            rating: 4.9,
            stock: 'En stock'
        },
        {
            id: 10,
            name: 'Windows 11 Pro',
            price: '19.99',
            originalPrice: '259.99',
            image: 'https://via.placeholder.com/300x200/0078d4/ffffff?text=Windows+11',
            badge: 'TOP VENTE',
            badgeColor: 'bg-blue-500',
            delivery: 'Instantané',
            rating: 4.8,
            stock: 'En stock'
        },
        {
            id: 11,
            name: 'Adobe Creative Cloud 1 an',
            price: '299.99',
            originalPrice: '659.88',
            image: 'https://via.placeholder.com/300x200/ff0000/ffffff?text=Adobe+CC',
            badge: 'ÉCONOMIE',
            badgeColor: 'bg-green-500',
            delivery: '10-30 min',
            rating: 4.7,
            stock: 'Stock limité'
        },
        {
            id: 12,
            name: 'Kaspersky Total Security',
            price: '24.99',
            originalPrice: '49.99',
            image: 'https://via.placeholder.com/300x200/006f3c/ffffff?text=Kaspersky',
            badge: 'SÉCURITÉ',
            badgeColor: 'bg-green-600',
            delivery: 'Instantané',
            rating: 4.6,
            stock: 'En stock'
        }
    ]
};

// Current active tab
let activeTab = 'gaming';
let cartItems = [];
let favorites = [];

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    loadCategories();
    loadProducts(activeTab);
    updateCartCount();
});

// Event listeners
function initializeEventListeners() {
    // User menu toggle
    if (userMenuBtn && userMenu) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function() {
            userMenu.classList.add('hidden');
        });

        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            performSearch(query);
        });

        searchInput.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-105');
        });

        searchInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-105');
        });
    }

    // Tab buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tab-btn') || e.target.closest('.tab-btn')) {
            const tabBtn = e.target.classList.contains('tab-btn') ? e.target : e.target.closest('.tab-btn');
            const tab = tabBtn.getAttribute('data-tab');
            
            if (tab && tab !== activeTab) {
                // Update active tab
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                tabBtn.classList.add('active');
                
                activeTab = tab;
                loadProducts(activeTab);
            }
        }
    });
}

// Load categories
function loadCategories() {
    if (!categoriesGrid) return;

    const categoriesHTML = categories.map(category => `
        <a href="${category.link}" class="group bg-gray-900 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 bg-gradient-to-r ${category.gradient} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="${category.icon} text-white text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400 bg-gray-800 px-3 py-1 rounded-full">
                        ${category.count}
                    </span>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                        ${category.name}
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        ${category.description}
                    </p>
                </div>
                
                <div class="flex items-center text-blue-400 text-sm font-medium">
                    <span>Découvrir</span>
                    <i class="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
    `).join('');

    categoriesGrid.innerHTML = categoriesHTML;
}

// Load products
function loadProducts(tab) {
    if (!productsGrid || !products[tab]) return;

    const productsHTML = products[tab].map(product => `
        <div class="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
            <div class="relative">
                <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded-t-xl">
                ${product.badge ? `
                    <span class="absolute top-3 left-3 ${product.badgeColor} text-white px-2 py-1 rounded-full text-xs font-bold">
                        ${product.badge}
                    </span>
                ` : ''}
                <div class="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                    <i class="ri-flashlight-line mr-1"></i>
                    ${product.delivery}
                </div>
            </div>

            <div class="p-6">
                <h3 class="font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    ${product.name}
                </h3>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-white">€${product.price}</span>
                        ${product.originalPrice !== product.price ? `
                            <span class="text-sm text-gray-400 line-through">€${product.originalPrice}</span>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-star-fill text-yellow-500 text-sm"></i>
                        <span class="text-sm text-gray-300">${product.rating}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm px-2 py-1 rounded ${product.stock === 'En stock' ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/20 text-orange-400'}">
                        ${product.stock}
                    </span>
                </div>

                <div class="space-y-2">
                    <button class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform group-hover:scale-105 whitespace-nowrap" onclick="addToCart(${product.id})">
                        <i class="ri-shopping-cart-line mr-2"></i>
                        Acheter maintenant
                    </button>
                    <button class="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg font-medium transition-colors whitespace-nowrap" onclick="toggleFavorite(${product.id})">
                        <i class="ri-heart-line mr-2"></i>
                        Ajouter aux favoris
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    productsGrid.innerHTML = productsHTML;
}

// Cart functionality
function addToCart(productId) {
    // Find product in all categories
    let product = null;
    for (const category in products) {
        product = products[category].find(p => p.id === productId);
        if (product) break;
    }

    if (product) {
        // Check if product already in cart
        const existingItem = cartItems.find(item => item.id === productId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cartItems.push({
                ...product,
                quantity: 1
            });
        }

        updateCartCount();
        showNotification('Produit ajouté au panier !', 'success');
        
        // Save to localStorage
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
    }
}

// Favorites functionality
function toggleFavorite(productId) {
    const index = favorites.indexOf(productId);
    if (index > -1) {
        favorites.splice(index, 1);
        showNotification('Retiré des favoris', 'info');
    } else {
        favorites.push(productId);
        showNotification('Ajouté aux favoris !', 'success');
    }
    
    // Save to localStorage
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

// Update cart count
function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
    
    switch (type) {
        case 'success':
            notification.classList.add('bg-green-500');
            break;
        case 'error':
            notification.classList.add('bg-red-500');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500');
            break;
        default:
            notification.classList.add('bg-blue-500');
    }

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="ri-check-line mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Search functionality
function performSearch(query) {
    if (!query) {
        loadProducts(activeTab);
        return;
    }

    // Simple search implementation
    const allProducts = Object.values(products).flat();
    const results = allProducts.filter(product => 
        product.name.toLowerCase().includes(query.toLowerCase())
    );

    if (results.length > 0) {
        displaySearchResults(results);
    } else {
        showNotification('Aucun produit trouvé', 'warning');
        loadProducts(activeTab);
    }
}

// Display search results
function displaySearchResults(results) {
    if (!productsGrid) return;

    const resultsHTML = results.map(product => `
        <div class="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
            <div class="relative">
                <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded-t-xl">
                ${product.badge ? `
                    <span class="absolute top-3 left-3 ${product.badgeColor} text-white px-2 py-1 rounded-full text-xs font-bold">
                        ${product.badge}
                    </span>
                ` : ''}
                <div class="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                    <i class="ri-flashlight-line mr-1"></i>
                    ${product.delivery}
                </div>
            </div>

            <div class="p-6">
                <h3 class="font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    ${product.name}
                </h3>
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-white">€${product.price}</span>
                        ${product.originalPrice !== product.price ? `
                            <span class="text-sm text-gray-400 line-through">€${product.originalPrice}</span>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-star-fill text-yellow-500 text-sm"></i>
                        <span class="text-sm text-gray-300">${product.rating}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <button class="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform group-hover:scale-105 whitespace-nowrap" onclick="addToCart(${product.id})">
                        <i class="ri-shopping-cart-line mr-2"></i>
                        Acheter maintenant
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    productsGrid.innerHTML = resultsHTML;
}

// Load data from localStorage on page load
function loadStoredData() {
    const storedCart = localStorage.getItem('cartItems');
    const storedFavorites = localStorage.getItem('favorites');
    
    if (storedCart) {
        cartItems = JSON.parse(storedCart);
        updateCartCount();
    }
    
    if (storedFavorites) {
        favorites = JSON.parse(storedFavorites);
    }
}

// Initialize stored data
document.addEventListener('DOMContentLoaded', function() {
    loadStoredData();
});

// Smooth scrolling for anchor links
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' && e.target.getAttribute('href') && e.target.getAttribute('href').startsWith('#')) {
        e.preventDefault();
        const target = document.querySelector(e.target.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

// Utility functions
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

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

// Export functions for use in other files
window.CREE2GK = {
    addToCart,
    toggleFavorite,
    showNotification,
    performSearch,
    formatPrice,
    cartItems,
    favorites
};