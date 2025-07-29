// Gaming products page JavaScript

let currentFilters = {
    platform: '',
    priceRange: '',
    sortBy: 'popular'
};

let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
let allProducts = [];

// Initialize gaming page
document.addEventListener('DOMContentLoaded', function() {
    initializeGamingPage();
    setupFilterListeners();
    loadGamingProducts();
});

function initializeGamingPage() {
    // Load stored data
    const storedCart = localStorage.getItem('cartItems');
    if (storedCart) {
        window.CREE2GK.cartItems = JSON.parse(storedCart);
        updateCartCount();
    }
}

function setupFilterListeners() {
    // Search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value.toLowerCase();
            filterProducts(query);
        }, 300));
    }

    // Sort dropdown
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentFilters.sortBy = this.value;
            filterAndDisplayProducts();
        });
    }

    // Clear filters button
    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearAllFilters();
        });
    }

    // Price range filters
    document.addEventListener('change', function(e) {
        if (e.target.name === 'priceRange') {
            currentFilters.priceRange = e.target.checked ? e.target.value : '';
            filterAndDisplayProducts();
        }
        if (e.target.name === 'platform') {
            currentFilters.platform = e.target.checked ? e.target.value : '';
            filterAndDisplayProducts();
        }
    });
}

async function loadGamingProducts() {
    showLoading(true);
    
    try {
        const response = await fetch('api/products.php?category=cartes-gaming');
        const result = await response.json();
        
        if (result.success) {
            allProducts = result.data;
            setupPlatformFilters();
            filterAndDisplayProducts();
        } else {
            showError('Erreur lors du chargement des produits');
        }
    } catch (error) {
        console.error('Error loading products:', error);
        // Fallback to static data
        loadStaticGamingProducts();
    } finally {
        showLoading(false);
    }
}

function loadStaticGamingProducts() {
    // Fallback static data
    allProducts = [
        {
            id: 1,
            name: 'Carte Steam 10€',
            price: 10.00,
            original_price: 10.00,
            platform: 'Steam',
            image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+10€',
            badge: 'POPULAIRE',
            badge_color: 'bg-blue-500',
            delivery_time: 'Instantané',
            rating: 4.9,
            stock_status: 'in_stock',
            description: 'Ajoutez des fonds à votre portefeuille Steam',
            features: ['Livraison instantanée', 'Code officiel Steam', 'Valable dans le monde entier']
        },
        {
            id: 2,
            name: 'Carte Steam 25€',
            price: 25.00,
            original_price: 25.00,
            platform: 'Steam',
            image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+25€',
            badge: 'BEST SELLER',
            badge_color: 'bg-purple-500',
            delivery_time: 'Instantané',
            rating: 4.9,
            stock_status: 'in_stock',
            description: 'Parfait pour acheter vos jeux préférés',
            features: ['Livraison instantanée', 'Code officiel Steam', 'Valable dans le monde entier']
        },
        {
            id: 3,
            name: 'Carte Steam 50€',
            price: 50.00,
            original_price: 52.00,
            platform: 'Steam',
            image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+50€',
            badge: 'PROMO',
            badge_color: 'bg-red-500',
            delivery_time: 'Instantané',
            rating: 4.8,
            stock_status: 'in_stock',
            description: 'Économisez 2€ sur cette carte',
            features: ['Livraison instantanée', 'Code officiel Steam', 'Valable dans le monde entier']
        },
        {
            id: 4,
            name: 'PlayStation Store 20€',
            price: 20.00,
            original_price: 20.00,
            platform: 'PlayStation',
            image: 'https://via.placeholder.com/300x200/003087/ffffff?text=PS+Store+20€',
            badge: null,
            badge_color: '',
            delivery_time: 'Instantané',
            rating: 4.7,
            stock_status: 'in_stock',
            description: 'Achetez des jeux et contenus PS4/PS5',
            features: ['Compatible PS4/PS5', 'Jeux et DLC', 'Contenu exclusif']
        },
        {
            id: 5,
            name: 'Xbox Live Gold 50€',
            price: 50.00,
            original_price: 50.00,
            platform: 'Xbox',
            image: 'https://via.placeholder.com/300x200/107c10/ffffff?text=Xbox+Live+50€',
            badge: null,
            badge_color: '',
            delivery_time: 'Instantané',
            rating: 4.6,
            stock_status: 'in_stock',
            description: 'Accédez au multijoueur Xbox',
            features: ['Multijoueur en ligne', 'Jeux gratuits', 'Remises exclusives']
        },
        {
            id: 6,
            name: 'Nintendo eShop 25€',
            price: 25.00,
            original_price: 25.00,
            platform: 'Nintendo',
            image: 'https://via.placeholder.com/300x200/e60012/ffffff?text=Nintendo+25€',
            badge: null,
            badge_color: '',
            delivery_time: 'Instantané',
            rating: 4.8,
            stock_status: 'in_stock',
            description: 'Jeux et contenus Nintendo Switch',
            features: ['Nintendo Switch', 'Jeux exclusifs', 'DLC et contenus']
        }
    ];
    
    setupPlatformFilters();
    filterAndDisplayProducts();
}

