
'use client';

import Link from 'next/link';
import { useState } from 'react';

export default function Header() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <header className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 py-4">
        <div className="flex items-center justify-between">
          <Link href="/" className="text-2xl font-bold text-blue-600" style={{ fontFamily: 'Pacifico, serif' }}>
            TechStore
          </Link>
          
          <nav className="hidden md:flex items-center space-x-8">
            <Link href="/smartphones" className="text-gray-700 hover:text-blue-600 transition-colors">
              Smartphones
            </Link>
            <Link href="/tablettes" className="text-gray-700 hover:text-blue-600 transition-colors">
              Tablettes
            </Link>
            <Link href="/accessoires" className="text-gray-700 hover:text-blue-600 transition-colors">
              Accessoires
            </Link>
            <Link href="/contact" className="text-gray-700 hover:text-blue-600 transition-colors">
              Contact
            </Link>
          </nav>

          <div className="flex items-center space-x-4">
            <div className="relative">
              <input
                type="text"
                placeholder="Rechercher..."
                className="pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              />
              <div className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 flex items-center justify-center">
                <i className="ri-search-line text-gray-400"></i>
              </div>
            </div>
            
            <button className="relative p-2 text-gray-700 hover:text-blue-600 transition-colors cursor-pointer">
              <div className="w-6 h-6 flex items-center justify-center">
                <i className="ri-shopping-cart-line text-xl"></i>
              </div>
              <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                3
              </span>
            </button>
          </div>

          <button
            className="md:hidden p-2 cursor-pointer"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
          >
            <div className="w-6 h-6 flex items-center justify-center">
              <i className={isMenuOpen ? "ri-close-line" : "ri-menu-line"}></i>
            </div>
          </button>
        </div>

        {isMenuOpen && (
          <div className="md:hidden mt-4 pb-4">
            <nav className="flex flex-col space-y-4">
              <Link href="/smartphones" className="text-gray-700 hover:text-blue-600 transition-colors">
                Smartphones
              </Link>
              <Link href="/tablettes" className="text-gray-700 hover:text-blue-600 transition-colors">
                Tablettes
              </Link>
              <Link href="/accessoires" className="text-gray-700 hover:text-blue-600 transition-colors">
                Accessoires
              </Link>
              <Link href="/contact" className="text-gray-700 hover:text-blue-600 transition-colors">
                Contact
              </Link>
            </nav>
          </div>
        )}
      </div>
    </header>
  );
}
