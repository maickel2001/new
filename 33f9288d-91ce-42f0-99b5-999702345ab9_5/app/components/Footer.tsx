
'use client';

import Link from 'next/link';

export default function Footer() {
  return (
    <footer className="bg-gray-900 text-white py-16">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <Link href="/" className="text-3xl font-bold text-blue-400 mb-4 block" style={{ fontFamily: 'Pacifico, serif' }}>
              TechStore
            </Link>
            <p className="text-gray-400 mb-6">
              Votre destination pour les dernières technologies. Smartphones, tablettes et accessoires de qualité premium.
            </p>
            <div className="flex space-x-4">
              <button className="p-2 bg-gray-800 rounded-full hover:bg-gray-700 transition-colors cursor-pointer">
                <div className="w-5 h-5 flex items-center justify-center">
                  <i className="ri-facebook-fill"></i>
                </div>
              </button>
              <button className="p-2 bg-gray-800 rounded-full hover:bg-gray-700 transition-colors cursor-pointer">
                <div className="w-5 h-5 flex items-center justify-center">
                  <i className="ri-twitter-fill"></i>
                </div>
              </button>
              <button className="p-2 bg-gray-800 rounded-full hover:bg-gray-700 transition-colors cursor-pointer">
                <div className="w-5 h-5 flex items-center justify-center">
                  <i className="ri-instagram-fill"></i>
                </div>
              </button>
            </div>
          </div>

          <div>
            <h3 className="text-xl font-semibold mb-4">Produits</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/smartphones" className="text-gray-400 hover:text-white transition-colors">
                  Smartphones
                </Link>
              </li>
              <li>
                <Link href="/tablettes" className="text-gray-400 hover:text-white transition-colors">
                  Tablettes
                </Link>
              </li>
              <li>
                <Link href="/accessoires" className="text-gray-400 hover:text-white transition-colors">
                  Accessoires
                </Link>
              </li>
              <li>
                <Link href="/nouveautes" className="text-gray-400 hover:text-white transition-colors">
                  Nouveautés
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="text-xl font-semibold mb-4">Support</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/contact" className="text-gray-400 hover:text-white transition-colors">
                  Contact
                </Link>
              </li>
              <li>
                <Link href="/faq" className="text-gray-400 hover:text-white transition-colors">
                  FAQ
                </Link>
              </li>
              <li>
                <Link href="/livraison" className="text-gray-400 hover:text-white transition-colors">
                  Livraison
                </Link>
              </li>
              <li>
                <Link href="/retours" className="text-gray-400 hover:text-white transition-colors">
                  Retours
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="text-xl font-semibold mb-4">Informations</h3>
            <ul className="space-y-2">
              <li>
                <Link href="/a-propos" className="text-gray-400 hover:text-white transition-colors">
                  À propos
                </Link>
              </li>
              <li>
                <Link href="/conditions" className="text-gray-400 hover:text-white transition-colors">
                  Conditions d'utilisation
                </Link>
              </li>
              <li>
                <Link href="/confidentialite" className="text-gray-400 hover:text-white transition-colors">
                  Politique de confidentialité
                </Link>
              </li>
              <li>
                <Link href="/mentions-legales" className="text-gray-400 hover:text-white transition-colors">
                  Mentions légales
                </Link>
              </li>
            </ul>
          </div>
        </div>

        <div className="border-t border-gray-800 mt-12 pt-8 text-center">
          <p className="text-gray-400">
            © 2024 TechStore. Tous droits réservés.
          </p>
        </div>
      </div>
    </footer>
  );
}