function setupPlatformFilters() {
    const platformFilters = document.getElementById('platformFilters');
    if (!platformFilters) return;
    
    // Get unique platforms
    const platforms = [...new Set(allProducts.map(p => p.platform))];
    
    const filtersHTML = platforms.map(platform => `
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="platform" value="${platform}" class="mr-2">
            <span class="text-gray-300">${platform}</span>
        </label>
    `).join('');
    
    platformFilters.innerHTML = filtersHTML;
}

function filterProducts(searchQuery = '') {
    let filtered = allProducts;
    
    // Search filter
    if (searchQuery) {
        filtered = filtered.filter(product => 
            product.name.toLowerCase().includes(searchQuery) ||
            product.description.toLowerCase().includes(searchQuery)
        );
    }
    
    // Platform filter
    if (currentFilters.platform) {
        filtered = filtered.filter(product => product.platform === currentFilters.platform);
    }
    
    // Price range filter
    if (currentFilters.priceRange) {
        const [min, max] = currentFilters.priceRange.split('-').map(Number);
        filtered = filtered.filter(product => {
            const price = product.price;
            if (max === 999) {
                return price >= min;
            }
            return price >= min && price <= max;
        });
    }
    
    return filtered;
}

function filterAndDisplayProducts() {
    const filtered = filterProducts();
    const sorted = sortProducts(filtered);
    displayProducts(sorted);
    updateProductCount(sorted.length);
}

function sortProducts(products) {
    const sorted = [...products];
    
    switch (currentFilters.sortBy) {
        case 'price-low':
            return sorted.sort((a, b) => a.price - b.price);
        case 'price-high':
            return sorted.sort((a, b) => b.price - a.price);
        case 'rating':
            return sorted.sort((a, b) => b.rating - a.rating);
        case 'popular':
        default:
            return sorted;
    }
}

