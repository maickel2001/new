
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function CartesGamingPage() {
  const [filters, setFilters] = useState({
    platform: '',
    priceRange: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const gamingProducts = [
    {
      id: 1,
      name: 'Carte Steam 10€',
      price: '10.00',
      originalPrice: '10.00',
      platform: 'Steam',
      image: 'https://readdy.ai/api/search-image?query=Steam%20gift%20card%2010%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design%20with%20steam%20logo&width=300&height=200&seq=steam-10-card&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Steam%20gift%20card%2025%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design%20with%20steam%20logo&width=300&height=200&seq=steam-25-card&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Steam%20gift%20card%2050%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design%20with%20steam%20logo&width=300&height=200&seq=steam-50-card&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=PlayStation%20Store%20gift%20card%2020%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design%20with%20PlayStation%20logo&width=300&height=200&seq=ps-store-20&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Xbox%20Live%20Gold%20gift%20card%2050%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20green%20gradient%20clean%20minimal%20design%20with%20Xbox%20logo&width=300&height=200&seq=xbox-live-50&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Nintendo%20eShop%20gift%20card%2025%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20red%20gradient%20clean%20minimal%20design%20with%20Nintendo%20logo&width=300&height=200&seq=nintendo-eshop-25&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=League%20of%20Legends%20Riot%20Points%201380%20RP%20gaming%20currency%20digital%20code%20dark%20background%20professional%20e-commerce%20product%20image%20gold%20gradient%20clean%20minimal%20design%20with%20Riot%20logo&width=300&height=200&seq=riot-points-1380&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Epic%20Games%20Store%20gift%20card%2025%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20purple%20gradient%20clean%20minimal%20design%20with%20Epic%20logo&width=300&height=200&seq=epic-games-25&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Fortnite%20V-Bucks%202800%20gaming%20currency%20digital%20code%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design%20with%20Fortnite%20logo&width=300&height=200&seq=fortnite-vbucks-2800&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Minecraft%20Java%20Edition%20game%20license%20digital%20code%20dark%20background%20professional%20e-commerce%20product%20image%20brown%20gradient%20clean%20minimal%20design%20with%20Minecraft%20logo&width=300&height=200&seq=minecraft-java&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Roblox%20800%20Robux%20gaming%20currency%20digital%20code%20dark%20background%20professional%20e-commerce%20product%20image%20green%20gradient%20clean%20minimal%20design%20with%20Roblox%20logo&width=300&height=200&seq=roblox-800&orientation=landscape',
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
      image: 'https://readdy.ai/api/search-image?query=Apex%20Legends%202150%20Coins%20gaming%20currency%20digital%20code%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20gradient%20clean%20minimal%20design%20with%20Apex%20logo&width=300&height=200&seq=apex-coins-2150&orientation=landscape',
      badge: null,
      badgeColor: '',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Monnaie virtuelle Apex Legends'
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = gamingProducts.filter(product => {
    if (filters.platform && product.platform !== filters.platform) return false;
    if (filters.priceRange) {
      const [min, max] = filters.priceRange.split('-').map(Number);
      const price = parseFloat(product.price);
      if (price < min || price > max) return false;
    }
    return true;
  });

  const sortedProducts = [...filteredProducts].sort((a, b) => {
    switch (filters.sortBy) {
      case 'price-low':
        return parseFloat(a.price) - parseFloat(b.price);
      case 'price-high':
        return parseFloat(b.price) - parseFloat(a.price);
      case 'rating':
        return b.rating - a.rating;
      default:
        return 0;
    }
  });

  return (
    <div className="min-h-screen bg-gray-900">
      <Navigation />
      
      <div className="bg-gradient-to-r from-purple-900 to-blue-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Cartes <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Gaming</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Rechargez vos comptes gaming favoris instantanément. Steam, PlayStation, Xbox, Nintendo et bien plus !
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="flex flex-col lg:flex-row gap-8">
          {/* Filtres */}
          <div className="lg:w-1/4">
            <div className="bg-gray-800 rounded-xl p-6 border border-gray-700 sticky top-24">
              <h3 className="text-xl font-bold text-white mb-6">Filtres</h3>
              
              <div className="space-y-6">
                <div>
                  <h4 className="font-semibold text-white mb-3">Plateforme</h4>
                  <div className="space-y-2">
                    {['Steam', 'PlayStation', 'Xbox', 'Nintendo', 'Epic Games', 'Riot Games'].map(platform => (
                      <label key={platform} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="platform"
                          value={platform}
                          checked={filters.platform === platform}
                          onChange={(e) => setFilters({...filters, platform: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{platform}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Prix</h4>
                  <div className="space-y-2">
                    {[
                      { label: 'Moins de 20€', value: '0-20' },
                      { label: '20€ - 50€', value: '20-50' },
                      { label: 'Plus de 50€', value: '50-999' }
                    ].map(range => (
                      <label key={range.value} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="priceRange"
                          value={range.value}
                          checked={filters.priceRange === range.value}
                          onChange={(e) => setFilters({...filters, priceRange: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{range.label}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({platform: '', priceRange: '', sortBy: 'popular'})}
                  className="w-full bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors cursor-pointer whitespace-nowrap"
                >
                  Effacer les filtres
                </button>
              </div>
            </div>
          </div>

          {/* Produits */}
          <div className="lg:w-3/4">
            <div className="flex items-center justify-between mb-8">
              <p className="text-gray-300">
                {sortedProducts.length} produits trouvés
              </p>
              <select 
                value={filters.sortBy} 
                onChange={(e) => setFilters({...filters, sortBy: e.target.value})}
                className="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white pr-8"
              >
                <option value="popular">Popularité</option>
                <option value="price-low">Prix croissant</option>
                <option value="price-high">Prix décroissant</option>
                <option value="rating">Meilleures notes</option>
              </select>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {sortedProducts.map((product) => (
                <div key={product.id} className="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
                  <div className="relative">
                    <img
                      src={product.image}
                      alt={product.name}
                      className="w-full h-48 object-cover rounded-t-xl"
                    />
                    {product.badge && (
                      <span className={`absolute top-3 left-3 ${product.badgeColor} text-white px-3 py-1 rounded-full text-sm font-bold`}>
                        {product.badge}
                      </span>
                    )}
                    <div className="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                      <i className="ri-flashlight-line mr-1"></i>
                      {product.delivery}
                    </div>
                    <button 
                      onClick={() => toggleFavorite(product.id)}
                      className="absolute top-12 right-3 p-2 bg-black/50 rounded-full hover:bg-black/70 transition-colors cursor-pointer"
                    >
                      <div className="w-4 h-4 flex items-center justify-center">
                        <i className={`${favorites.includes(product.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-white'}`}></i>
                      </div>
                    </button>
                  </div>

                  <div className="p-6">
                    <h3 className="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                      {product.name}
                    </h3>
                    <p className="text-gray-400 text-sm mb-3">{product.description}</p>
                    
                    <div className="flex items-center justify-between mb-4">
                      <div className="flex items-center space-x-2">
                        <span className="text-2xl font-bold text-white">€{product.price}</span>
                        {product.originalPrice !== product.price && (
                          <span className="text-sm text-gray-400 line-through">€{product.originalPrice}</span>
                        )}
                      </div>
                      <div className="flex items-center space-x-1">
                        <div className="w-4 h-4 flex items-center justify-center">
                          <i className="ri-star-fill text-yellow-500"></i>
                        </div>
                        <span className="text-sm text-gray-300">{product.rating}</span>
                      </div>
                    </div>

                    <div className="flex items-center justify-between mb-4">
                      <span className="text-sm bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                        {product.stock}
                      </span>
                      <span className="text-sm text-blue-400">
                        {product.platform}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
                      <i className="ri-shopping-cart-line mr-2"></i>
                      Acheter maintenant
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <FooterDigital />
    </div>
  );
}
