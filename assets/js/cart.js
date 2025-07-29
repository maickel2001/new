// Cart page JavaScript

let cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]');
let appliedCoupon = null;

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
    initializeCartPage();
    setupEventListeners();
});

function initializeCartPage() {
    loadCartItems();
    updateCartSummary();
    updateCartCount();
}

function setupEventListeners() {
    // Apply coupon button
    const applyCouponBtn = document.getElementById('applyCoupon');
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', applyCoupon);
    }

    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', proceedToCheckout);
    }

    // Coupon code input (Enter key)
    const couponInput = document.getElementById('couponCode');
    if (couponInput) {
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyCoupon();
            }
        });
    }
}

function loadCartItems() {
    const cartItemsContainer = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    
    if (!cartItemsContainer || !emptyCart) return;

    if (cartItems.length === 0) {
        cartItemsContainer.classList.add('hidden');
        emptyCart.classList.remove('hidden');
        return;
    }

    cartItemsContainer.classList.remove('hidden');
    emptyCart.classList.add('hidden');

    const itemsHTML = cartItems.map(item => `
        <div class="flex items-center space-x-4 p-4 border border-gray-700 rounded-lg mb-4" data-product-id="${item.id}">
            <img src="${item.image || 'https://via.placeholder.com/80x80/374151/ffffff?text=Product'}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg">
            
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-1">${item.name}</h3>
                <p class="text-gray-400 text-sm mb-2">${item.description || 'Produit numérique'}</p>
                <div class="flex items-center space-x-2">
                    ${item.badge ? `<span class="text-xs ${item.badge_color || 'bg-blue-500'} text-white px-2 py-1 rounded-full">${item.badge}</span>` : ''}
                    <span class="text-xs text-gray-400">${item.delivery_time || 'Livraison instantanée'}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" class="w-8 h-8 bg-gray-700 hover:bg-gray-600 text-white rounded-full flex items-center justify-center transition-colors">
                        <i class="ri-subtract-line"></i>
                    </button>
                    <span class="w-8 text-center text-white font-semibold">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" class="w-8 h-8 bg-gray-700 hover:bg-gray-600 text-white rounded-full flex items-center justify-center transition-colors">
                        <i class="ri-add-line"></i>
                    </button>
                </div>
                
                <div class="text-right">
                    <div class="text-lg font-bold text-white">€${(item.price * item.quantity).toFixed(2)}</div>
                    ${item.original_price && item.original_price !== item.price ? `
                        <div class="text-sm text-gray-400 line-through">€${(item.original_price * item.quantity).toFixed(2)}</div>
                    ` : ''}
                </div>
                
                <button onclick="removeFromCart(${item.id})" class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    `).join('');

    cartItemsContainer.innerHTML = itemsHTML;
}

function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }

    const item = cartItems.find(item => item.id === productId);
    if (item) {
        item.quantity = newQuantity;
        saveCart();
        loadCartItems();
        updateCartSummary();
        updateCartCount();
        window.CREE2GK.showNotification('Quantité mise à jour', 'info');
    }
}

function removeFromCart(productId) {
    cartItems = cartItems.filter(item => item.id !== productId);
    saveCart();
    loadCartItems();
    updateCartSummary();
    updateCartCount();
    window.CREE2GK.showNotification('Produit retiré du panier', 'info');
}

function updateCartSummary() {
    const subtotalElement = document.getElementById('subtotal');
    const taxElement = document.getElementById('tax');
    const totalElement = document.getElementById('total');

    if (!subtotalElement || !taxElement || !totalElement) return;

    let subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Apply coupon discount
    if (appliedCoupon) {
        if (appliedCoupon.type === 'percentage') {
            subtotal = subtotal * (1 - appliedCoupon.value / 100);
        } else if (appliedCoupon.type === 'fixed') {
            subtotal = Math.max(0, subtotal - appliedCoupon.value);
        }
    }

    const tax = subtotal * 0.20; // 20% TVA
    const total = subtotal + tax;

    subtotalElement.textContent = `€${subtotal.toFixed(2)}`;
    taxElement.textContent = `€${tax.toFixed(2)}`;
    totalElement.textContent = `€${total.toFixed(2)}`;
}

function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

function saveCart() {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
    // Update global cart items
    if (window.CREE2GK) {
        window.CREE2GK.cartItems = cartItems;
    }
}

async function applyCoupon() {
    const couponInput = document.getElementById('couponCode');
    const applyCouponBtn = document.getElementById('applyCoupon');
    
    if (!couponInput || !applyCouponBtn) return;

    const couponCode = couponInput.value.trim();
    if (!couponCode) {
        window.CREE2GK.showNotification('Veuillez entrer un code promo', 'warning');
        return;
    }

    // Disable button during request
    applyCouponBtn.disabled = true;
    applyCouponBtn.textContent = 'Vérification...';

    try {
        // Simulate API call for coupon validation
        const isValid = await validateCoupon(couponCode);
        
        if (isValid) {
            appliedCoupon = {
                code: couponCode,
                type: 'percentage',
                value: 10 // 10% discount
            };
            updateCartSummary();
            window.CREE2GK.showNotification('Code promo appliqué !', 'success');
            couponInput.disabled = true;
            applyCouponBtn.textContent = 'Appliqué';
        } else {
            window.CREE2GK.showNotification('Code promo invalide', 'error');
        }
    } catch (error) {
        window.CREE2GK.showNotification('Erreur lors de la vérification du code', 'error');
    } finally {
        if (!appliedCoupon) {
            applyCouponBtn.disabled = false;
            applyCouponBtn.textContent = 'Appliquer';
        }
    }
}

async function validateCoupon(code) {
    // Simulate API call
    return new Promise((resolve) => {
        setTimeout(() => {
            // Simple validation - in real app, this would be an API call
            const validCoupons = ['WELCOME10', 'SAVE20', 'FIRST15'];
            resolve(validCoupons.includes(code.toUpperCase()));
        }, 1000);
    });
}

function proceedToCheckout() {
    if (cartItems.length === 0) {
        window.CREE2GK.showNotification('Votre panier est vide', 'warning');
        return;
    }

    // Calculate total
    let subtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    if (appliedCoupon) {
        if (appliedCoupon.type === 'percentage') {
            subtotal = subtotal * (1 - appliedCoupon.value / 100);
        } else if (appliedCoupon.type === 'fixed') {
            subtotal = Math.max(0, subtotal - appliedCoupon.value);
        }
    }

    const tax = subtotal * 0.20;
    const total = subtotal + tax;

    // Store checkout data
    const checkoutData = {
        items: cartItems,
        coupon: appliedCoupon,
        subtotal: subtotal,
        tax: tax,
        total: total,
        timestamp: new Date().toISOString()
    };

    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));

    // Redirect to checkout page
    window.location.href = 'checkout.html';
}

// Initialize cart items from global state if available
if (window.CREE2GK && window.CREE2GK.cartItems) {
    cartItems = window.CREE2GK.cartItems;
}