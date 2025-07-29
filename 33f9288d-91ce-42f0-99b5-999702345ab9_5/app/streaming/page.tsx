
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function StreamingPage() {
  const [filters, setFilters] = useState({
    service: '',
    duration: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const streamingProducts = [
    {
      id: 1,
      name: 'Netflix Premium 1 mois',
      price: '15.99',
      originalPrice: '17.99',
      service: 'Netflix',
      duration: '1 mois',
      image: 'https://readdy.ai/api/search-image?query=Netflix%20Premium%20subscription%201%20month%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20professional%20product%20image%20with%20Netflix%20logo&width=300&height=200&seq=netflix-1m-premium&orientation=landscape',
      badge: 'PREMIUM',
      badgeColor: 'bg-red-500',
      delivery: '5-15 min',
      rating: 4.8,
      stock: 'En stock',
      description: '4K Ultra HD, 4 écrans simultanés',
      features: ['4K Ultra HD', '4 écrans', 'Téléchargement', 'Pas de pub']
    },
    {
      id: 2,
      name: 'Netflix Premium 6 mois',
      price: '89.99',
      originalPrice: '95.94',
      service: 'Netflix',
      duration: '6 mois',
      image: 'https://readdy.ai/api/search-image?query=Netflix%20Premium%20subscription%206%20months%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20professional%20product%20image%20with%20Netflix%20logo&width=300&height=200&seq=netflix-6m-premium&orientation=landscape',
      badge: 'ÉCONOMIE',
      badgeColor: 'bg-green-500',
      delivery: 'Manuel',
      rating: 4.9,
      stock: 'En stock',
      description: 'Économisez 6€ avec cet abonnement longue durée',
      features: ['4K Ultra HD', '4 écrans', 'Téléchargement', 'Pas de pub']
    },
    {
      id: 3,
      name: 'Spotify Premium 3 mois',
      price: '29.97',
      originalPrice: '32.97',
      service: 'Spotify',
      duration: '3 mois',
      image: 'https://readdy.ai/api/search-image?query=Spotify%20Premium%20subscription%203%20months%20music%20streaming%20service%20digital%20access%20dark%20background%20green%20branding%20professional%20product%20image%20with%20Spotify%20logo&width=300&height=200&seq=spotify-3m-premium&orientation=landscape',
      badge: 'POPULAIRE',
      badgeColor: 'bg-blue-500',
      delivery: 'Instantané',
      rating: 4.7,
      stock: 'En stock',
      description: 'Musique illimitée sans publicité',
      features: ['Sans publicité', 'Téléchargement', 'Qualité haute', 'Lecture hors ligne']
    },
    {
      id: 4,
      name: 'YouTube Premium 1 mois',
      price: '11.99',
      originalPrice: '11.99',
      service: 'YouTube',
      duration: '1 mois',
      image: 'https://readdy.ai/api/search-image?query=YouTube%20Premium%20subscription%201%20month%20video%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20professional%20product%20image%20with%20YouTube%20logo&width=300&height=200&seq=youtube-1m-premium&orientation=landscape',
      badge: null,
      badgeColor: '',
      delivery: '1-2h',
      rating: 4.6,
      stock: 'En stock',
      description: 'Vidéos sans pub + YouTube Music',
      features: ['Sans publicité', 'Lecture en arrière-plan', 'YouTube Music', 'Téléchargement']
    },
    {
      id: 5,
      name: 'Disney+ 12 mois',
      price: '89.90',
      originalPrice: '89.90',
      service: 'Disney+',
      duration: '12 mois',
      image: 'https://readdy.ai/api/search-image?query=Disney%20Plus%20subscription%2012%20months%20streaming%20service%20digital%20access%20dark%20background%20blue%20branding%20professional%20product%20image%20with%20Disney%20logo%20family%20entertainment&width=300&height=200&seq=disney-12m-annual&orientation=landscape',
      badge: 'ANNUEL',
      badgeColor: 'bg-purple-500',
      delivery: 'Manuel',
      rating: 4.5,
      stock: 'En stock',
      description: 'Tout l\'univers Disney, Marvel, Star Wars',
      features: ['4K Ultra HD', '4 écrans', 'Téléchargement', 'Contenu exclusif']
    },
    {
      id: 6,
      name: 'Amazon Prime Video 1 mois',
      price: '5.99',
      originalPrice: '5.99',
      service: 'Prime Video',
      duration: '1 mois',
      image: 'https://readdy.ai/api/search-image?query=Amazon%20Prime%20Video%20subscription%201%20month%20streaming%20service%20digital%20access%20dark%20background%20blue%20branding%20professional%20product%20image%20with%20Amazon%20logo&width=300&height=200&seq=prime-video-1m&orientation=landscape',
      badge: 'ABORDABLE',
      badgeColor: 'bg-orange-500',
      delivery: 'Instantané',
      rating: 4.4,
      stock: 'En stock',
      description: 'Films et séries Amazon Originals',
      features: ['HD/4K', '3 écrans', 'Téléchargement', 'X-Ray']
    },
    {
      id: 7,
      name: 'HBO Max 6 mois',
      price: '89.94',
      originalPrice: '95.94',
      service: 'HBO Max',
      duration: '6 mois',
      image: 'https://readdy.ai/api/search-image?query=HBO%20Max%20subscription%206%20months%20streaming%20service%20digital%20access%20dark%20background%20purple%20branding%20professional%20product%20image%20with%20HBO%20logo&width=300&height=200&seq=hbo-max-6m&orientation=landscape',
      badge: 'PREMIUM',
      badgeColor: 'bg-purple-600',
      delivery: 'Manuel',
      rating: 4.7,
      stock: 'Stock limité',
      description: 'Séries et films HBO de prestige',
      features: ['4K Ultra HD', '3 écrans', 'Téléchargement', 'Contenu exclusif']
    },
    {
      id: 8,
      name: 'Apple TV+ 3 mois',
      price: '20.97',
      originalPrice: '20.97',
      service: 'Apple TV+',
      duration: '3 mois',
      image: 'https://readdy.ai/api/search-image?query=Apple%20TV%20Plus%20subscription%203%20months%20streaming%20service%20digital%20access%20dark%20background%20gray%20branding%20professional%20product%20image%20with%20Apple%20logo&width=300&height=200&seq=apple-tv-3m&orientation=landscape',
      badge: null,
      badgeColor: '',
      delivery: 'Instantané',
      rating: 4.3,
      stock: 'En stock',
      description: 'Apple Originals et productions exclusives',
      features: ['4K HDR', '6 écrans', 'Téléchargement', 'Dolby Atmos']
    },
    {
      id: 9,
      name: 'Paramount+ 1 mois',
      price: '7.99',
      originalPrice: '7.99',
      service: 'Paramount+',
      duration: '1 mois',
      image: 'https://readdy.ai/api/search-image?query=Paramount%20Plus%20subscription%201%20month%20streaming%20service%20digital%20access%20dark%20background%20blue%20branding%20professional%20product%20image%20with%20Paramount%20logo&width=300&height=200&seq=paramount-1m&orientation=landscape',
      badge: 'NOUVEAU',
      badgeColor: 'bg-green-500',
      delivery: 'Instantané',
      rating: 4.2,
      stock: 'En stock',
      description: 'Films Paramount et séries CBS',
      features: ['HD/4K', '3 écrans', 'Téléchargement', 'Live TV']
    },
    {
      id: 10,
      name: 'Crunchyroll Premium 12 mois',
      price: '79.99',
      originalPrice: '95.88',
      service: 'Crunchyroll',
      duration: '12 mois',
      image: 'https://readdy.ai/api/search-image?query=Crunchyroll%20Premium%20subscription%2012%20months%20anime%20streaming%20service%20digital%20access%20dark%20background%20orange%20branding%20professional%20product%20image%20with%20Crunchyroll%20logo&width=300&height=200&seq=crunchyroll-12m&orientation=landscape',
      badge: 'ANIME',
      badgeColor: 'bg-orange-500',
      delivery: 'Manuel',
      rating: 4.8,
      stock: 'En stock',
      description: 'Plus grand catalogue d\'anime au monde',
      features: ['Sans publicité', 'Simulcast', 'Téléchargement', 'Accès anticipé']
    },
    {
      id: 11,
      name: 'Deezer Premium 6 mois',
      price: '59.94',
      originalPrice: '65.94',
      service: 'Deezer',
      duration: '6 mois',
      image: 'https://readdy.ai/api/search-image?query=Deezer%20Premium%20subscription%206%20months%20music%20streaming%20service%20digital%20access%20dark%20background%20orange%20branding%20professional%20product%20image%20with%20Deezer%20logo&width=300&height=200&seq=deezer-6m-premium&orientation=landscape',
      badge: 'PROMO',
      badgeColor: 'bg-red-500',
      delivery: 'Instantané',
      rating: 4.4,
      stock: 'En stock',
      description: 'Musique HD et recommandations Flow',
      features: ['HiFi qualité', 'Sans publicité', 'Téléchargement', 'Flow personnalisé']
    },
    {
      id: 12,
      name: 'Twitch Turbo 3 mois',
      price: '26.97',
      originalPrice: '26.97',
      service: 'Twitch',
      duration: '3 mois',
      image: 'https://readdy.ai/api/search-image?query=Twitch%20Turbo%20subscription%203%20months%20gaming%20streaming%20service%20digital%20access%20dark%20background%20purple%20branding%20professional%20product%20image%20with%20Twitch%20logo&width=300&height=200&seq=twitch-turbo-3m&orientation=landscape',
      badge: 'GAMING',
      badgeColor: 'bg-purple-600',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Expérience Twitch sans publicité',
      features: ['Sans publicité', 'Emotes exclusives', 'Stockage étendu', 'Priorité support']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = streamingProducts.filter(product => {
    if (filters.service && product.service !== filters.service) return false;
    if (filters.duration && product.duration !== filters.duration) return false;
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
      
      <div className="bg-gradient-to-r from-red-900 to-purple-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Streaming & <span className="bg-gradient-to-r from-red-400 to-pink-400 bg-clip-text text-transparent">Divertissement</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Accédez à tous vos services de streaming favoris. Netflix, Spotify, Disney+, YouTube et bien plus !
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
                  <h4 className="font-semibold text-white mb-3">Service</h4>
                  <div className="space-y-2">
                    {['Netflix', 'Spotify', 'YouTube', 'Disney+', 'Prime Video', 'HBO Max'].map(service => (
                      <label key={service} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="service"
                          value={service}
                          checked={filters.service === service}
                          onChange={(e) => setFilters({...filters, service: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{service}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Durée</h4>
                  <div className="space-y-2">
                    {['1 mois', '3 mois', '6 mois', '12 mois'].map(duration => (
                      <label key={duration} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="duration"
                          value={duration}
                          checked={filters.duration === duration}
                          onChange={(e) => setFilters({...filters, duration: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{duration}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({service: '', duration: '', sortBy: 'popular'})}
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
                {sortedProducts.length} abonnements disponibles
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
                      <i className="ri-time-line mr-1"></i>
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
                      <span className="text-sm text-blue-400">
                        {product.duration}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
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
