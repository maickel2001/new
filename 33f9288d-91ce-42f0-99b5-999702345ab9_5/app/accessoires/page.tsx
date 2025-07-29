'use client';

import Header from '../components/Header';
import Footer from '../components/Footer';
import { useState } from 'react';

export default function AccessoiresPage() {
  const [activeCategory, setActiveCategory] = useState('audio');
  const [favorites, setFavorites] = useState<number[]>([]);

  const accessories = {
    audio: [
      {
        id: 1,
        name: 'AirPods Pro 2e génération',
        price: 279,
        originalPrice: 299,
        image: 'https://readdy.ai/api/search-image?query=Apple%20AirPods%20Pro%202nd%20generation%20wireless%20earbuds%20in%20white%20charging%20case%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20premium%20audio%20accessories%20with%20noise%20cancellation%2C%20elegant%20commercial%20product%20style&width=300&height=300&seq=airpodspro2gen&orientation=squarish',
        badge: 'Nouveau',
        rating: 4.8,
        reviews: 456,
        specs: ['Réduction de bruit active', 'Audio spatial', 'Résistant à l\'eau IPX4']
      },
      {
        id: 2,
        name: 'Sony WH-1000XM5',
        price: 349,
        originalPrice: 399,
        image: 'https://readdy.ai/api/search-image?query=Sony%20WH-1000XM5%20wireless%20noise%20cancelling%20headphones%20in%20midnight%20black%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20high-end%20audio%20accessories%20with%20modern%20design%2C%20commercial%20showcase%20style&width=300&height=300&seq=sonywh1000xm5black&orientation=squarish',
        badge: 'Premium',
        rating: 4.7,
        reviews: 345,
        specs: ['Réduction de bruit leader', '30h d\'autonomie', 'Charge rapide 3min = 3h']
      },
      {
        id: 3,
        name: 'Samsung Galaxy Buds2 Pro',
        price: 199,
        originalPrice: 229,
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20Buds2%20Pro%20wireless%20earbuds%20in%20bora%20purple%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20audio%20accessories%20with%20case%2C%20commercial%20product%20style&width=300&height=300&seq=galaxybuds2propurple&orientation=squarish',
        badge: 'Promo',
        rating: 4.6,
        reviews: 178,
        specs: ['Audio 360 degrés', 'Réduction de bruit ANC', 'Résistant à l\'eau IPX7']
      },
      {
        id: 4,
        name: 'Beats Studio Buds +',
        price: 179,
        originalPrice: 199,
        image: 'https://readdy.ai/api/search-image?query=Beats%20Studio%20Buds%20Plus%20wireless%20earbuds%20in%20transparent%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20modern%20audio%20accessories%20with%20charging%20case%2C%20commercial%20showcase%20style&width=300&height=300&seq=beatsstudiobudsplus&orientation=squarish',
        badge: 'Sport',
        rating: 4.4,
        reviews: 234,
        specs: ['Son signature Beats', 'Réduction de bruit', 'Jusqu\'à 36h d\'écoute']
      },
      {
        id: 5,
        name: 'Bose QuietComfort 45',
        price: 299,
        originalPrice: 349,
        image: 'https://readdy.ai/api/search-image?query=Bose%20QuietComfort%2045%20wireless%20noise%20cancelling%20headphones%20in%20white%20smoke%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20comfort-focused%20audio%20accessories%2C%20commercial%20product%20style&width=300&height=300&seq=bosequietcomfort45&orientation=squarish',
        badge: 'Confort',
        rating: 4.5,
        reviews: 289,
        specs: ['Réduction de bruit légendaire', 'Confort exceptionnel', '24h d\'autonomie']
      },
      {
        id: 6,
        name: 'Jabra Elite 85t',
        price: 149,
        originalPrice: 199,
        image: 'https://readdy.ai/api/search-image?query=Jabra%20Elite%2085t%20wireless%20earbuds%20in%20gold%20beige%20color%20on%20clean%20white%20surface%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20premium%20business%20audio%20accessories%20with%20charging%20case%2C%20commercial%20showcase%20style&width=300&height=300&seq=jabraelite85t&orientation=squarish',
        badge: 'Business',
        rating: 4.3,
        reviews: 156,
        specs: ['Réduction de bruit réglable', 'Appels HD', 'Multipoint Bluetooth']
      }
    ],
    charge: [
      {
        id: 7,
        name: 'Chargeur MagSafe Apple',
        price: 45,
        originalPrice: 55,
        image: 'https://readdy.ai/api/search-image?query=Apple%20MagSafe%20wireless%20charger%20in%20white%20color%20on%20pristine%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20magnetic%20charging%20accessory%20with%20cable%2C%20commercial%20showcase%20style&width=300&height=300&seq=magsafecharger&orientation=squarish',
        badge: 'Magnétique',
        rating: 4.5,
        reviews: 234,
        specs: ['Charge sans fil 15W', 'Aimants parfaitement alignés', 'Compatible iPhone 12+']
      },
      {
        id: 8,
        name: 'Anker PowerCore 20000',
        price: 49,
        originalPrice: 69,
        image: 'https://readdy.ai/api/search-image?query=Anker%20PowerCore%2020000%20portable%20power%20bank%20in%20black%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20high-capacity%20charging%20accessory%2C%20commercial%20product%20style&width=300&height=300&seq=ankerpowercore20000&orientation=squarish',
        badge: 'Haute capacité',
        rating: 4.6,
        reviews: 567,
        specs: ['20000mAh', 'Charge rapide 18W', 'Charge simultanée 2 appareils']
      },
      {
        id: 9,
        name: 'Belkin Station 3-en-1',
        price: 149,
        originalPrice: 179,
        image: 'https://readdy.ai/api/search-image?query=Belkin%203-in-1%20wireless%20charging%20station%20in%20white%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20multi-device%20charging%20dock%20for%20iPhone%20Apple%20Watch%20AirPods%2C%20commercial%20showcase%20style&width=300&height=300&seq=belkin3in1station&orientation=squarish',
        badge: 'Tout-en-un',
        rating: 4.4,
        reviews: 123,
        specs: ['iPhone + Watch + AirPods', 'Charge simultanée', 'Design épuré']
      },
      {
        id: 10,
        name: 'Samsung Chargeur Duo',
        price: 79,
        originalPrice: 99,
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Wireless%20Charger%20Duo%20in%20black%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20dual%20device%20charging%20pad%2C%20commercial%20product%20style&width=300&height=300&seq=samsungchargerduo&orientation=squarish',
        badge: 'Duo',
        rating: 4.3,
        reviews: 198,
        specs: ['Charge 2 appareils', 'Charge rapide 15W', 'LED de statut']
      }
    ],
    protection: [
      {
        id: 11,
        name: 'Coque iPhone 15 Pro Silicone',
        price: 29,
        originalPrice: 39,
        image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20Pro%20silicone%20case%20in%20deep%20purple%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20protective%20phone%20accessory%20with%20smooth%20finish%2C%20commercial%20showcase%20style&width=300&height=300&seq=iphone15procase&orientation=squarish',
        badge: 'Officiel',
        rating: 4.7,
        reviews: 345,
        specs: ['Silicone premium', 'Protection MagSafe', 'Doublure microfibre']
      },
      {
        id: 12,
        name: 'Verre Trempé iPhone 15',
        price: 19,
        originalPrice: 29,
        image: 'https://readdy.ai/api/search-image?query=iPhone%2015%20tempered%20glass%20screen%20protector%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20protective%20accessory%20with%20installation%20kit%2C%20commercial%20product%20style&width=300&height=300&seq=iphone15glass&orientation=squarish',
        badge: 'Protection',
        rating: 4.5,
        reviews: 678,
        specs: ['Verre trempé 9H', 'Installation facile', 'Transparent 99%']
      },
      {
        id: 13,
        name: 'Coque Galaxy S24 Ultra',
        price: 35,
        originalPrice: 45,
        image: 'https://readdy.ai/api/search-image?query=Samsung%20Galaxy%20S24%20Ultra%20protective%20case%20in%20clear%20transparent%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20protective%20phone%20accessory%20with%20S%20Pen%20slot%2C%20commercial%20showcase%20style&width=300&height=300&seq=galaxys24ultracase&orientation=squarish',
        badge: 'Transparent',
        rating: 4.4,
        reviews: 234,
        specs: ['Protection S Pen', 'Antichoc renforcé', 'Matériau transparent']
      },
      {
        id: 14,
        name: 'Protection iPad Pro 12.9"',
        price: 59,
        originalPrice: 79,
        image: 'https://readdy.ai/api/search-image?query=iPad%20Pro%2012.9%20inch%20protective%20case%20with%20keyboard%20in%20navy%20blue%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20tablet%20accessory%20with%20Apple%20Pencil%20holder%2C%20commercial%20product%20style&width=300&height=300&seq=ipadpro129case&orientation=squarish',
        badge: 'Clavier',
        rating: 4.6,
        reviews: 156,
        specs: ['Clavier détachable', 'Support Apple Pencil', 'Protection intégrale']
      }
    ],
    connectique: [
      {
        id: 15,
        name: 'Câble USB-C vers Lightning',
        price: 25,
        originalPrice: 35,
        image: 'https://readdy.ai/api/search-image?query=Apple%20USB-C%20to%20Lightning%20cable%20in%20white%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20high-quality%20charging%20cable%2C%20commercial%20product%20style&width=300&height=300&seq=usbclightning&orientation=squarish',
        badge: 'Officiel',
        rating: 4.8,
        reviews: 567,
        specs: ['Charge rapide', 'Transfert de données', 'Certifié MFi']
      },
      {
        id: 16,
        name: 'Hub USB-C 7-en-1',
        price: 79,
        originalPrice: 99,
        image: 'https://readdy.ai/api/search-image?query=USB-C%20hub%207-in-1%20in%20space%20gray%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20multi-port%20connectivity%20accessory%2C%20commercial%20showcase%20style&width=300&height=300&seq=usbchub7in1&orientation=squarish',
        badge: 'Polyvalent',
        rating: 4.5,
        reviews: 234,
        specs: ['7 ports', 'HDMI 4K', 'Ethernet Gigabit']
      },
      {
        id: 17,
        name: 'Adaptateur Lightning vers Jack',
        price: 12,
        originalPrice: 19,
        image: 'https://readdy.ai/api/search-image?query=Apple%20Lightning%20to%203.5mm%20headphone%20jack%20adapter%20in%20white%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20small%20audio%20accessory%2C%20commercial%20product%20style&width=300&height=300&seq=lightningjack&orientation=squarish',
        badge: 'Audio',
        rating: 4.3,
        reviews: 789,
        specs: ['Audio haute qualité', 'Compact', 'Plug & Play']
      },
      {
        id: 18,
        name: 'Câble Thunderbolt 4',
        price: 149,
        originalPrice: 179,
        image: 'https://readdy.ai/api/search-image?query=Thunderbolt%204%20cable%20in%20black%20color%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20premium%20lighting%2C%20high-performance%20data%20transfer%20cable%2C%20commercial%20showcase%20style&width=300&height=300&seq=thunderbolt4&orientation=squarish',
        badge: 'Pro',
        rating: 4.7,
        reviews: 89,
        specs: ['40 Gbps', '8K @ 60Hz', 'Charge 100W']
      }
    ]
  };

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  return (
    <div className="min-h-screen bg-white">
      <Header />
      
      <div className="bg-gray-50 py-12">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Accessoires
          </h1>
          <p className="text-xl text-gray-600">
            Complétez votre setup avec nos accessoires premium
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        {/* Category Tabs */}
        <div className="flex flex-wrap justify-center gap-2 mb-8">
          {[
            { key: 'audio', label: 'Audio', icon: 'ri-headphone-line' },
            { key: 'charge', label: 'Charge', icon: 'ri-battery-charge-line' },
            { key: 'protection', label: 'Protection', icon: 'ri-shield-line' },
            { key: 'connectique', label: 'Connectique', icon: 'ri-usb-line' }
          ].map(category => (
            <button
              key={category.key}
              onClick={() => setActiveCategory(category.key)}
              className={`flex items-center gap-2 px-6 py-3 rounded-full font-semibold transition-all whitespace-nowrap cursor-pointer $\{
                activeCategory === category.key
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              }`}
            >
              <div className="w-5 h-5 flex items-center justify-center">
                <i className={category.icon}></i>
              </div>
              {category.label}
            </button>
          ))}
        </div>

        {/* Products Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {accessories[activeCategory].map((accessory) => (
            <div key={accessory.id} className="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
              <div className="relative">
                <img
                  src={accessory.image}
                  alt={accessory.name}
                  className="w-full h-64 object-cover object-top group-hover:scale-105 transition-transform duration-300"
                />
                <div className="absolute top-4 left-4">
                  <span className="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    {accessory.badge}
                  </span>
                </div>
                <button 
                  onClick={() => toggleFavorite(accessory.id)}
                  className="absolute top-4 right-4 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
                >
                  <div className="w-5 h-5 flex items-center justify-center">
                    <i className={`${favorites.includes(accessory.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-gray-600'}`}></i>
                  </div>
                </button>
              </div>
              <div className="p-6">
                <h3 className="text-xl font-bold text-gray-900 mb-2">
                  {accessory.name}
                </h3>
                <div className="flex items-center gap-2 mb-2">
                  <div className="flex items-center">
                    {[...Array(5)].map((_, i) => (
                      <div key={i} className="w-4 h-4 flex items-center justify-center">
                        <i className={`ri-star-${i < Math.floor(accessory.rating) ? 'fill' : 'line'} text-yellow-400`}></i>
                      </div>
                    ))}
                  </div>
                  <span className="text-sm text-gray-600">({accessory.reviews})</span>
                </div>
                <div className="flex items-center gap-2 mb-3">
                  <span className="text-2xl font-bold text-blue-600">
                    {accessory.price}€
                  </span>
                  <span className="text-lg text-gray-400 line-through">
                    {accessory.originalPrice}€
                  </span>
                </div>
                <div className="mb-4">
                  <ul className="text-sm text-gray-600 space-y-1">
                    {accessory.specs.map((spec, index) => (
                      <li key={index} className="flex items-center">
                        <div className="w-2 h-2 bg-blue-600 rounded-full mr-2"></div>
                        {spec}
                      </li>
                    ))}
                  </ul>
                </div>
                <button className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap cursor-pointer">
                  Ajouter au panier
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      <Footer />
    </div>
  );
}