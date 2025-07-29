'use client';

import Header from '../components/Header';
import Footer from '../components/Footer';
import { useState } from 'react';

export default function TablettesPage() {
  const [filters, setFilters] = useState({
    brand: '',
    priceRange: '',
    screenSize: '',
    sortBy: 'popular'
  });

  const [favorites, setFavorites] = useState<number[]>([]);

  const tablets = [
    {
      id: 1,
      name: 'iPad Pro 12.9" M2',
      brand: 'Apple',
      price: 1449,
      originalPrice: 1549,
      screenSize: '12.9"',
      image: 'https://readdy.ai/api/search-image?query=iPad%20Pro%2012.9%20inch%20M2%20in%20space%20gray%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20large%20premium%20tablet%20device%20with%20Apple%20Pencil%2C%20sophisticated%20commercial%20showcase%20style&width=300&height=300&seq=ipadpro129m2&orientation=squarish',
      badge: 'Premium',
      rating: 4.8,
      reviews: 167,
      specs: ['Puce M2', 'WiFi 6E', 'Face ID', 'Compatible Apple Pencil']
    },
    {
      id: 2,
      name: 'Samsung Galaxy Tab S9 Ultra',
      brand: 'Samsung',
      price: 1199,
      originalPrice: 1349,
      screenSize: '14.6"',
      image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Tab%20S9%20Ultra%20in%20beige%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20large%20premium%20Android%20tablet%20with%20S%20Pen%2C%20sophisticated%20commercial%20showcase%20style&width=300&height=300&seq=galaxytabs9ultra&orientation=squarish',
      badge: 'Ultra',
      rating: 4.7,
      reviews: 92,
      specs: ['Écran AMOLED 2X', 'S Pen inclus', 'DeX Mode', 'Résistant à l\'eau']
    },
    {
      id: 3,
      name: 'iPad Air 5e génération',
      brand: 'Apple',
      price: 649,
      originalPrice: 729,
      screenSize: '10.9"',
      image: 'https://readdy.ai/api/search-image?query=iPad%20Air%205th%20generation%20in%20blue%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20mid-range%20premium%20tablet%20with%20modern%20design%2C%20elegant%20commercial%20product%20showcase%20style&width=300&height=300&seq=ipadair5blue&orientation=squarish',
      badge: 'Populaire',
      rating: 4.6,
      reviews: 234,
      specs: ['Puce M1', 'Touch ID', 'USB-C', 'Compatible Magic Keyboard']
    },
    {
      id: 4,
      name: 'Microsoft Surface Pro 9',
      brand: 'Microsoft',
      price: 1179,
      originalPrice: 1299,
      screenSize: '13"',
      image: 'https://readdy.ai/api/search-image?query=Microsoft%20Surface%20Pro%209%20tablet%20in%20platinum%20color%20with%20Type%20Cover%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%202-in-1%20Windows%20tablet%20with%20kickstand%2C%20commercial%20showcase%20style&width=300&height=300&seq=surfacepro9platinum&orientation=squarish',
      badge: 'Productivité',
      rating: 4.4,
      reviews: 89,
      specs: ['Windows 11', 'Intel Core i5', 'Type Cover compatible', 'Kickstand intégré']
    },
    {
      id: 5,
      name: 'Samsung Galaxy Tab S9',
      brand: 'Samsung',
      price: 799,
      originalPrice: 899,
      screenSize: '11"',
      image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Tab%20S9%20tablet%20in%20graphite%20color%20on%20pristine%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20Android%20tablet%20device%20with%20S%20Pen%2C%20high-end%20commercial%20style&width=300&height=300&seq=galaxytabs9graphite&orientation=squarish',
      badge: 'Nouveau',
      rating: 4.5,
      reviews: 143,
      specs: ['Écran Dynamic AMOLED 2X', 'S Pen inclus', 'Snapdragon 8 Gen 2', 'Book Cover Keyboard compatible']
    },
    {
      id: 6,
      name: 'iPad Pro 11" M2',
      brand: 'Apple',
      price: 949,
      originalPrice: 1079,
      screenSize: '11"',
      image: 'https://readdy.ai/api/search-image?query=iPad%20Pro%2011%20inch%20M2%20in%20silver%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20compact%20premium%20tablet%20device%20with%20Apple%20Pencil%2C%20sophisticated%20commercial%20showcase%20style&width=300&height=300&seq=ipadpro11m2silver&orientation=squarish',
      badge: 'Pro',
      rating: 4.7,
      reviews: 198,
      specs: ['Puce M2', 'ProMotion 120Hz', 'Thunderbolt', 'Caméra TrueDepth']
    },
    {
      id: 7,
      name: 'iPad 10e génération',
      brand: 'Apple',
      price: 489,
      originalPrice: 579,
      screenSize: '10.9"',
      image: 'https://readdy.ai/api/search-image?query=iPad%2010th%20generation%20in%20yellow%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20affordable%20tablet%20with%20modern%20design%2C%20elegant%20commercial%20product%20style&width=300&height=300&seq=ipad10yellow&orientation=squarish',
      badge: 'Abordable',
      rating: 4.3,
      reviews: 312,
      specs: ['Puce A14 Bionic', 'Touch ID', 'USB-C', 'Compatible Apple Pencil 1']
    },
    {
      id: 8,
      name: 'Samsung Galaxy Tab A9+',
      brand: 'Samsung',
      price: 269,
      originalPrice: 329,
      screenSize: '11"',
      image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Tab%20A9%20Plus%20tablet%20in%20dark%20gray%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20affordable%20Android%20tablet%20device%20with%20slim%20design%2C%20commercial%20showcase%20style&width=300&height=300&seq=galaxytaba9plusgray&orientation=squarish',
      badge: 'Entrée de gamme',
      rating: 4.1,
      reviews: 156,
      specs: ['Écran TFT 90Hz', 'Helio G99', 'Quad speakers', 'Multitâche amélioré']
    },
    {
      id: 9,
      name: 'iPad Mini 6e génération',
      brand: 'Apple',
      price: 549,
      originalPrice: 609,
      screenSize: '8.3"',
      image: 'https://readdy.ai/api/search-image?query=iPad%20Mini%206th%20generation%20in%20starlight%20color%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20compact%20premium%20tablet%20device%20with%20modern%20design%2C%20elegant%20commercial%20product%20style&width=300&height=300&seq=ipadmini6starlight&orientation=squarish',
      badge: 'Compact',
      rating: 4.4,
      reviews: 178,
      specs: ['Puce A15 Bionic', 'Touch ID', 'USB-C', 'Compatible Apple Pencil 2']
    },
    {
      id: 10,
      name: 'Lenovo Tab P11 Pro',
      brand: 'Lenovo',
      price: 399,
      originalPrice: 499,
      screenSize: '11.5"',
      image: 'https://readdy.ai/api/search-image?query=Lenovo%20Tab%20P11%20Pro%20tablet%20in%20slate%20gray%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20Android%20tablet%20with%20keyboard%20dock%2C%20commercial%20showcase%20style&width=300&height=300&seq=lenovotabp11pro&orientation=squarish',
      badge: 'Productivité',
      rating: 4.2,
      reviews: 87,
      specs: ['Écran OLED 2K', 'MediaTek Helio G90T', 'Quad speakers JBL', 'Keyboard Pack disponible']
    },
    {
      id: 11,
      name: 'Huawei MatePad Pro 12.6',
      brand: 'Huawei',
      price: 649,
      originalPrice: 799,
      screenSize: '12.6"',
      image: 'https://readdy.ai/api/search-image?query=Huawei%20MatePad%20Pro%2012.6%20tablet%20in%20olive%20green%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20large%20Android%20tablet%20with%20M-Pencil%2C%20sophisticated%20commercial%20style&width=300&height=300&seq=huaweimatepro126&orientation=squarish',
      badge: 'Créatif',
      rating: 4.3,
      reviews: 76,
      specs: ['Écran OLED 2.5K', 'Kirin 9000E', 'M-Pencil compatible', 'Charge sans fil 40W']
    },
    {
      id: 12,
      name: 'Xiaomi Pad 6',
      brand: 'Xiaomi',
      price: 349,
      originalPrice: 429,
      screenSize: '11"',
      image: 'https://readdy.ai/api/search-image?query=Xiaomi%20Pad%206%20tablet%20in%20champagne%20gold%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20affordable%20premium%20Android%20tablet%20with%20stylus%2C%20commercial%20product%20style&width=300&height=300&seq=xiaomipad6gold&orientation=squarish',
      badge: 'Rapport qualité/prix',
      rating: 4.4,
      reviews: 124,
      specs: ['Écran 2.8K 144Hz', 'Snapdragon 870', 'Xiaomi Smart Pen compatible', 'Charge rapide 33W']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredTablets = tablets.filter(tablet => {
    if (filters.brand && tablet.brand !== filters.brand) return false;
    if (filters.screenSize && tablet.screenSize !== filters.screenSize) return false;
    if (filters.priceRange) {
      const [min, max] = filters.priceRange.split('-').map(Number);
      if (tablet.price < min || tablet.price > max) return false;
    }
    return true;
  });

  const sortedTablets = [...filteredTablets].sort((a, b) => {
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
    <div className="min-h-screen bg-white">
      <Header />
      
      <div className="bg-gray-50 py-12">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Tablettes
          </h1>
          <p className="text-xl text-gray-600">
            Découvrez notre collection de tablettes pour tous vos besoins
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <div className="flex flex-col lg:flex-row gap-8">
          {/* Sidebar Filters */}
          <div className="lg:w-1/4">
            <div className="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
              <h3 className="text-xl font-bold text-gray-900 mb-6">Filtres</h3>
              
              <div className="space-y-6">
                {/* Brand Filter */}
                <div>
                  <h4 className="font-semibold text-gray-900 mb-3">Marque</h4>
                  <div className="space-y-2">
                    {['Apple', 'Samsung', 'Microsoft', 'Lenovo', 'Huawei', 'Xiaomi'].map(brand => (
                      <label key={brand} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="brand"
                          value={brand}
                          checked={filters.brand === brand}
                          onChange={(e) => setFilters({...filters, brand: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-700">{brand}</span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Price Range */}
                <div>
                  <h4 className="font-semibold text-gray-900 mb-3">Prix</h4>
                  <div className="space-y-2">
                    {[
                      { label: 'Moins de 400€', value: '0-400' },
                      { label: '400€ - 800€', value: '400-800' },
                      { label: '800€ - 1200€', value: '800-1200' },
                      { label: 'Plus de 1200€', value: '1200-9999' }
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
                        <span className="text-gray-700">{range.label}</span>
                      </label>
                    ))}
                  </div>
                </div>

                {/* Screen Size */}
                <div>
                  <h4 className="font-semibold text-gray-900 mb-3">Taille d'écran</h4>
                  <div className="space-y-2">
                    {['8.3"', '10.9"', '11"', '12.9"', '13"', '14.6"'].map(size => (
                      <label key={size} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="screenSize"
                          value={size}
                          checked={filters.screenSize === size}
                          onChange={(e) => setFilters({...filters, screenSize: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-700">{size}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({brand: '', priceRange: '', screenSize: '', sortBy: 'popular'})}
                  className="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors cursor-pointer whitespace-nowrap"
                >
                  Effacer les filtres
                </button>
              </div>
            </div>
          </div>

          {/* Products Grid */}
          <div className="lg:w-3/4">
            <div className="flex items-center justify-between mb-6">
              <p className="text-gray-600">
                {sortedTablets.length} tablettes trouvées
              </p>
              <div className="flex items-center gap-4">
                <span className="text-sm text-gray-600">Trier par:</span>
                <select 
                  value={filters.sortBy} 
                  onChange={(e) => setFilters({...filters, sortBy: e.target.value})}
                  className="border border-gray-300 rounded-lg px-3 py-2 text-sm pr-8"
                >
                  <option value="popular">Popularité</option>
                  <option value="price-low">Prix croissant</option>
                  <option value="price-high">Prix décroissant</option>
                  <option value="rating">Meilleures notes</option>
                  <option value="newest">Plus récentes</option>
                </select>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {sortedTablets.map((tablet) => (
                <div key={tablet.id} className="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                  <div className="relative">
                    <img
                      src={tablet.image}
                      alt={tablet.name}
                      className="w-full h-64 object-cover object-top group-hover:scale-105 transition-transform duration-300"
                    />
                    <div className="absolute top-4 left-4">
                      <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        {tablet.badge}
                      </span>
                    </div>
                    <button 
                      onClick={() => toggleFavorite(tablet.id)}
                      className="absolute top-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
                    >
                      <div className="w-5 h-5 flex items-center justify-center">
                        <i className={`${favorites.includes(tablet.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-gray-600'}`}></i>
                      </div>
                    </button>
                  </div>
                  <div className="p-6">
                    <h3 className="text-xl font-bold text-gray-900 mb-2">
                      {tablet.name}
                    </h3>
                    <div className="flex items-center gap-2 mb-2">
                      <div className="flex items-center">
                        {[...Array(5)].map((_, i) => (
                          <div key={i} className="w-4 h-4 flex items-center justify-center">
                            <i className={`ri-star-${i < Math.floor(tablet.rating) ? 'fill' : 'line'} text-yellow-400`}></i>
                          </div>
                        ))}
                      </div>
                      <span className="text-sm text-gray-600">({tablet.reviews})</span>
                    </div>
                    <div className="flex items-center gap-2 mb-3">
                      <span className="text-2xl font-bold text-blue-600">
                        {tablet.price}€
                      </span>
                      <span className="text-lg text-gray-400 line-through">
                        {tablet.originalPrice}€
                      </span>
                    </div>
                    <div className="mb-4">
                      <ul className="text-sm text-gray-600 space-y-1">
                        {tablet.specs.slice(0, 3).map((spec, index) => (
                          <li key={index} className="flex items-center">
                            <div className="w-2 h-2 bg-blue-600 rounded-full mr-2"></div>
                            {spec}
                          </li>
                        ))}
                      </ul>
                    </div>
                    <button className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap cursor-pointer">
                      Ajouter au panier
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
  );
}