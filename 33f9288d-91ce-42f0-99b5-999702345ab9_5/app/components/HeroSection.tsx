
'use client';

import Link from 'next/link';

export default function HeroSection() {
  return (
    <section 
      className="relative bg-cover bg-center bg-no-repeat min-h-screen flex items-center"
      style={{
        backgroundImage: `url('https://readdy.ai/api/search-image?query=Modern%20technology%20store%20interior%20with%20premium%20smartphones%20and%20tablets%20displayed%20on%20sleek%20white%20surfaces%2C%20professional%20lighting%20creating%20elegant%20shadows%2C%20minimalist%20contemporary%20design%20with%20clean%20lines%2C%20high-end%20retail%20environment%20with%20sophisticated%20ambiance%2C%20ultra-wide%20panoramic%20view%20showcasing%20premium%20electronic%20devices&width=1920&height=1080&seq=hero-tech-store&orientation=landscape')`
      }}
    >
      <div className="absolute inset-0 bg-black bg-opacity-40"></div>
      
      <div className="container mx-auto px-4 relative z-10">
        <div className="w-full max-w-2xl">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
            Découvrez les dernières 
            <span className="text-blue-400"> innovations technologiques</span>
          </h1>
          <p className="text-xl text-gray-200 mb-8 leading-relaxed">
            Smartphones, tablettes et accessoires de qualité premium. 
            Livraison gratuite et garantie étendue sur tous nos produits.
          </p>
          <div className="flex flex-col sm:flex-row gap-4">
            <Link href="/smartphones">
              <button className="bg-blue-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap cursor-pointer">
                Voir nos smartphones
              </button>
            </Link>
            <Link href="/tablettes">
              <button className="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-gray-900 transition-colors whitespace-nowrap cursor-pointer">
                Découvrir les tablettes
              </button>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
