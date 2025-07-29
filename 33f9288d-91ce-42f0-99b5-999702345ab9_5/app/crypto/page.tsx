
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function CryptoPage() {
  const [filters, setFilters] = useState({
    currency: '',
    amount: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const cryptoProducts = [
    {
      id: 1,
      name: 'Bitcoin (BTC) 0.001 BTC',
      price: '45.00',
      originalPrice: '47.00',
      currency: 'Bitcoin',
      amount: '0.001 BTC',
      image: 'https://readdy.ai/api/search-image?query=Bitcoin%20BTC%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20gold%20Bitcoin%20colors%20clean%20minimal%20design%20with%20Bitcoin%20logo&width=300&height=200&seq=bitcoin-0001&orientation=landscape',
      badge: 'POPULAIRE',
      badgeColor: 'bg-yellow-500',
      delivery: '10-30 min',
      rating: 4.8,
      stock: 'En stock',
      description: 'Crédit Bitcoin instantané dans votre wallet',
      features: ['Transfert rapide', 'Wallet sécurisé', 'Frais réduits', 'Support 24/7']
    },
    {
      id: 2,
      name: 'Ethereum (ETH) 0.02 ETH',
      price: '50.00',
      originalPrice: '52.00',
      currency: 'Ethereum',
      amount: '0.02 ETH',
      image: 'https://readdy.ai/api/search-image?query=Ethereum%20ETH%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Ethereum%20colors%20clean%20minimal%20design%20with%20Ethereum%20logo&width=300&height=200&seq=ethereum-002&orientation=landscape',
      badge: 'SMART CONTRACT',
      badgeColor: 'bg-blue-500',
      delivery: '15-45 min',
      rating: 4.7,
      stock: 'En stock',
      description: 'Ethereum pour DeFi et smart contracts',
      features: ['Smart contracts', 'DeFi ready', 'NFT compatible', 'Gas optimisé']
    },
    {
      id: 3,
      name: 'Binance Coin (BNB) 0.2 BNB',
      price: '60.00',
      originalPrice: '62.00',
      currency: 'Binance',
      amount: '0.2 BNB',
      image: 'https://readdy.ai/api/search-image?query=Binance%20Coin%20BNB%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20yellow%20Binance%20colors%20clean%20minimal%20design%20with%20Binance%20logo&width=300&height=200&seq=binance-02&orientation=landscape',
      badge: 'EXCHANGE',
      badgeColor: 'bg-yellow-600',
      delivery: '10-20 min',
      rating: 4.6,
      stock: 'En stock',
      description: 'Token natif de Binance Exchange',
      features: ['Frais trading réduits', 'Staking rewards', 'Launchpad access', 'BSC compatible']
    },
    {
      id: 4,
      name: 'Cardano (ADA) 50 ADA',
      price: '25.00',
      originalPrice: '27.00',
      currency: 'Cardano',
      amount: '50 ADA',
      image: 'https://readdy.ai/api/search-image?query=Cardano%20ADA%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Cardano%20colors%20clean%20minimal%20design%20with%20Cardano%20logo&width=300&height=200&seq=cardano-50&orientation=landscape',
      badge: 'ECO-FRIENDLY',
      badgeColor: 'bg-blue-600',
      delivery: '20-40 min',
      rating: 4.5,
      stock: 'En stock',
      description: 'Blockchain proof-of-stake écologique',
      features: ['Proof-of-stake', 'Faible consommation', 'Smart contracts', 'Gouvernance']
    },
    {
      id: 5,
      name: 'Solana (SOL) 1 SOL',
      price: '70.00',
      originalPrice: '72.00',
      currency: 'Solana',
      amount: '1 SOL',
      image: 'https://readdy.ai/api/search-image?query=Solana%20SOL%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20purple%20Solana%20colors%20clean%20minimal%20design%20with%20Solana%20logo&width=300&height=200&seq=solana-1&orientation=landscape',
      badge: 'RAPIDE',
      badgeColor: 'bg-purple-500',
      delivery: '5-15 min',
      rating: 4.4,
      stock: 'En stock',
      description: 'Blockchain ultra-rapide et scalable',
      features: ['Transactions rapides', 'Frais ultra-bas', 'NFT ecosystem', 'DeFi intégré']
    },
    {
      id: 6,
      name: 'Polygon (MATIC) 100 MATIC',
      price: '80.00',
      originalPrice: '82.00',
      currency: 'Polygon',
      amount: '100 MATIC',
      image: 'https://readdy.ai/api/search-image?query=Polygon%20MATIC%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20purple%20Polygon%20colors%20clean%20minimal%20design%20with%20Polygon%20logo&width=300&height=200&seq=polygon-100&orientation=landscape',
      badge: 'LAYER 2',
      badgeColor: 'bg-purple-600',
      delivery: '10-25 min',
      rating: 4.3,
      stock: 'En stock',
      description: 'Solution Layer 2 pour Ethereum',
      features: ['Layer 2 scaling', 'Ethereum compatible', 'Frais réduits', 'DApp friendly']
    },
    {
      id: 7,
      name: 'Chainlink (LINK) 5 LINK',
      price: '75.00',
      originalPrice: '77.00',
      currency: 'Chainlink',
      amount: '5 LINK',
      image: 'https://readdy.ai/api/search-image?query=Chainlink%20LINK%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Chainlink%20colors%20clean%20minimal%20design%20with%20Chainlink%20logo&width=300&height=200&seq=chainlink-5&orientation=landscape',
      badge: 'ORACLE',
      badgeColor: 'bg-blue-700',
      delivery: '15-30 min',
      rating: 4.5,
      stock: 'En stock',
      description: 'Oracle décentralisé pour smart contracts',
      features: ['Oracle network', 'Data feeds', 'VRF random', 'Cross-chain']
    },
    {
      id: 8,
      name: 'Dogecoin (DOGE) 200 DOGE',
      price: '20.00',
      originalPrice: '22.00',
      currency: 'Dogecoin',
      amount: '200 DOGE',
      image: 'https://readdy.ai/api/search-image?query=Dogecoin%20DOGE%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20yellow%20Dogecoin%20colors%20clean%20minimal%20design%20with%20Dogecoin%20logo&width=300&height=200&seq=dogecoin-200&orientation=landscape',
      badge: 'MEME',
      badgeColor: 'bg-yellow-500',
      delivery: '5-10 min',
      rating: 4.2,
      stock: 'En stock',
      description: 'La crypto meme préférée de la communauté',
      features: ['Communauté active', 'Transactions rapides', 'Frais très bas', 'Adoption large']
    },
    {
      id: 9,
      name: 'Litecoin (LTC) 0.5 LTC',
      price: '35.00',
      originalPrice: '37.00',
      currency: 'Litecoin',
      amount: '0.5 LTC',
      image: 'https://readdy.ai/api/search-image?query=Litecoin%20LTC%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20silver%20Litecoin%20colors%20clean%20minimal%20design%20with%20Litecoin%20logo&width=300&height=200&seq=litecoin-05&orientation=landscape',
      badge: 'ARGENT DIGITAL',
      badgeColor: 'bg-gray-500',
      delivery: '10-20 min',
      rating: 4.4,
      stock: 'En stock',
      description: 'L\'argent numérique, plus rapide que Bitcoin',
      features: ['Transactions rapides', 'Frais bas', 'Sécurité éprouvée', 'Adoption commerçants']
    },
    {
      id: 10,
      name: 'Ripple (XRP) 100 XRP',
      price: '30.00',
      originalPrice: '32.00',
      currency: 'Ripple',
      amount: '100 XRP',
      image: 'https://readdy.ai/api/search-image?query=Ripple%20XRP%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20Ripple%20colors%20clean%20minimal%20design%20with%20Ripple%20logo&width=300&height=200&seq=ripple-100&orientation=landscape',
      badge: 'PAIEMENTS',
      badgeColor: 'bg-blue-500',
      delivery: '5-15 min',
      rating: 4.3,
      stock: 'En stock',
      description: 'Paiements internationaux instantanés',
      features: ['Paiements rapides', 'Frais ultra-bas', 'Banques partenaires', 'Remittances']
    },
    {
      id: 11,
      name: 'Avalanche (AVAX) 2 AVAX',
      price: '65.00',
      originalPrice: '67.00',
      currency: 'Avalanche',
      amount: '2 AVAX',
      image: 'https://readdy.ai/api/search-image?query=Avalanche%20AVAX%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20red%20Avalanche%20colors%20clean%20minimal%20design%20with%20Avalanche%20logo&width=300&height=200&seq=avalanche-2&orientation=landscape',
      badge: 'CONSENSUS',
      badgeColor: 'bg-red-500',
      delivery: '10-30 min',
      rating: 4.4,
      stock: 'Stock limité',
      description: 'Plateforme de consensus révolutionnaire',
      features: ['Consensus unique', 'Subnets', 'DeFi native', 'Interoperabilité']
    },
    {
      id: 12,
      name: 'Polkadot (DOT) 5 DOT',
      price: '40.00',
      originalPrice: '42.00',
      currency: 'Polkadot',
      amount: '5 DOT',
      image: 'https://readdy.ai/api/search-image?query=Polkadot%20DOT%20cryptocurrency%20digital%20wallet%20voucher%20dark%20background%20professional%20e-commerce%20product%20image%20pink%20Polkadot%20colors%20clean%20minimal%20design%20with%20Polkadot%20logo&width=300&height=200&seq=polkadot-5&orientation=landscape',
      badge: 'INTERCHAIN',
      badgeColor: 'bg-pink-500',
      delivery: '15-25 min',
      rating: 4.5,
      stock: 'En stock',
      description: 'Blockchain des blockchains connectées',
      features: ['Parachains', 'Interoperabilité', 'Gouvernance', 'Staking rewards']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = cryptoProducts.filter(product => {
    if (filters.currency && product.currency !== filters.currency) return false;
    if (filters.amount) {
      const price = parseFloat(product.price);
      const [min, max] = filters.amount.split('-').map(Number);
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
      
      <div className="bg-gradient-to-r from-yellow-900 to-orange-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Crypto <span className="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">monnaies</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Achetez vos cryptomonnaies préférées instantanément. Bitcoin, Ethereum, Binance et plus !
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
                  <h4 className="font-semibold text-white mb-3">Cryptomonnaie</h4>
                  <div className="space-y-2">
                    {['Bitcoin', 'Ethereum', 'Binance', 'Cardano', 'Solana', 'Polygon'].map(currency => (
                      <label key={currency} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="currency"
                          value={currency}
                          checked={filters.currency === currency}
                          onChange={(e) => setFilters({...filters, currency: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{currency}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Prix</h4>
                  <div className="space-y-2">
                    {[
                      { label: 'Moins de 30€', value: '0-30' },
                      { label: '30€ - 60€', value: '30-60' },
                      { label: 'Plus de 60€', value: '60-999' }
                    ].map(range => (
                      <label key={range.value} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="amount"
                          value={range.value}
                          checked={filters.amount === range.value}
                          onChange={(e) => setFilters({...filters, amount: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{range.label}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({currency: '', amount: '', sortBy: 'popular'})}
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
                {sortedProducts.length} cryptomonnaies disponibles
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
                    <h3 className="text-xl font-bold text-white mb-2 group-hover:text-yellow-400 transition-colors">
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
                      <span className="text-sm text-yellow-400">
                        {product.amount}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
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
