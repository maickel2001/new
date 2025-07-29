
'use client';

import { useState } from 'react';

export default function ProductGrid({ filters }: { filters: any }) {
  const [favorites, setFavorites] = useState<number[]>([]);

  const allProducts = [
    {
      id: 1,
      name: 'iPhone 15 Pro Max',
      brand: 'Apple',
      price: 1479,
      originalPrice: 1599,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20Pro%20Max%20in%20natural%20titanium%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20soft%20shadows%2C%20premium%20large%20smartphone%20showcasing%20elegant%20design%2C%20high-end%20mobile%20device%20with%20sophisticated%20lighting%2C%20commercial%20product%20style&width=300&height=300&seq=iphone15promax&orientation=squarish',
      badge: 'Nouveau',
      rating: 4.8,
      reviews: 245
    },
    {
      id: 2,
      name: 'Samsung Galaxy S24 Ultra',
      brand: 'Samsung',
      price: 1299,
      originalPrice: 1419,
      storage: '512GB',
      image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20S24%20Ultra%20smartphone%20in%20titanium%20black%20color%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20flagship%20Android%20device%20with%20S%20Pen%2C%20high-quality%20commercial%20showcase%20style&width=300&height=300&seq=galaxys24ultra&orientation=squarish',
      badge: 'Top vente',
      rating: 4.7,
      reviews: 189
    },
    {
      id: 3,
      name: 'Google Pixel 8 Pro',
      brand: 'Google',
      price: 1099,
      originalPrice: 1199,
      storage: '128GB',
      image: 'https://readdy.ai/api/search-image?query=Google%20Pixel%208%20Pro%20smartphone%20in%20obsidian%20black%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20Android%20flagship%20device%20with%20advanced%20camera%2C%20sophisticated%20commercial%20product%20style&width=300&height=300&seq=pixel8pro&orientation=squarish',
      badge: 'Photo Pro',
      rating: 4.6,
      reviews: 156
    },
    {
      id: 4,
      name: 'OnePlus 12 Pro',
      brand: 'OnePlus',
      price: 1049,
      originalPrice: 1149,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=OnePlus%2012%20Pro%20smartphone%20in%20sleek%20emerald%20green%20finish%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20flagship%20Android%20device%20with%20elegant%20design%2C%20high-end%20commercial%20showcase%20style&width=300&height=300&seq=oneplus12pro&orientation=squarish',
      badge: 'Performance',
      rating: 4.5,
      reviews: 134
    },
    {
      id: 5,
      name: 'Xiaomi 14 Ultra',
      brand: 'Xiaomi',
      price: 1299,
      originalPrice: 1399,
      storage: '512GB',
      image: 'https://readdy.ai/api/search-image?query=Xiaomi%2014%20Ultra%20smartphone%20in%20premium%20black%20color%20with%20large%20camera%20module%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20sophisticated%20lighting%2C%20flagship%20camera%20phone%20with%20Leica%20branding%2C%20commercial%20product%20style&width=300&height=300&seq=xiaomi14ultra&orientation=squarish',
      badge: 'Photo Pro',
      rating: 4.4,
      reviews: 98
    },
    {
      id: 6,
      name: 'iPhone 15',
      brand: 'Apple',
      price: 969,
      originalPrice: 1079,
      storage: '128GB',
      image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20in%20pink%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20premium%20smartphone%20with%20elegant%20design%20and%20USB-C%20port%2C%20high-end%20mobile%20device%20showcase%2C%20commercial%20product%20style&width=300&height=300&seq=iphone15pink&orientation=squarish',
      badge: 'Populaire',
      rating: 4.7,
      reviews: 312
    },
    {
      id: 7,
      name: 'Samsung Galaxy S24',
      brand: 'Samsung',
      price: 899,
      originalPrice: 999,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20S24%20smartphone%20in%20elegant%20violet%20color%20on%20pristine%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20modern%20flagship%20Android%20device%20with%20AI%20features%2C%20high-quality%20commercial%20showcase%20style&width=300&height=300&seq=galaxys24violet&orientation=squarish',
      badge: 'AI',
      rating: 4.6,
      reviews: 278
    },
    {
      id: 8,
      name: 'Google Pixel 8',
      brand: 'Google',
      price: 699,
      originalPrice: 799,
      storage: '128GB',
      image: 'https://readdy.ai/api/search-image?query=Google%20Pixel%208%20smartphone%20in%20hazel%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20Android%20device%20with%20advanced%20AI%20features%2C%20sophisticated%20commercial%20product%20style&width=300&height=300&seq=pixel8hazel&orientation=squarish',
      badge: 'Promo',
      rating: 4.5,
      reviews: 203
    },
    {
      id: 9,
      name: 'OnePlus 12',
      brand: 'OnePlus',
      price: 949,
      originalPrice: 1099,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=OnePlus%2012%20smartphone%20in%20flowy%20emerald%20finish%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20flagship%20Android%20device%20with%20curved%20display%2C%20high-end%20commercial%20showcase%20style&width=300&height=300&seq=oneplus12emerald&orientation=squarish',
      badge: 'Charge rapide',
      rating: 4.4,
      reviews: 167
    },
    {
      id: 10,
      name: 'Xiaomi 14',
      brand: 'Xiaomi',
      price: 799,
      originalPrice: 899,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=Xiaomi%2014%20smartphone%20in%20white%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20sophisticated%20lighting%2C%20flagship%20device%20with%20premium%20build%20quality%2C%20commercial%20product%20style&width=300&height=300&seq=xiaomi14white&orientation=squarish',
      badge: 'Rapport qualité/prix',
      rating: 4.3,
      reviews: 145
    },
    {
      id: 11,
      name: 'iPhone 14 Pro',
      brand: 'Apple',
      price: 1129,
      originalPrice: 1329,
      storage: '128GB',
      image: 'https://readdy.ai/api/search-image?query=iPhone%2014%20Pro%20in%20deep%20purple%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20Pro%20smartphone%20with%20Dynamic%20Island%2C%20high-end%20mobile%20device%20showcase%2C%20commercial%20product%20style&width=300&height=300&seq=iphone14propurple&orientation=squarish',
      badge: 'Promo',
      rating: 4.6,
      reviews: 456
    },
    {
      id: 12,
      name: 'Nothing Phone 2',
      brand: 'Nothing',
      price: 649,
      originalPrice: 749,
      storage: '256GB',
      image: 'https://readdy.ai/api/search-image?query=Nothing%20Phone%202%20smartphone%20with%20transparent%20back%20design%20and%20unique%20LED%20light%20patterns%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20innovative%20Android%20device%20with%20distinctive%20design%2C%20commercial%20product%20style&width=300&height=300&seq=nothingphone2&orientation=squarish',
      badge: 'Design unique',
      rating: 4.2,
      reviews: 89
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = allProducts.filter(product => {
    if (filters.brand && product.brand !== filters.brand) return false;
    if (filters.storage && product.storage !== filters.storage) return false;
    if (filters.priceRange) {
      const [min, max] = filters.priceRange.split('-').map(Number);
      if (product.price < min || product.price > max) return false;
    }
    return true;
  });

  const sortedProducts = [...filteredProducts].sort((a, b) => {
    switch (filters.sortBy) {
      case 'price-low':
        return a.price - b.price;
      case 'price-high':
        return b.price - a.price;
      case 'rating':
        return b.rating - a.rating;
      case 'newest':
        return b.id - a.id;
      default:
        return 0;
    }
  });

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <p className="text-gray-600">
          {sortedProducts.length} produits trouvés
        </p>
        <div className="flex items-center gap-4">
          <span className="text-sm text-gray-600">Trier par:</span>
          <select 
            value={filters.sortBy} 
            onChange={(e) => {}} 
            className="border border-gray-300 rounded-lg px-3 py-2 text-sm pr-8"
          >
            <option value="popular">Popularité</option>
            <option value="price-low">Prix croissant</option>
            <option value="price-high">Prix décroissant</option>
            <option value="rating">Meilleures notes</option>
            <option value="newest">Plus récents</option>
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {sortedProducts.map((product) => (
          <div key={product.id} className="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
            <div className="relative">
              <img
                src={product.image}
                alt={product.name}
                className="w-full h-64 object-cover object-top group-hover:scale-105 transition-transform duration-300"
              />
              <div className="absolute top-4 left-4">
                <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                  {product.badge}
                </span>
              </div>
              <button 
                onClick={() => toggleFavorite(product.id)}
                className="absolute top-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
              >
                <div className="w-5 h-5 flex items-center justify-center">
                  <i className={`${favorites.includes(product.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-gray-600'}`}></i>
                </div>
              </button>
            </div>
            <div className="p-6">
              <h3 className="text-xl font-bold text-gray-900 mb-2">
                {product.name}
              </h3>
              <div className="flex items-center gap-2 mb-2">
                <div className="flex items-center">
                  {[...Array(5)].map((_, i) => (
                    <div key={i} className="w-4 h-4 flex items-center justify-center">
                      <i className={`ri-star-${i < Math.floor(product.rating) ? 'fill' : 'line'} text-yellow-400`}></i>
                    </div>
                  ))}
                </div>
                <span className="text-sm text-gray-600">({product.reviews})</span>
              </div>
              <div className="flex items-center gap-2 mb-4">
                <span className="text-2xl font-bold text-blue-600">
                  {product.price}€
                </span>
                <span className="text-lg text-gray-400 line-through">
                  {product.originalPrice}€
                </span>
              </div>
              <div className="flex items-center gap-2 mb-4">
                <span className="text-sm text-gray-600">Stockage:</span>
                <span className="text-sm font-semibold">{product.storage}</span>
              </div>
              <button className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap cursor-pointer">
                Ajouter au panier
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
