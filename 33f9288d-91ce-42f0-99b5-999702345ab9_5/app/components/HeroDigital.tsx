
'use client';

import Link from 'next/link';

export default function HeroDigital() {
  return (
    <div className="relative bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 overflow-hidden">
      {/* Background decoration */}
      <div className="absolute inset-0">
        <div className="absolute top-20 left-10 w-20 h-20 bg-blue-500/20 rounded-full blur-xl"></div>
        <div className="absolute top-40 right-20 w-32 h-32 bg-purple-500/20 rounded-full blur-xl"></div>
        <div className="absolute bottom-20 left-1/4 w-24 h-24 bg-cyan-500/20 rounded-full blur-xl"></div>
      </div>
      
      <div className="relative max-w-7xl mx-auto px-4 py-24">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Contenu principal */}
          <div className="space-y-8">
            <div className="space-y-4">
              <div className="inline-flex items-center space-x-2 bg-blue-500/10 border border-blue-500/20 rounded-full px-4 py-2">
                <i className="ri-flashlight-line text-blue-400"></i>
                <span className="text-blue-400 text-sm font-medium">Livraison Instantanée</span>
              </div>
              
              <h1 className="text-5xl lg:text-6xl font-bold leading-tight">
                <span className="text-white">Votre boutique de</span>
                <br />
                <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                  produits numériques
                </span>
              </h1>
              
              <p className="text-xl text-gray-300 max-w-lg">
                Cartes cadeaux, codes d'abonnement, licences logicielles et bien plus. 
                Achetez en toute sécurité et recevez vos codes instantanément.
              </p>
            </div>

            <div className="flex flex-col sm:flex-row gap-4">
              <Link 
                href="/cartes-cadeaux"
                className="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl text-center whitespace-nowrap"
              >
                <i className="ri-gift-line mr-2"></i>
                Découvrir les Cartes
              </Link>
              
              <Link 
                href="/logiciels"
                className="bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 hover:border-gray-500 px-8 py-4 rounded-lg font-semibold transition-all duration-200 text-center whitespace-nowrap"
              >
                <i className="ri-software-line mr-2"></i>
                Voir les Logiciels
              </Link>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-3 gap-8 pt-8 border-t border-gray-700">
              <div className="text-center">
                <div className="text-2xl font-bold text-white">500K+</div>
                <div className="text-sm text-gray-400">Clients satisfaits</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-white">24/7</div>
                <div className="text-sm text-gray-400">Support client</div>
              </div>
              <div className="text-center">
                <div className="text-2xl font-bold text-white">100%</div>
                <div className="text-sm text-gray-400">Sécurisé</div>
              </div>
            </div>
          </div>

          {/* Image illustration */}
          <div className="relative">
            <div className="relative z-10 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-8 shadow-2xl">
              {/* Simulation d'interface */}
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-lg font-semibold text-white">Commande récente</h3>
                  <span className="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm">
                    <i className="ri-check-line mr-1"></i>
                    Livrée
                  </span>
                </div>
                
                <div className="bg-gray-700 rounded-lg p-4">
                  <div className="flex items-center space-x-3">
                    <img 
                      src="https://readdy.ai/api/search-image?query=Steam%20gift%20card%20digital%20code%20gaming%20platform%20logo%20dark%20background%20professional%20e-commerce%20product%20image%20minimal%20clean%20design%20blue%20accent%20colors&width=60&height=60&seq=steam-card&orientation=squarish"
                      alt="Steam Card"
                      className="w-12 h-12 rounded-lg object-cover"
                    />
                    <div>
                      <h4 className="font-medium text-white">Carte Steam 50€</h4>
                      <p className="text-sm text-gray-400">Code: XXXX-XXXX-XXXX</p>
                    </div>
                  </div>
                </div>
                
                <div className="bg-gray-700 rounded-lg p-4">
                  <div className="flex items-center space-x-3">
                    <img 
                      src="https://readdy.ai/api/search-image?query=Netflix%20subscription%20streaming%20service%20digital%20code%20entertainment%20platform%20dark%20background%20modern%20e-commerce%20product%20image%20red%20branding%20colors&width=60&height=60&seq=netflix-sub&orientation=squarish"
                      alt="Netflix"
                      className="w-12 h-12 rounded-lg object-cover"
                    />
                    <div>
                      <h4 className="font-medium text-white">Netflix Premium 3 mois</h4>
                      <p className="text-sm text-gray-400">Accès immédiat</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            {/* Decoration */}
            <div className="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl opacity-20 blur-xl"></div>
          </div>
        </div>
      </div>
    </div>
  );
}
