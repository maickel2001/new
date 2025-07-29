// Gaming products page JavaScript

const gamingProducts = [
    {
        id: 1,
        name: 'Carte Steam 10€',
        price: '10.00',
        originalPrice: '10.00',
        platform: 'Steam',
        image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+10€',
        badge: 'POPULAIRE',
        badgeColor: 'bg-blue-500',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock',
        description: 'Ajoutez des fonds à votre portefeuille Steam'
    },
    {
        id: 2,
        name: 'Carte Steam 25€',
        price: '25.00',
        originalPrice: '25.00',
        platform: 'Steam',
        image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+25€',
        badge: 'BEST SELLER',
        badgeColor: 'bg-purple-500',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock',
        description: 'Parfait pour acheter vos jeux préférés'
    },
    {
        id: 3,
        name: 'Carte Steam 50€',
        price: '50.00',
        originalPrice: '52.00',
        platform: 'Steam',
        image: 'https://via.placeholder.com/300x200/1e3a8a/ffffff?text=Steam+50€',
        badge: 'PROMO',
        badgeColor: 'bg-red-500',
        delivery: 'Instantané',
        rating: 4.8,
        stock: 'En stock',
        description: 'Économisez 2€ sur cette carte'
    },
    {
        id: 4,
        name: 'PlayStation Store 20€',
        price: '20.00',
        originalPrice: '20.00',
        platform: 'PlayStation',
        image: 'https://via.placeholder.com/300x200/003087/ffffff?text=PS+Store+20€',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.7,
        stock: 'En stock',
        description: 'Achetez des jeux et contenus PS4/PS5'
    },
    {
        id: 5,
        name: 'Xbox Live Gold 50€',
        price: '50.00',
        originalPrice: '50.00',
        platform: 'Xbox',
        image: 'https://via.placeholder.com/300x200/107c10/ffffff?text=Xbox+Live+50€',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.6,
        stock: 'En stock',
        description: 'Accédez au multijoueur Xbox'
    },
    {
        id: 6,
        name: 'Nintendo eShop 25€',
        price: '25.00',
        originalPrice: '25.00',
        platform: 'Nintendo',
        image: 'https://via.placeholder.com/300x200/e60012/ffffff?text=Nintendo+25€',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.8,
        stock: 'En stock',
        description: 'Jeux et contenus Nintendo Switch'
    },
    {
        id: 7,
        name: 'Riot Points 1380 RP',
        price: '10.00',
        originalPrice: '10.00',
        platform: 'Riot Games',
        image: 'https://via.placeholder.com/300x200/c89b3c/ffffff?text=Riot+Points+1380',
        badge: 'NOUVEAU',
        badgeColor: 'bg-green-500',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock',
        description: 'Monnaie virtuelle League of Legends'
    },
    {
        id: 8,
        name: 'Epic Games Store 25€',
        price: '25.00',
        originalPrice: '25.00',
        platform: 'Epic Games',
        image: 'https://via.placeholder.com/300x200/313131/ffffff?text=Epic+Games+25€',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.5,
        stock: 'En stock',
        description: 'Jeux exclusifs Epic Games Store'
    },
    {
        id: 9,
        name: 'Fortnite V-Bucks 2800',
        price: '19.99',
        originalPrice: '19.99',
        platform: 'Fortnite',
        image: 'https://via.placeholder.com/300x200/0078f2/ffffff?text=V-Bucks+2800',
        badge: 'POPULAIRE',
        badgeColor: 'bg-blue-500',
        delivery: 'Instantané',
        rating: 4.8,
        stock: 'En stock',
        description: 'Monnaie virtuelle Fortnite'
    },
    {
        id: 10,
        name: 'Minecraft Java Edition',
        price: '23.95',
        originalPrice: '26.95',
        platform: 'Minecraft',
        image: 'https://via.placeholder.com/300x200/62b47a/ffffff?text=Minecraft+Java',
        badge: 'PROMO',
        badgeColor: 'bg-red-500',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock',
        description: 'Version complète Minecraft Java'
    },
    {
        id: 11,
        name: 'Roblox 800 Robux',
        price: '9.99',
        originalPrice: '9.99',
        platform: 'Roblox',
        image: 'https://via.placeholder.com/300x200/00a2ff/ffffff?text=Roblox+800',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.7,
        stock: 'En stock',
        description: 'Monnaie virtuelle Roblox'
    },
    {
        id: 12,
        name: 'Apex Legends 2150 Coins',
        price: '19.99',
        originalPrice: '19.99',
        platform: 'Apex Legends',
        image: 'https://via.placeholder.com/300x200/ff6600/ffffff?text=Apex+2150',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.6,
        stock: 'En stock',
        description: 'Monnaie virtuelle Apex Legends'
    }
];

