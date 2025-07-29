
'use client';

import Link from 'next/link';
import { useState } from 'react';

export default function FeaturedProducts() {
  const [activeTab, setActiveTab] = useState('smartphones');

  const products = {
    smartphones: [
      {
        id: 1,
        name: 'iPhone 15 Pro',
        price: '1 199€',
        originalPrice: '1 329€',
        image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20Pro%20in%20natural%20titanium%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20soft%20shadows%2C%20premium%20smartphone%20showcasing%20elegant%20design%2C%20high-end%20mobile%20device%20with%20sophisticated%20lighting%2C%20commercial%20product%20style&width=300&height=300&seq=iphone15pro&orientation=squarish',
        badge: 'Nouveau'
      },
      {
        id: 2,
        name: 'Samsung Galaxy S24',
        price: '899€',
        originalPrice: '999€',
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20S24%20smartphone%20in%20elegant%20black%20color%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20modern%20flagship%20Android%20device%2C%20high-quality%20commercial%20showcase%20style&width=300&height=300&seq=galaxys24&orientation=squarish',
        badge: 'Promo'
      },
      {
        id: 3,
        name: 'Google Pixel 8',
        price: '699€',
        originalPrice: '799€',
        image: 'https://readdy.ai/api/search-image?query=Google%20Pixel%208%20smartphone%20in%20obsidian%20black%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20Android%20flagship%20device%2C%20sophisticated%20commercial%20product%20style&width=300&height=300&seq=pixel8&orientation=squarish',
        badge: 'Populaire'
      },
      {
        id: 4,
        name: 'OnePlus 12',
        price: '949€',
        originalPrice: '1 099€',
        image: 'https://readdy.ai/api/search-image?query=OnePlus%2012%20smartphone%20in%20sleek%20black%20finish%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20flagship%20Android%20device%20with%20elegant%20design%2C%20high-end%20commercial%20showcase%20style&width=300&height=300&seq=oneplus12&orientation=squarish',
        badge: 'Promo'
      },
      {
        id: 5,
        name: 'Xiaomi 14 Ultra',
        price: '1 299€',
        originalPrice: '1 399€',
        image: 'https://readdy.ai/api/search-image?query=Xiaomi%2014%20Ultra%20smartphone%20in%20premium%20black%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20sophisticated%20lighting%2C%20flagship%20camera%20phone%20with%20elegant%20design%2C%20commercial%20product%20style&width=300&height=300&seq=xiaomi14ultra&orientation=squarish',
        badge: 'Photo Pro'
      },
      {
        id: 6,
        name: 'iPhone 15',
        price: '969€',
        originalPrice: '1 079€',
        image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20in%20pink%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20premium%20smartphone%20with%20elegant%20design%2C%20high-end%20mobile%20device%20showcase%2C%20commercial%20product%20style&width=300&height=300&seq=iphone15&orientation=squarish',
        badge: 'Populaire'
      }
    ],
    tablettes: [
      {
        id: 7,
        name: 'iPad Pro 12.9"',
        price: '1 449€',
        originalPrice: '1 549€',
        image: 'https://readdy.ai/api/search-image?query=iPad%20Pro%2012.9%20inch%20in%20space%20gray%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20large%20premium%20tablet%20device%2C%20sophisticated%20commercial%20showcase%20style&width=300&height=300&seq=ipadpro&orientation=squarish',
        badge: 'Premium'
      },
      {
        id: 8,
        name: 'Samsung Galaxy Tab S9',
        price: '799€',
        originalPrice: '899€',
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Tab%20S9%20tablet%20in%20graphite%20color%20on%20pristine%20white%20background%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20Android%20tablet%20device%2C%20high-end%20commercial%20style&width=300&height=300&seq=galaxytab&orientation=squarish',
        badge: 'Nouveau'
      },
      {
        id: 9,
        name: 'iPad Air',
        price: '649€',
        originalPrice: '729€',
        image: 'https://readdy.ai/api/search-image?query=iPad%20Air%20in%20blue%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20mid-range%20premium%20tablet%2C%20elegant%20commercial%20product%20showcase%20style&width=300&height=300&seq=ipadair&orientation=squarish',
        badge: 'Promo'
      },
      {
        id: 10,
        name: 'Surface Pro 9',
        price: '1 179€',
        originalPrice: '1 299€',
        image: 'https://readdy.ai/api/search-image?query=Microsoft%20Surface%20Pro%209%20tablet%20in%20platinum%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%202-in-1%20Windows%20tablet%20with%20elegant%20design%2C%20commercial%20showcase%20style&width=300&height=300&seq=surfacepro9&orientation=squarish',
        badge: 'Productivité'
      },
      {
        id: 11,
        name: 'iPad Mini',
        price: '549€',
        originalPrice: '609€',
        image: 'https://readdy.ai/api/search-image?query=iPad%20Mini%20in%20starlight%20color%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20compact%20premium%20tablet%20device%2C%20elegant%20commercial%20product%20style&width=300&height=300&seq=ipadmini&orientation=squarish',
        badge: 'Compact'
      },
      {
        id: 12,
        name: 'Galaxy Tab S9 Ultra',
        price: '1 199€',
        originalPrice: '1 349€',
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Tab%20S9%20Ultra%20in%20beige%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20large%20premium%20Android%20tablet%2C%20sophisticated%20commercial%20showcase%20style&width=300&height=300&seq=galaxytabultra&orientation=squarish',
        badge: 'Ultra'
      }
    ],
    accessoires: [
      {
        id: 13,
        name: 'AirPods Pro 2',
        price: '279€',
        originalPrice: '299€',
        image: 'https://readdy.ai/api/search-image?query=Apple%20AirPods%20Pro%202%20wireless%20earbuds%20in%20white%20charging%20case%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20premium%20audio%20accessories%2C%20elegant%20commercial%20product%20style&width=300&height=300&seq=airpodspro2&orientation=squarish',
        badge: 'Audio'
      },
      {
        id: 14,
        name: 'MagSafe Charger',
        price: '45€',
        originalPrice: '55€',
        image: 'https://readdy.ai/api/search-image?query=Apple%20MagSafe%20wireless%20charger%20in%20white%20color%20on%20pristine%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20magnetic%20charging%20accessory%2C%20commercial%20showcase%20style&width=300&height=300&seq=magsafe&orientation=squarish',
        badge: 'Charge'
      },
      {
        id: 15,
        name: 'Samsung Galaxy Buds2 Pro',
        price: '199€',
        originalPrice: '229€',
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Buds2%20Pro%20wireless%20earbuds%20in%20graphite%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20audio%20accessories%2C%20commercial%20product%20style&width=300&height=300&seq=galaxybuds2pro&orientation=squarish',
        badge: 'Promo'
      }
    ]
  };

  return (
    <section className="py-20 bg-white">
      <div className="container mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-gray-900 mb-4">
            Produits populaires
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Découvrez nos meilleures ventes et nouveautés
          </p>
        </div>

        <div className="flex justify-center mb-12">
          <div className="bg-gray-100 rounded-full p-1">
            <button
              onClick={() => setActiveTab('smartphones')}
              className={`px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer $\{
                activeTab === 'smartphones'
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              Smartphones
            </button>
            <button
              onClick={() => setActiveTab('tablettes')}
              className={`px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer $\{
                activeTab === 'tablettes'
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              Tablettes
            </button>
            <button
              onClick={() => setActiveTab('accessoires')}
              className={`px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer $\{
                activeTab === 'accessoires'
                  ? 'bg-blue-600 text-white'
                  : 'text-gray-600 hover:text-gray-900'
              }`}
            >
              Accessoires
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {products[activeTab].map((product) => (
            <div key={product.id} className="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
              <div className="relative">
                <img
                  src={product.image}
                  alt={product.name}
                  className="w-full h-64 object-cover object-top group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute top-4 left-4">
                  <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    {product.badge}
                  </span>
                </div>
                <button className="absolute top-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors cursor-pointer">
                  <div className="w-5 h-5 flex items-center justify-center">
                    <i className="ri-heart-line text-gray-600"></i>
                  </div>
                </button>
              </div>
              <div className="p-6">
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {product.name}
                </h3>
                <div className="flex items-center gap-2 mb-4">
                  <span className="text-2xl font-bold text-blue-600">
                    {product.price}
                  </span>
                  <span className="text-lg text-gray-400 line-through">
                    {product.originalPrice}
                  </span>
                </div>
                <button className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap cursor-pointer">
                  Ajouter au panier
                </button>
              </div>
            </div>
          ))}
        </div>

        <div className="text-center mt-12">
          <Link href="/produits">
            <button className="bg-gray-900 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-gray-800 transition-colors whitespace-nowrap cursor-pointer">
              Voir tous les produits
            </button>
          </Link>
        </div>
      </div>
    </section>
  );
}