function displayProducts(products) {
    const grid = document.getElementById('gamingProductsGrid');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="ri-search-line text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">Aucun produit trouvé</h3>
                <p class="text-gray-500">Essayez de modifier vos filtres de recherche</p>
            </div>
        `;
        return;
    }

    const productsHTML = products.map(product => `
        <div class="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
            <div class="relative">
                <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded-t-xl">
                ${product.badge ? `
                    <span class="absolute top-3 left-3 ${product.badge_color} text-white px-3 py-1 rounded-full text-sm font-bold">
                        ${product.badge}
                    </span>
                ` : ''}
                <div class="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                    <i class="ri-flashlight-line mr-1"></i>
                    ${product.delivery_time}
                </div>
                <button onclick="toggleGamingFavorite(${product.id})" class="absolute top-12 right-3 p-2 bg-black/50 rounded-full hover:bg-black/70 transition-colors">
                    <i class="${favorites.includes(product.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-white'}"></i>
                </button>
            </div>

            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    ${product.name}
                </h3>
                <p class="text-gray-400 text-sm mb-3">${product.description}</p>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-white">€${product.price.toFixed(2)}</span>
                        ${product.original_price && product.original_price !== product.price ? `
                            <span class="text-sm text-gray-400 line-through">€${product.original_price.toFixed(2)}</span>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-star-fill text-yellow-500"></i>
                        <span class="text-sm text-gray-300">${product.rating}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm ${getStockStatusClass(product.stock_status)} px-3 py-1 rounded-full">
                        ${getStockStatusText(product.stock_status)}
                    </span>
                    <span class="text-sm text-blue-400">
                        ${product.platform}
                    </span>
                </div>

                <button onclick="addToCart(${product.id})" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105">
                    <i class="ri-shopping-cart-line mr-2"></i>
                    Acheter maintenant
                </button>
            </div>
        </div>
    `).join('');

    grid.innerHTML = productsHTML;
}

function toggleGamingFavorite(productId) {
    const index = favorites.indexOf(productId);
    if (index > -1) {
        favorites.splice(index, 1);
        window.CREE2GK.showNotification('Retiré des favoris', 'info');
    } else {
        favorites.push(productId);
        window.CREE2GK.showNotification('Ajouté aux favoris !', 'success');
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
    filterAndDisplayProducts(); // Refresh display to update heart icons
}

function addToCart(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) return;
    
    // Check if product already in cart
    const existingItem = window.CREE2GK.cartItems.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        window.CREE2GK.cartItems.push({
            ...product,
            quantity: 1
        });
    }
    
    localStorage.setItem('cartItems', JSON.stringify(window.CREE2GK.cartItems));
    updateCartCount();
    window.CREE2GK.showNotification('Produit ajouté au panier !', 'success');
}

function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const totalItems = window.CREE2GK.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

function updateProductCount(count) {
    const countElement = document.getElementById('productCount');
    if (countElement) {
        countElement.textContent = count;
    }
}

function clearAllFilters() {
    // Clear all radio buttons
    document.querySelectorAll('input[type="radio"]').forEach(input => {
        input.checked = false;
    });

    // Reset filters
    currentFilters = {
        platform: '',
        priceRange: '',
        sortBy: 'popular'
    };

    // Reset sort dropdown
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.value = 'popular';
    }

    // Clear search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }

    // Reload all products
    filterAndDisplayProducts();
    window.CREE2GK.showNotification('Filtres effacés', 'info');
}

function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    const grid = document.getElementById('gamingProductsGrid');
    
    if (spinner && grid) {
        if (show) {
            spinner.classList.remove('hidden');
            grid.classList.add('hidden');
        } else {
            spinner.classList.add('hidden');
            grid.classList.remove('hidden');
        }
    }
}

function showError(message) {
    const grid = document.getElementById('gamingProductsGrid');
    if (grid) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <i class="ri-error-warning-line text-6xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">Erreur</h3>
                <p class="text-gray-500">${message}</p>
            </div>
        `;
    }
}

function getStockStatusClass(status) {
    switch (status) {
        case 'in_stock':
            return 'bg-green-500/20 text-green-400';
        case 'limited':
            return 'bg-orange-500/20 text-orange-400';
        case 'out_of_stock':
            return 'bg-red-500/20 text-red-400';
        default:
            return 'bg-gray-500/20 text-gray-400';
    }
}

function getStockStatusText(status) {
    switch (status) {
        case 'in_stock':
            return 'En stock';
        case 'limited':
            return 'Stock limité';
        case 'out_of_stock':
            return 'Rupture de stock';
        default:
            return 'Indisponible';
    }
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