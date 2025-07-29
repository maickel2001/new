
'use client';

import Link from 'next/link';

export default function CategoriesGrid() {
  const categories = [
    {
      name: 'Cartes Gaming',
      description: 'Steam, Epic Games, PlayStation, Xbox',
      icon: 'ri-gamepad-line',
      link: '/cartes-gaming',
      gradient: 'from-purple-500 to-blue-500',
      count: '250+ produits'
    },
    {
      name: 'Streaming & Divertissement',
      description: 'Netflix, Spotify, YouTube Premium',
      icon: 'ri-play-circle-line',
      link: '/streaming',
      gradient: 'from-red-500 to-pink-500',
      count: '50+ services'
    },
    {
      name: 'Logiciels & Outils',
      description: 'Windows, Office, Adobe, Antivirus',
      icon: 'ri-computer-line',
      link: '/logiciels',
      gradient: 'from-green-500 to-blue-500',
      count: '180+ licences'
    },
    {
      name: 'Cartes Prépayées',
      description: 'Amazon, iTunes, Google Play',
      icon: 'ri-gift-2-line',
      link: '/cartes-prepayees',
      gradient: 'from-orange-500 to-yellow-500',
      count: '100+ cartes'
    },
    {
      name: 'Cryptomonnaies',
      description: 'Bitcoin, Ethereum, codes crypto',
      icon: 'ri-bit-coin-line',
      link: '/crypto',
      gradient: 'from-yellow-500 to-orange-500',
      count: '20+ devises'
    },
    {
      name: 'VPN & Sécurité',
      description: 'NordVPN, ExpressVPN, antivirus',
      icon: 'ri-shield-check-line',
      link: '/vpn-securite',
      gradient: 'from-indigo-500 to-purple-500',
      count: '30+ solutions'
    }
  ];

  return (
    <section className="py-20 bg-gray-800">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-white mb-4">
            Explorez nos <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">catégories</span>
          </h2>
          <p className="text-xl text-gray-300 max-w-2xl mx-auto">
            Des milliers de produits numériques disponibles dans toutes les catégories populaires
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          {categories.map((category, index) => (
            <Link
              key={index}
              href={category.link}
              className="group bg-gray-900 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl"
            >
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <div className={`w-12 h-12 bg-gradient-to-r ${category.gradient} rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300`}>
                    <i className={`${category.icon} text-white text-xl`}></i>
                  </div>
                  <span className="text-sm text-gray-400 bg-gray-800 px-3 py-1 rounded-full">
                    {category.count}
                  </span>
                </div>
                
                <div>
                  <h3 className="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                    {category.name}
                  </h3>
                  <p className="text-gray-400 text-sm leading-relaxed">
                    {category.description}
                  </p>
                </div>
                
                <div className="flex items-center text-blue-400 text-sm font-medium">
                  <span>Découvrir</span>
                  <i className="ri-arrow-right-line ml-2 group-hover:translate-x-1 transition-transform"></i>
                </div>
              </div>
            </Link>
          ))}
        </div>

        <div className="text-center mt-12">
          <Link
            href="/catalogue"
            className="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl whitespace-nowrap"
          >
            <i className="ri-apps-2-line mr-2"></i>
            Voir tout le catalogue
          </Link>
        </div>
      </div>
    </section>
  );
}
