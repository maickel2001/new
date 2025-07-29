
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function CartesPrePayeesPage() {
  const [filters, setFilters] = useState({
    brand: '',
    amount: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const giftCardProducts = [
    {
      id: 1,
      name: 'Amazon Gift Card 25€',
      price: '25.00',
      originalPrice: '25.00',
      brand: 'Amazon',
      amount: '25€',
      image: 'https://readdy.ai/api/search-image?query=Amazon%20Gift%20Card%2025%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20Amazon%20colors%20clean%20minimal%20design%20with%20Amazon%20logo&width=300&height=200&seq=amazon-gift-25&orientation=landscape',
      badge: 'POPULAIRE',
      badgeColor: 'bg-orange-500',
      delivery: 'Instantané',
      rating: 4.9,
      stock: 'En stock',
      description: 'Utilisable sur Amazon.fr pour tous les achats',
      features: ['Pas d\'expiration', 'Cumulable', 'Tous produits Amazon', 'Livraison gratuite']
    },
    {
      id: 2,
      name: 'iTunes Gift Card 15€',
      price: '15.00',
      originalPrice: '15.00',
      brand: 'Apple',
      amount: '15€',
      image: 'https://readdy.ai/api/search-image?query=iTunes%20Apple%20Gift%20Card%2015%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Apple%20colors%20clean%20minimal%20design%20with%20Apple%20logo&width=300&height=200&seq=itunes-gift-15&orientation=landscape',
      badge: 'APPLE',
      badgeColor: 'bg-blue-500',
      delivery: 'Instantané',
      rating: 4.8,
      stock: 'En stock',
      description: 'App Store, iTunes, Apple Music et plus',
      features: ['App Store', 'iTunes', 'Apple Music', 'iCloud', 'Apple Books', 'Apple TV+']
    },
    {
      id: 3,
      name: 'Google Play Gift Card 10€',
      price: '10.00',
      originalPrice: '10.00',
      brand: 'Google',
      amount: '10€',
      image: 'https://readdy.ai/api/search-image?query=Google%20Play%20Gift%20Card%2010%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20green%20Google%20colors%20clean%20minimal%20design%20with%20Google%20Play%20logo&width=300&height=200&seq=google-play-10&orientation=landscape',
      badge: 'ANDROID',
      badgeColor: 'bg-green-500',
      delivery: 'Instantané',
      rating: 4.7,
      stock: 'En stock',
      description: 'Applications, jeux, films et plus sur Google Play',
      features: ['Apps & Jeux', 'Films & Séries', 'Livres', 'Musique', 'Abonnements', 'Achats in-app']
    },
    {
      id: 4,
      name: 'Zalando Gift Card 50€',
      price: '50.00',
      originalPrice: '50.00',
      brand: 'Zalando',
      amount: '50€',
      image: 'https://readdy.ai/api/search-image?query=Zalando%20Gift%20Card%2050%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20Zalando%20colors%20clean%20minimal%20design%20with%20Zalando%20logo&width=300&height=200&seq=zalando-gift-50&orientation=landscape',
      badge: 'MODE',
      badgeColor: 'bg-orange-600',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Mode et lifestyle sur Zalando',
      features: ['Mode femme', 'Mode homme', 'Enfants', 'Chaussures', 'Accessoires', 'Beauté']
    },
    {
      id: 5,
      name: 'FNAC Gift Card 30€',
      price: '30.00',
      originalPrice: '30.00',
      brand: 'FNAC',
      amount: '30€',
      image: 'https://readdy.ai/api/search-image?query=FNAC%20Gift%20Card%2030%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20yellow%20FNAC%20colors%20clean%20minimal%20design%20with%20FNAC%20logo&width=300&height=200&seq=fnac-gift-30&orientation=landscape',
      badge: 'CULTURE',
      badgeColor: 'bg-yellow-500',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Livres, high-tech, culture et plus',
      features: ['Livres', 'High-tech', 'Musique', 'Films', 'Jeux vidéo', 'Billetterie']
    },
    {
      id: 6,
      name: 'Uber Gift Card 20€',
      price: '20.00',
      originalPrice: '20.00',
      brand: 'Uber',
      amount: '20€',
      image: 'https://readdy.ai/api/search-image?query=Uber%20Gift%20Card%2020%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20black%20Uber%20colors%20clean%20minimal%20design%20with%20Uber%20logo&width=300&height=200&seq=uber-gift-20&orientation=landscape',
      badge: 'TRANSPORT',
      badgeColor: 'bg-black',
      delivery: 'Instantané',
      rating: 4.4,
      stock: 'En stock',
      description: 'Trajets Uber et livraisons Uber Eats',
      features: ['Uber trajets', 'Uber Eats', 'Uber One', 'Réservation', 'Priorité', 'Annulation']
    },
    {
      id: 7,
      name: 'Spotify Gift Card 30€',
      price: '30.00',
      originalPrice: '30.00',
      brand: 'Spotify',
      amount: '30€',
      image: 'https://readdy.ai/api/search-image?query=Spotify%20Gift%20Card%2030%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20green%20Spotify%20colors%20clean%20minimal%20design%20with%20Spotify%20logo&width=300&height=200&seq=spotify-gift-30&orientation=landscape',
      badge: 'MUSIQUE',
      badgeColor: 'bg-green-600',
      delivery: 'Instantané',
      rating: 4.8,
      stock: 'En stock',
      description: 'Abonnement Premium Spotify',
      features: ['Sans publicité', 'Écoute hors ligne', 'Qualité haute', 'Lecture illimitée', 'Podcasts', 'Playlists']
    },
    {
      id: 8,
      name: 'Steam Gift Card 20€',
      price: '20.00',
      originalPrice: '20.00',
      brand: 'Steam',
      amount: '20€',
      image: 'https://readdy.ai/api/search-image?query=Steam%20Gift%20Card%2020%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Steam%20colors%20clean%20minimal%20design%20with%20Steam%20logo&width=300&height=200&seq=steam-gift-20&orientation=landscape',
      badge: 'GAMING',
      badgeColor: 'bg-blue-600',
      delivery: 'Instantané',
      rating: 4.9,
      stock: 'En stock',
      description: 'Jeux PC et contenu Steam',
      features: ['Jeux PC', 'DLC', 'Objets Steam', 'Portefeuille Steam', 'Marketplace', 'Workshop']
    },
    {
      id: 9,
      name: 'JustEat Gift Card 40€',
      price: '40.00',
      originalPrice: '40.00',
      brand: 'JustEat',
      amount: '40€',
      image: 'https://readdy.ai/api/search-image?query=JustEat%20Gift%20Card%2040%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20JustEat%20colors%20clean%20minimal%20design%20with%20JustEat%20logo&width=300&height=200&seq=justeat-gift-40&orientation=landscape',
      badge: 'FOOD',
      badgeColor: 'bg-orange-500',
      delivery: 'Instantané',
      rating: 4.3,
      stock: 'En stock',
      description: 'Livraisons de repas JustEat',
      features: ['Restaurants', 'Livraison rapide', 'Cuisines variées', 'Promotions', 'Cashback', 'Suivi commande']
    },
    {
      id: 10,
      name: 'Deliveroo Gift Card 25€',
      price: '25.00',
      originalPrice: '25.00',
      brand: 'Deliveroo',
      amount: '25€',
      image: 'https://readdy.ai/api/search-image?query=Deliveroo%20Gift%20Card%2025%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20teal%20Deliveroo%20colors%20clean%20minimal%20design%20with%20Deliveroo%20logo&width=300&height=200&seq=deliveroo-gift-25&orientation=landscape',
      badge: 'FOOD',
      badgeColor: 'bg-teal-500',
      delivery: 'Instantané',
      rating: 4.4,
      stock: 'En stock',
      description: 'Livraisons Deliveroo premium',
      features: ['Restaurants premium', 'Livraison ultra rapide', 'Deliveroo Plus', 'Épicerie', 'Desserts', 'Alcohol']
    },
    {
      id: 11,
      name: 'Sephora Gift Card 60€',
      price: '60.00',
      originalPrice: '60.00',
      brand: 'Sephora',
      amount: '60€',
      image: 'https://readdy.ai/api/search-image?query=Sephora%20Gift%20Card%2060%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20black%20Sephora%20colors%20clean%20minimal%20design%20with%20Sephora%20logo&width=300&height=200&seq=sephora-gift-60&orientation=landscape',
      badge: 'BEAUTÉ',
      badgeColor: 'bg-black',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Cosmétiques et parfums Sephora',
      features: ['Maquillage', 'Parfums', 'Soins visage', 'Soins corps', 'Cheveux', 'Marques premium']
    },
    {
      id: 12,
      name: 'Decathlon Gift Card 75€',
      price: '75.00',
      originalPrice: '75.00',
      brand: 'Decathlon',
      amount: '75€',
      image: 'https://readdy.ai/api/search-image?query=Decathlon%20Gift%20Card%2075%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Decathlon%20colors%20clean%20minimal%20design%20with%20Decathlon%20logo&width=300&height=200&seq=decathlon-gift-75&orientation=landscape',
      badge: 'SPORT',
      badgeColor: 'bg-blue-700',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Équipements sportifs Decathlon',
      features: ['Tous sports', 'Vêtements', 'Équipements', 'Chaussures', 'Outdoor', 'Fitness']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = giftCardProducts.filter(product => {
    if (filters.brand && product.brand !== filters.brand) return false;
    if (filters.amount && product.amount !== filters.amount) return false;
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
      
      <div className="bg-gradient-to-r from-orange-900 to-yellow-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Cartes <span className="bg-gradient-to-r from-orange-400 to-yellow-400 bg-clip-text text-transparent">Prépayées</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Cartes cadeaux des plus grandes marques. Amazon, Apple, Google, Spotify et bien plus !
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
                  <h4 className="font-semibold text-white mb-3">Marque</h4>
                  <div className="space-y-2">
                    {['Amazon', 'Apple', 'Google', 'Spotify', 'Steam', 'Zalando'].map(brand => (
                      <label key={brand} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="brand"
                          value={brand}
                          checked={filters.brand === brand}
                          onChange={(e) => setFilters({...filters, brand: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{brand}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Montant</h4>
                  <div className="space-y-2">
                    {['10€', '15€', '20€', '25€', '30€', '50€'].map(amount => (
                      <label key={amount} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="amount"
                          value={amount}
                          checked={filters.amount === amount}
                          onChange={(e) => setFilters({...filters, amount: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{amount}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({brand: '', amount: '', sortBy: 'popular'})}
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
                {sortedProducts.length} cartes disponibles
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
                      </div>
                      <div className="flex items-center space-x-1">
                        <div className="w-4 h-4 flex items-center justify-center">
                          <i className="ri-star-fill text-yellow-500"></i>
                        </div>
                        <span className="text-sm text-gray-300">{product.rating}</span>
                      </div>
                    </div>

                    <div className="mb-4">
                      <div className="flex flex-wrap gap-1">
                        {product.features.slice(0, 3).map((feature, index) => (
                          <span key={index} className="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded">
                            {feature}
                          </span>
                        ))}
                      </div>
                    </div>

                    <div className="flex items-center justify-between mb-4">
                      <span className="text-sm bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                        {product.stock}
                      </span>
                      <span className="text-sm text-orange-400">
                        {product.amount}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-orange-600 to-yellow-600 hover:from-orange-700 hover:to-yellow-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
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