let currentFilters = {
    platform: '',
    priceRange: '',
    sortBy: 'popular'
};

let favorites = [];

// Initialize gaming page
document.addEventListener('DOMContentLoaded', function() {
    initializeGamingPage();
    setupFilterListeners();
});

function initializeGamingPage() {
    loadGamingProducts();
    updateProductCount();
}

function setupFilterListeners() {
    // Platform filters
    document.querySelectorAll('input[name="platform"]').forEach(input => {
        input.addEventListener('change', function() {
            currentFilters.platform = this.checked ? this.value : '';
            filterAndDisplayProducts();
        });
    });

    // Price range filters
    document.querySelectorAll('input[name="priceRange"]').forEach(input => {
        input.addEventListener('change', function() {
            currentFilters.priceRange = this.checked ? this.value : '';
            filterAndDisplayProducts();
        });
    });

    // Sort dropdown
    const sortSelect = document.querySelector('select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentFilters.sortBy = this.value;
            filterAndDisplayProducts();
        });
    }

    // Clear filters button
    const clearFiltersBtn = document.querySelector('.bg-gray-700');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearAllFilters();
        });
    }
}

function filterAndDisplayProducts() {
    const filteredProducts = applyFilters(gamingProducts);
    const sortedProducts = sortProducts(filteredProducts);
    displayProducts(sortedProducts);
    updateProductCount(sortedProducts.length);
}

function applyFilters(products) {
    return products.filter(product => {
        // Platform filter
        if (currentFilters.platform && product.platform !== currentFilters.platform) {
            return false;
        }

        // Price range filter
        if (currentFilters.priceRange) {
            const [min, max] = currentFilters.priceRange.split('-').map(Number);
            const price = parseFloat(product.price);
            if (max === 999) {
                if (price < min) return false;
            } else {
                if (price < min || price > max) return false;
            }
        }

        return true;
    });
}

function sortProducts(products) {
    const sorted = [...products];
    
    switch (currentFilters.sortBy) {
        case 'price-low':
            return sorted.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
        case 'price-high':
            return sorted.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
        case 'rating':
            return sorted.sort((a, b) => b.rating - a.rating);
        case 'popular':
        default:
            return sorted;
    }
}

function loadGamingProducts() {
    displayProducts(gamingProducts);
}

function displayProducts(products) {
    const grid = document.getElementById('gamingProductsGrid');
    if (!grid) return;

    const productsHTML = products.map(product => `
        <div class="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
            <div class="relative">
                <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded-t-xl">
                ${product.badge ? `
                    <span class="absolute top-3 left-3 ${product.badgeColor} text-white px-3 py-1 rounded-full text-sm font-bold">
                        ${product.badge}
                    </span>
                ` : ''}
                <div class="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                    <i class="ri-flashlight-line mr-1"></i>
                    ${product.delivery}
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
                        <span class="text-2xl font-bold text-white">€${product.price}</span>
                        ${product.originalPrice !== product.price ? `
                            <span class="text-sm text-gray-400 line-through">€${product.originalPrice}</span>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-1">
                        <i class="ri-star-fill text-yellow-500"></i>
                        <span class="text-sm text-gray-300">${product.rating}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                        ${product.stock}
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
    
    // Update the display
    filterAndDisplayProducts();
}

function updateProductCount(count = gamingProducts.length) {
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
    const sortSelect = document.querySelector('select');
    if (sortSelect) {
        sortSelect.value = 'popular';
    }

    // Reload all products
    loadGamingProducts();
    updateProductCount();
    
    window.CREE2GK.showNotification('Filtres effacés', 'info');
}