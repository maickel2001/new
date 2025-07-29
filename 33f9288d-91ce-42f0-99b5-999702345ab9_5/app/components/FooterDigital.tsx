
'use client';

import Link from 'next/link';

export default function FooterDigital() {
  return (
    <footer className="bg-gray-900 border-t border-gray-800">
      <div className="max-w-7xl mx-auto px-4 py-16">
        <div className="grid lg:grid-cols-5 gap-8">
          {/* Logo et description */}
          <div className="lg:col-span-2">
            <Link href="/" className="flex items-center space-x-2 mb-4">
              <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                <i className="ri-shopping-cart-2-line text-white text-xl"></i>
              </div>
              <span className="text-2xl font-bold text-white">CREE 2GK</span>
            </Link>
            <p className="text-gray-400 mb-6 max-w-md">
              Votre plateforme de confiance pour l'achat de produits numériques. 
              Cartes cadeaux, codes d'abonnement et licences logicielles avec livraison instantanée.
            </p>
            <div className="flex space-x-4">
              <a href="#" className="w-10 h-10 bg-gray-800 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                <i className="ri-facebook-line text-gray-300 hover:text-white"></i>
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 hover:bg-blue-400 rounded-lg flex items-center justify-center transition-colors">
                <i className="ri-twitter-line text-gray-300 hover:text-white"></i>
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors">
                <i className="ri-instagram-line text-gray-300 hover:text-white"></i>
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 hover:bg-red-600 rounded-lg flex items-center justify-center transition-colors">
                <i className="ri-youtube-line text-gray-300 hover:text-white"></i>
              </a>
            </div>
          </div>

          {/* Produits */}
          <div>
            <h3 className="text-white font-semibold mb-4">Produits</h3>
            <div className="space-y-3">
              <Link href="/cartes-gaming" className="block text-gray-400 hover:text-white transition-colors">
                Cartes Gaming
              </Link>
              <Link href="/streaming" className="block text-gray-400 hover:text-white transition-colors">
                Streaming & Divertissement
              </Link>
              <Link href="/logiciels" className="block text-gray-400 hover:text-white transition-colors">
                Logiciels & Outils
              </Link>
              <Link href="/cartes-prepayees" className="block text-gray-400 hover:text-white transition-colors">
                Cartes Prépayées
              </Link>
              <Link href="/crypto" className="block text-gray-400 hover:text-white transition-colors">
                Cryptomonnaies
              </Link>
              <Link href="/vpn-securite" className="block text-gray-400 hover:text-white transition-colors">
                VPN & Sécurité
              </Link>
            </div>
          </div>

          {/* Support */}
          <div>
            <h3 className="text-white font-semibold mb-4">Support</h3>
            <div className="space-y-3">
              <Link href="/aide" className="block text-gray-400 hover:text-white transition-colors">
                Centre d'aide
              </Link>
              <Link href="/contact" className="block text-gray-400 hover:text-white transition-colors">
                Nous contacter
              </Link>
              <Link href="/faq" className="block text-gray-400 hover:text-white transition-colors">
                FAQ
              </Link>
              <Link href="/retours" className="block text-gray-400 hover:text-white transition-colors">
                Retours & Remboursements
              </Link>
              <Link href="/guide-achat" className="block text-gray-400 hover:text-white transition-colors">
                Guide d'achat
              </Link>
              <Link href="/statut" className="block text-gray-400 hover:text-white transition-colors">
                Statut du service
              </Link>
            </div>
          </div>

          {/* Entreprise */}
          <div>
            <h3 className="text-white font-semibold mb-4">Entreprise</h3>
            <div className="space-y-3">
              <Link href="/a-propos" className="block text-gray-400 hover:text-white transition-colors">
                À propos
              </Link>
              <Link href="/emplois" className="block text-gray-400 hover:text-white transition-colors">
                Carrières
              </Link>
              <Link href="/presse" className="block text-gray-400 hover:text-white transition-colors">
                Espace presse
              </Link>
              <Link href="/partenaires" className="block text-gray-400 hover:text-white transition-colors">
                Nos partenaires
              </Link>
              <Link href="/blog" className="block text-gray-400 hover:text-white transition-colors">
                Blog
              </Link>
              <Link href="/affilies" className="block text-gray-400 hover:text-white transition-colors">
                Programme d'affiliation
              </Link>
            </div>
          </div>
        </div>

        {/* Newsletter */}
        <div className="border-t border-gray-800 mt-12 pt-8">
          <div className="max-w-md">
            <h3 className="text-white font-semibold mb-4">
              <i className="ri-mail-line mr-2"></i>
              Restez informé de nos offres
            </h3>
            <div className="flex">
              <input
                type="email"
                placeholder="Votre adresse email"
                className="flex-1 bg-gray-800 border border-gray-700 rounded-l-lg px-4 py-3 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button className="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 rounded-r-lg font-semibold transition-all duration-200 whitespace-nowrap">
                S'abonner
              </button>
            </div>
            <p className="text-xs text-gray-500 mt-2">
              Recevez nos meilleures offres et nouveautés en exclusivité
            </p>
          </div>
        </div>

        {/* Bottom */}
        <div className="border-t border-gray-800 mt-12 pt-8 flex flex-col lg:flex-row justify-between items-center">
          <div className="text-gray-400 text-sm">
            © 2024 CREE 2GK. Tous droits réservés.
          </div>
          <div className="flex flex-wrap gap-6 mt-4 lg:mt-0">
            <Link href="/mentions-legales" className="text-gray-400 hover:text-white text-sm transition-colors">
              Mentions légales
            </Link>
            <Link href="/politique-confidentialite" className="text-gray-400 hover:text-white text-sm transition-colors">
              Politique de confidentialité
            </Link>
            <Link href="/cgu" className="text-gray-400 hover:text-white text-sm transition-colors">
              CGU
            </Link>
            <Link href="/cgv" className="text-gray-400 hover:text-white text-sm transition-colors">
              CGV
            </Link>
            <Link href="/cookies" className="text-gray-400 hover:text-white text-sm transition-colors">
              Gestion des cookies
            </Link>
          </div>
        </div>
      </div>

      {/* Méthodes de paiement */}
      <div className="border-t border-gray-800 bg-gray-950 px-4 py-6">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col lg:flex-row justify-between items-center">
            <div className="flex items-center space-x-4 text-gray-400 text-sm">
              <span>Paiement sécurisé par :</span>
              <div className="flex items-center space-x-3">
                <div className="bg-gray-800 px-3 py-1 rounded text-xs font-medium">KiaPay</div>
                <div className="bg-gray-800 px-3 py-1 rounded text-xs font-medium">PayPal</div>
                <div className="bg-gray-800 px-3 py-1 rounded text-xs font-medium">Visa</div>
                <div className="bg-gray-800 px-3 py-1 rounded text-xs font-medium">Mastercard</div>
              </div>
            </div>
            <div className="flex items-center space-x-4 text-gray-400 text-sm mt-4 lg:mt-0">
              <div className="flex items-center space-x-1">
                <i className="ri-shield-check-line text-green-400"></i>
                <span>SSL Sécurisé</span>
              </div>
              <div className="flex items-center space-x-1">
                <i className="ri-flashlight-line text-blue-400"></i>
                <span>Livraison instantanée</span>
              </div>
              <div className="flex items-center space-x-1">
                <i className="ri-customer-service-2-line text-purple-400"></i>
                <span>Support 24/7</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
