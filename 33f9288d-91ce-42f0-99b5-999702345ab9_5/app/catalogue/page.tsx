
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import Link from 'next/link';
import { useState } from 'react';

export default function CataloguePage() {
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');

  const categories = [
    {
      id: 'gaming',
      name: 'Cartes Gaming',
      description: 'Steam, PlayStation, Xbox, Nintendo',
      icon: 'ri-gamepad-line',
      link: '/cartes-gaming',
      gradient: 'from-purple-500 to-blue-500',
      count: '250+',
      popular: ['Steam 50€', 'PlayStation Plus', 'Xbox Game Pass']
    },
    {
      id: 'streaming',
      name: 'Streaming',
      description: 'Netflix, Spotify, YouTube Premium',
      icon: 'ri-play-circle-line',
      link: '/streaming',
      gradient: 'from-red-500 to-pink-500',
      count: '50+',
      popular: ['Netflix Premium', 'Spotify Premium', 'Disney+']
    },
    {
      id: 'software',
      name: 'Logiciels',
      description: 'Windows, Office, Adobe, Antivirus',
      icon: 'ri-computer-line',
      link: '/logiciels',
      gradient: 'from-green-500 to-blue-500',
      count: '180+',
      popular: ['Office 2021', 'Windows 11', 'Adobe CC']
    },
    {
      id: 'giftcards',
      name: 'Cartes Prépayées',
      description: 'Amazon, iTunes, Google Play',
      icon: 'ri-gift-2-line',
      link: '/cartes-prepayees',
      gradient: 'from-orange-500 to-yellow-500',
      count: '100+',
      popular: ['Amazon 25€', 'iTunes 15€', 'Google Play']
    },
    {
      id: 'crypto',
      name: 'Cryptomonnaies',
      description: 'Bitcoin, Ethereum, codes crypto',
      icon: 'ri-bit-coin-line',
      link: '/crypto',
      gradient: 'from-yellow-500 to-orange-500',
      count: '20+',
      popular: ['Bitcoin 0.001', 'Ethereum 0.02', 'Binance Coin']
    },
    {
      id: 'security',
      name: 'VPN & Sécurité',
      description: 'NordVPN, ExpressVPN, antivirus',
      icon: 'ri-shield-check-line',
      link: '/vpn-securite',
      gradient: 'from-indigo-500 to-purple-500',
      count: '30+',
      popular: ['NordVPN 2 ans', 'ExpressVPN', 'Norton 360']
    }
  ];

  const featuredProducts = [
    {
      id: 1,
      name: 'Microsoft Office 2021 Pro',
      price: '49.99',
      originalPrice: '439.99',
      category: 'software',
      image: 'https://readdy.ai/api/search-image?query=Microsoft%20Office%202021%20Professional%20software%20license%20productivity%20suite%20digital%20key%20dark%20background%20blue%20Microsoft%20colors%20business%20professional%20product%20image&width=250&height=180&seq=office-2021-featured&orientation=landscape',
      badge: 'MEGA PROMO',
      badgeColor: 'bg-red-600',
      link: '/logiciels'
    },
    {
      id: 2,
      name: 'Netflix Premium 6 mois',
      price: '89.99',
      originalPrice: '95.94',
      category: 'streaming',
      image: 'https://readdy.ai/api/search-image?query=Netflix%20Premium%20subscription%206%20months%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20professional%20product%20image%20with%20Netflix%20logo&width=250&height=180&seq=netflix-6m-featured&orientation=landscape',
      badge: 'ÉCONOMIE',
      badgeColor: 'bg-green-500',
      link: '/streaming'
    },
    {
      id: 3,
      name: 'Steam Gift Card 50€',
      price: '50.00',
      originalPrice: '52.00',
      category: 'gaming',
      image: 'https://readdy.ai/api/search-image?query=Steam%20gift%20card%2050%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design&width=250&height=180&seq=steam-50-featured&orientation=landscape',
      badge: 'POPULAIRE',
      badgeColor: 'bg-blue-500',
      link: '/cartes-gaming'
    },
    {
      id: 4,
      name: 'NordVPN 2 ans',
      price: '79.99',
      originalPrice: '287.76',
      category: 'security',
      image: 'https://readdy.ai/api/search-image?query=NordVPN%202%20years%20subscription%20security%20VPN%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20NordVPN%20colors&width=250&height=180&seq=nordvpn-2y-featured&orientation=landscape',
      badge: 'BEST DEAL',
      badgeColor: 'bg-purple-600',
      link: '/vpn-securite'
    },
    {
      id: 5,
      name: 'Amazon Gift Card 25€',
      price: '25.00',
      originalPrice: '25.00',
      category: 'giftcards',
      image: 'https://readdy.ai/api/search-image?query=Amazon%20Gift%20Card%2025%20euros%20digital%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20Amazon%20colors%20clean%20minimal%20design&width=250&height=180&seq=amazon-25-featured&orientation=landscape',
      badge: 'POPULAIRE',
      badgeColor: 'bg-orange-500',
      link: '/cartes-prepayees'
    },
    {
      id: 6,
      name: 'Bitcoin 0.001 BTC',
      price: '45.00',
      originalPrice: '47.00',
      category: 'crypto',
      image: 'https://readdy.ai/api/search-image?query=Bitcoin%20BTC%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20gold%20Bitcoin%20colors%20clean%20minimal%20design&width=250&height=180&seq=bitcoin-featured&orientation=landscape',
      badge: 'CRYPTO',
      badgeColor: 'bg-yellow-500',
      link: '/crypto'
    }
  ];

  const filteredCategories = categories.filter(category => 
    selectedCategory === 'all' || category.id === selectedCategory
  );

  const filteredProducts = featuredProducts.filter(product => 
    (selectedCategory === 'all' || product.category === selectedCategory) &&
    (searchTerm === '' || product.name.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  return (
    <div className="min-h-screen bg-gray-900">
      <Navigation />
      
      <div className="bg-gradient-to-r from-gray-800 to-gray-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Catalogue <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Complet</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
            Découvrez notre catalogue complet de produits numériques avec livraison instantanée
          </p>
          
          {/* Barre de recherche */}
          <div className="max-w-2xl mx-auto mb-8">
            <div className="relative">
              <input
                type="text"
                placeholder="Rechercher un produit..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full bg-gray-800 border border-gray-700 rounded-xl px-6 py-4 pl-12 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <div className="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 flex items-center justify-center">
                <i className="ri-search-line text-gray-400"></i>
              </div>
            </div>
          </div>

          {/* Filtres par catégorie */}
          <div className="flex flex-wrap justify-center gap-3 mb-8">
            <button
              onClick={() => setSelectedCategory('all')}
              className={`px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer ${
                selectedCategory === 'all'
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
              }`}
            >
              Toutes les catégories
            </button>
            {categories.map(category => (
              <button
                key={category.id}
                onClick={() => setSelectedCategory(category.id)}
                className={`px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer ${
                  selectedCategory === category.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                }`}
              >
                <i className={`${category.icon} mr-2`}></i>
                {category.name}
              </button>
            ))}
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-12">
        {/* Statistiques */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
          <div className="bg-gray-800 rounded-xl p-6 text-center border border-gray-700">
            <div className="text-3xl font-bold text-blue-400 mb-2">500K+</div>
            <div className="text-gray-400">Clients satisfaits</div>
          </div>
          <div className="bg-gray-800 rounded-xl p-6 text-center border border-gray-700">
            <div className="text-3xl font-bold text-green-400 mb-2">630+</div>
            <div className="text-gray-400">Produits disponibles</div>
          </div>
          <div className="bg-gray-800 rounded-xl p-6 text-center border border-gray-700">
            <div className="text-3xl font-bold text-purple-400 mb-2">24/7</div>
            <div className="text-gray-400">Support client</div>
          </div>
          <div className="bg-gray-800 rounded-xl p-6 text-center border border-gray-700">
            <div className="text-3xl font-bold text-yellow-400 mb-2">< 5min</div>
            <div className="text-gray-400">Livraison moyenne</div>
          </div>
        </div>

        {/* Grille des catégories */}
        <section className="mb-16">
          <h2 className="text-3xl font-bold text-white mb-8 text-center">
            Explorez nos catégories
          </h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredCategories.map((category) => (
              <Link
                key={category.id}
                href={category.link}
                className="group bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl"
              >
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <div className={`w-12 h-12 bg-gradient-to-r ${category.gradient} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300`}>
                      <i className={`${category.icon} text-white text-xl`}></i>
                    </div>
                    <span className="text-sm text-gray-400 bg-gray-700 px-3 py-1 rounded-full">
                      {category.count} produits
                    </span>
                  </div>
                  
                  <div>
                    <h3 className="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                      {category.name}
                    </h3>
                    <p className="text-gray-400 text-sm leading-relaxed mb-3">
                      {category.description}
                    </p>
                    <div className="text-xs text-gray-500">
                      Populaires: {category.popular.join(', ')}
                    </div>
                  </div>
                  
                  <div className="flex items-center text-blue-400 text-sm font-medium">
                    <span>Explorer</span>
                    <i className="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform"></i>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </section>

        {/* Produits populaires */}
        <section>
          <h2 className="text-3xl font-bold text-white mb-8 text-center">
            Produits populaires
          </h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredProducts.map((product) => (
              <Link
                key={product.id}
                href={product.link}
                className="group bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
              >
                <div className="relative">
                  <img
                    src={product.image}
                    alt={product.name}
                    className="w-full h-48 object-cover rounded-t-xl"
                  />
                  <span className={`absolute top-3 left-3 ${product.badgeColor} text-white px-3 py-1 rounded-full text-sm font-bold`}>
                    {product.badge}
                  </span>
                </div>
                <div className="p-6">
                  <h3 className="text-xl font-bold text-white mb-3 group-hover:text-blue-400 transition-colors">
                    {product.name}
                  </h3>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-2">
                      <span className="text-2xl font-bold text-white">€{product.price}</span>
                      {product.originalPrice !== product.price && (
                        <span className="text-sm text-gray-400 line-through">€{product.originalPrice}</span>
                      )}
                    </div>
                    <div className="flex items-center text-blue-400 text-sm font-medium">
                      <span>Voir</span>
                      <i className="ri-arrow-right-line ml-1 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </section>

        {/* Call to action */}
        <div className="text-center mt-16">
          <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-center">
            <h3 className="text-2xl font-bold text-white mb-4">
              Vous ne trouvez pas ce que vous cherchez ?
            </h3>
            <p className="text-blue-100 mb-6">
              Contactez notre équipe pour des produits personnalisés ou des commandes en gros
            </p>
            <button className="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors whitespace-nowrap cursor-pointer">
              Nous contacter
            </button>
          </div>
        </div>
      </div>

      <FooterDigital />
    </div>
  );
}
