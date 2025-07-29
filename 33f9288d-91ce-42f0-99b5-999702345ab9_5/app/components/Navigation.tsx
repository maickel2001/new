
'use client';

import { useState } from 'react';
import Link from 'next/link';

export default function Navigation() {
  const [isSearchFocused, setIsSearchFocused] = useState(false);
  const [cartCount, setCartCount] = useState(3);
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);

  return (
    <nav className="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center space-x-2">
            <div className="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
              <i className="ri-shopping-cart-2-line text-white text-lg"></i>
            </div>
            <span className="text-xl font-bold text-white">CREE 2GK</span>
          </Link>

          {/* Barre de recherche */}
          <div className="flex-1 max-w-2xl mx-8">
            <div className={`relative transition-all duration-200 ${isSearchFocused ? 'scale-105' : ''}`}>
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center">
                <i className="ri-search-line text-gray-400"></i>
              </div>
              <input
                type="text"
                className="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                placeholder="Rechercher des cartes cadeaux, codes, licences..."
                onFocus={() => setIsSearchFocused(true)}
                onBlur={() => setIsSearchFocused(false)}
              />
            </div>
          </div>

          {/* Navigation principale */}
          <div className="hidden md:flex items-center space-x-6">
            <Link href="/cartes-cadeaux" className="text-gray-300 hover:text-white transition-colors whitespace-nowrap">
              Cartes Cadeaux
            </Link>
            <Link href="/abonnements" className="text-gray-300 hover:text-white transition-colors whitespace-nowrap">
              Abonnements
            </Link>
            <Link href="/logiciels" className="text-gray-300 hover:text-white transition-colors whitespace-nowrap">
              Logiciels
            </Link>
            <Link href="/support" className="text-gray-300 hover:text-white transition-colors whitespace-nowrap">
              Support
            </Link>
          </div>

          {/* Actions utilisateur */}
          <div className="flex items-center space-x-4 ml-6">
            {/* Panier */}
            <Link href="/panier" className="relative p-2 text-gray-300 hover:text-white transition-colors">
              <i className="ri-shopping-cart-line text-xl"></i>
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {cartCount}
                </span>
              )}
            </Link>

            {/* Menu utilisateur */}
            <div className="relative">
              <button
                onClick={() => setIsUserMenuOpen(!isUserMenuOpen)}
                className="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors"
              >
                <i className="ri-user-line text-xl"></i>
                <span className="hidden sm:block whitespace-nowrap">Mon Compte</span>
                <i className="ri-arrow-down-s-line"></i>
              </button>

              {isUserMenuOpen && (
                <div className="absolute right-0 mt-2 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-50">
                  <Link href="/connexion" className="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i className="ri-login-box-line mr-2"></i>
                    Connexion
                  </Link>
                  <Link href="/inscription" className="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i className="ri-user-add-line mr-2"></i>
                    Inscription
                  </Link>
                  <div className="border-t border-gray-700"></div>
                  <Link href="/mes-commandes" className="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i className="ri-file-list-3-line mr-2"></i>
                    Mes Commandes
                  </Link>
                  <Link href="/mes-codes" className="block px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                    <i className="ri-key-2-line mr-2"></i>
                    Mes Codes
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </nav>
  );
}
