
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function VpnSecurityPage() {
  const [filters, setFilters] = useState({
    type: '',
    duration: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const securityProducts = [
    {
      id: 1,
      name: 'NordVPN 2 ans',
      price: '79.99',
      originalPrice: '287.76',
      type: 'VPN',
      duration: '2 ans',
      image: 'https://readdy.ai/api/search-image?query=NordVPN%202%20years%20subscription%20security%20VPN%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20NordVPN%20colors%20clean%20minimal%20design%20with%20NordVPN%20logo&width=300&height=200&seq=nordvpn-2y&orientation=landscape',
      badge: 'MEGA DEAL',
      badgeColor: 'bg-blue-600',
      delivery: '5-15 min',
      rating: 4.8,
      stock: 'En stock',
      description: 'VPN premium avec sécurité militaire',
      features: ['6 appareils', '5400+ serveurs', 'Kill Switch', 'No logs', 'CyberSec', 'P2P']
    },
    {
      id: 2,
      name: 'ExpressVPN 1 an',
      price: '99.99',
      originalPrice: '155.88',
      type: 'VPN',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=ExpressVPN%201%20year%20subscription%20security%20VPN%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20red%20ExpressVPN%20colors%20clean%20minimal%20design%20with%20ExpressVPN%20logo&width=300&height=200&seq=expressvpn-1y&orientation=landscape',
      badge: 'PREMIUM',
      badgeColor: 'bg-red-600',
      delivery: 'Instantané',
      rating: 4.9,
      stock: 'En stock',
      description: 'VPN le plus rapide au monde',
      features: ['5 appareils', '3000+ serveurs', 'Vitesse max', 'Netflix', 'Tor', 'Split tunneling']
    },
    {
      id: 3,
      name: 'Surfshark 2 ans',
      price: '59.99',
      originalPrice: '287.76',
      type: 'VPN',
      duration: '2 ans',
      image: 'https://readdy.ai/api/search-image?query=Surfshark%202%20years%20subscription%20security%20VPN%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20teal%20Surfshark%20colors%20clean%20minimal%20design%20with%20Surfshark%20logo&width=300&height=200&seq=surfshark-2y&orientation=landscape',
      badge: 'APPAREILS ILLIMITÉS',
      badgeColor: 'bg-teal-600',
      delivery: '10-20 min',
      rating: 4.7,
      stock: 'En stock',
      description: 'VPN avec appareils illimités',
      features: ['Appareils illimités', '3200+ serveurs', 'MultiHop', 'CleanWeb', 'Whitelister', 'GPS spoofing']
    },
    {
      id: 4,
      name: 'CyberGhost 3 ans',
      price: '89.99',
      originalPrice: '431.64',
      type: 'VPN',
      duration: '3 ans',
      image: 'https://readdy.ai/api/search-image?query=CyberGhost%203%20years%20subscription%20security%20VPN%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20yellow%20CyberGhost%20colors%20clean%20minimal%20design%20with%20CyberGhost%20logo&width=300&height=200&seq=cyberghost-3y&orientation=landscape',
      badge: 'LONGUE DURÉE',
      badgeColor: 'bg-yellow-600',
      delivery: '15-30 min',
      rating: 4.6,
      stock: 'En stock',
      description: 'VPN simple et efficace',
      features: ['7 appareils', '9000+ serveurs', 'Streaming', 'Torrenting', 'Ad blocker', 'Antivirus']
    },
    {
      id: 5,
      name: 'Bitdefender Total Security',
      price: '39.99',
      originalPrice: '89.99',
      type: 'Antivirus',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Bitdefender%20Total%20Security%20antivirus%20software%20license%20digital%20key%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20Bitdefender%20colors%20clean%20minimal%20design%20with%20Bitdefender%20logo&width=300&height=200&seq=bitdefender-total&orientation=landscape',
      badge: 'PROTECTION TOTALE',
      badgeColor: 'bg-orange-600',
      delivery: 'Instantané',
      rating: 4.8,
      stock: 'En stock',
      description: 'Protection antivirus complète',
      features: ['5 appareils', 'Firewall', 'VPN intégré', 'Contrôle parental', 'Anti-phishing', 'Optimisation']
    },
    {
      id: 6,
      name: 'Norton 360 Premium',
      price: '49.99',
      originalPrice: '109.99',
      type: 'Antivirus',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Norton%20360%20Premium%20antivirus%20security%20software%20license%20digital%20key%20dark%20background%20professional%20e-commerce%20product%20image%20yellow%20Norton%20colors%20clean%20minimal%20design%20with%20Norton%20logo&width=300&height=200&seq=norton-360-premium&orientation=landscape',
      badge: 'PREMIUM',
      badgeColor: 'bg-yellow-500',
      delivery: 'Instantané',
      rating: 4.7,
      stock: 'En stock',
      description: 'Sécurité Norton 360 Premium',
      features: ['10 appareils', 'VPN illimité', 'Dark Web', 'Sauvegarde 75GB', 'Contrôle parental', 'SafeCam']
    },
    {
      id: 7,
      name: 'Kaspersky Internet Security',
      price: '29.99',
      originalPrice: '59.99',
      type: 'Antivirus',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Kaspersky%20Internet%20Security%20antivirus%20software%20license%20digital%20key%20dark%20background%20professional%20e-commerce%20product%20image%20green%20Kaspersky%20colors%20clean%20minimal%20design%20with%20Kaspersky%20logo&width=300&height=200&seq=kaspersky-internet&orientation=landscape',
      badge: 'SÉCURITÉ',
      badgeColor: 'bg-green-600',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Protection Internet avancée',
      features: ['3 appareils', 'Firewall', 'Anti-spam', 'Paiements sécurisés', 'Contrôle parental', 'Webcam protection']
    },
    {
      id: 8,
      name: 'Avast Premium Security',
      price: '34.99',
      originalPrice: '69.99',
      type: 'Antivirus',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Avast%20Premium%20Security%20antivirus%20software%20license%20digital%20key%20dark%20background%20professional%20e-commerce%20product%20image%20orange%20Avast%20colors%20clean%20minimal%20design%20with%20Avast%20logo&width=300&height=200&seq=avast-premium&orientation=landscape',
      badge: 'PREMIUM',
      badgeColor: 'bg-orange-500',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Sécurité Avast Premium',
      features: ['10 appareils', 'Firewall', 'Anti-spam', 'Sandbox', 'WiFi security', 'Webcam shield']
    },
    {
      id: 9,
      name: 'Private Internet Access VPN',
      price: '69.99',
      originalPrice: '119.88',
      type: 'VPN',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Private%20Internet%20Access%20VPN%201%20year%20subscription%20security%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20green%20PIA%20colors%20clean%20minimal%20design%20with%20PIA%20logo&width=300&height=200&seq=pia-vpn-1y&orientation=landscape',
      badge: 'PRIVÉ',
      badgeColor: 'bg-green-500',
      delivery: '10-20 min',
      rating: 4.4,
      stock: 'En stock',
      description: 'VPN axé sur la confidentialité',
      features: ['10 appareils', 'No logs', 'Kill Switch', 'SOCKS5 proxy', 'Port forwarding', 'Open source']
    },
    {
      id: 10,
      name: 'Proton VPN Plus',
      price: '79.99',
      originalPrice: '119.88',
      type: 'VPN',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Proton%20VPN%20Plus%201%20year%20subscription%20security%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20purple%20Proton%20colors%20clean%20minimal%20design%20with%20Proton%20logo&width=300&height=200&seq=proton-vpn-plus&orientation=landscape',
      badge: 'SÉCURISÉ',
      badgeColor: 'bg-purple-600',
      delivery: '15-25 min',
      rating: 4.5,
      stock: 'En stock',
      description: 'VPN ultra-sécurisé de Proton',
      features: ['10 appareils', 'Secure Core', 'Tor over VPN', 'NetShield', 'Streaming', 'P2P']
    },
    {
      id: 11,
      name: 'McAfee Total Protection',
      price: '44.99',
      originalPrice: '89.99',
      type: 'Antivirus',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=McAfee%20Total%20Protection%20antivirus%20security%20software%20license%20digital%20key%20dark%20background%20professional%20e-commerce%20product%20image%20red%20McAfee%20colors%20clean%20minimal%20design%20with%20McAfee%20logo&width=300&height=200&seq=mcafee-total&orientation=landscape',
      badge: 'PROTECTION',
      badgeColor: 'bg-red-500',
      delivery: 'Instantané',
      rating: 4.3,
      stock: 'En stock',
      description: 'Protection McAfee complète',
      features: ['Appareils illimités', 'Firewall', 'VPN', 'Gestionnaire mots de passe', 'Shredder', 'Anti-spam']
    },
    {
      id: 12,
      name: 'Windscribe VPN Pro',
      price: '54.99',
      originalPrice: '108.00',
      type: 'VPN',
      duration: '1 an',
      image: 'https://readdy.ai/api/search-image?query=Windscribe%20VPN%20Pro%201%20year%20subscription%20security%20service%20digital%20license%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Windscribe%20colors%20clean%20minimal%20design%20with%20Windscribe%20logo&width=300&height=200&seq=windscribe-pro&orientation=landscape',
      badge: 'PRO',
      badgeColor: 'bg-blue-500',
      delivery: '10-15 min',
      rating: 4.4,
      stock: 'En stock',
      description: 'VPN innovant avec R.O.B.E.R.T.',
      features: ['Appareils illimités', 'R.O.B.E.R.T.', 'Windflix', 'Port forwarding', 'Split tunneling', 'Double VPN']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = securityProducts.filter(product => {
    if (filters.type && product.type !== filters.type) return false;
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
      
      <div className="bg-gradient-to-r from-indigo-900 to-purple-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            VPN & <span className="bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Sécurité</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Protégez votre vie privée en ligne. VPN premium, antivirus et solutions de sécurité !
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
                  <h4 className="font-semibold text-white mb-3">Type</h4>
                  <div className="space-y-2">
                    {['VPN', 'Antivirus'].map(type => (
                      <label key={type} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="type"
                          value={type}
                          checked={filters.type === type}
                          onChange={(e) => setFilters({...filters, type: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{type}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Durée</h4>
                  <div className="space-y-2">
                    {['1 an', '2 ans', '3 ans'].map(duration => (
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
                  onClick={() => setFilters({type: '', duration: '', sortBy: 'popular'})}
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
                {sortedProducts.length} solutions sécurité
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
                      <i className="ri-shield-check-line mr-1"></i>
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
                    <h3 className="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors">
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
                      <span className="text-sm text-indigo-400">
                        {product.duration}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
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
