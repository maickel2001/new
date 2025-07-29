
'use client';

import { useState } from 'react';
import Link from 'next/link';

export default function FeaturedDigital() {
  const [activeTab, setActiveTab] = useState('gaming');

  const products = {
    gaming: [
      {
        id: 1,
        name: 'Carte Steam 50€',
        price: '50.00',
        originalPrice: '52.00',
        image: 'https://readdy.ai/api/search-image?query=Steam%20gift%20card%2050%20euros%20digital%20gaming%20platform%20dark%20background%20professional%20e-commerce%20product%20image%20blue%20gradient%20clean%20minimal%20design&width=300&height=200&seq=steam-50&orientation=landscape',
        badge: 'PROMO',
        badgeColor: 'bg-red-500',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock'
      },
      {
        id: 2,
        name: 'PlayStation Plus 12 mois',
        price: '59.99',
        originalPrice: '69.99',
        image: 'https://readdy.ai/api/search-image?query=PlayStation%20Plus%20subscription%2012%20months%20gaming%20service%20digital%20code%20dark%20background%20professional%20product%20image%20blue%20colors%20modern%20design&width=300&height=200&seq=ps-plus-12&orientation=landscape',
        badge: 'POPULAIRE',
        badgeColor: 'bg-blue-500',
        delivery: '5-10 min',
        rating: 4.8,
        stock: 'En stock'
      },
      {
        id: 3,
        name: 'Xbox Game Pass Ultimate 3 mois',
        price: '29.99',
        originalPrice: '34.99',
        image: 'https://readdy.ai/api/search-image?query=Xbox%20Game%20Pass%20Ultimate%203%20months%20gaming%20subscription%20digital%20service%20dark%20background%20green%20accent%20colors%20professional%20product%20image&width=300&height=200&seq=xbox-gpu-3&orientation=landscape',
        badge: 'NOUVEAU',
        badgeColor: 'bg-green-500',
        delivery: 'Instantané',
        rating: 4.7,
        stock: 'Stock limité'
      },
      {
        id: 4,
        name: 'Riot Points 10€ (League of Legends)',
        price: '10.00',
        originalPrice: '10.00',
        image: 'https://readdy.ai/api/search-image?query=League%20of%20Legends%20Riot%20Points%20gaming%20currency%20digital%20code%2010%20euros%20dark%20background%20professional%20e-commerce%20product%20image%20gold%20accent%20colors&width=300&height=200&seq=riot-points-10&orientation=landscape',
        badge: null,
        badgeColor: '',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock'
      }
    ],
    streaming: [
      {
        id: 5,
        name: 'Netflix Premium 6 mois',
        price: '89.99',
        originalPrice: '95.94',
        image: 'https://readdy.ai/api/search-image?query=Netflix%20Premium%20subscription%206%20months%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20colors%20professional%20product%20image&width=300&height=200&seq=netflix-6m&orientation=landscape',
        badge: 'ÉCONOMIE',
        badgeColor: 'bg-green-500',
        delivery: '2-5 min',
        rating: 4.8,
        stock: 'En stock'
      },
      {
        id: 6,
        name: 'Spotify Premium 12 mois',
        price: '99.99',
        originalPrice: '119.88',
        image: 'https://readdy.ai/api/search-image?query=Spotify%20Premium%20subscription%2012%20months%20music%20streaming%20service%20digital%20access%20dark%20background%20green%20branding%20professional%20product%20image&width=300&height=200&seq=spotify-12m&orientation=landscape',
        badge: 'BEST SELLER',
        badgeColor: 'bg-purple-500',
        delivery: 'Manuel',
        rating: 4.7,
        stock: 'En stock'
      },
      {
        id: 7,
        name: 'YouTube Premium 3 mois',
        price: '32.99',
        originalPrice: '35.97',
        image: 'https://readdy.ai/api/search-image?query=YouTube%20Premium%20subscription%203%20months%20video%20streaming%20service%20digital%20access%20dark%20background%20red%20branding%20professional%20product%20image&width=300&height=200&seq=youtube-3m&orientation=landscape',
        badge: 'PROMO',
        badgeColor: 'bg-red-500',
        delivery: '1-2h',
        rating: 4.6,
        stock: 'En stock'
      },
      {
        id: 8,
        name: 'Disney+ 12 mois',
        price: '89.90',
        originalPrice: '89.90',
        image: 'https://readdy.ai/api/search-image?query=Disney%20Plus%20subscription%2012%20months%20streaming%20service%20digital%20access%20dark%20background%20blue%20branding%20professional%20product%20image%20family%20entertainment&width=300&height=200&seq=disney-12m&orientation=landscape',
        badge: null,
        badgeColor: '',
        delivery: 'Manuel',
        rating: 4.5,
        stock: 'En stock'
      }
    ],
    software: [
      {
        id: 9,
        name: 'Microsoft Office 2021 Pro',
        price: '49.99',
        originalPrice: '439.99',
        image: 'https://readdy.ai/api/search-image?query=Microsoft%20Office%202021%20Professional%20software%20license%20productivity%20suite%20digital%20key%20dark%20background%20blue%20colors%20business%20professional%20product%20image&width=300&height=200&seq=office-2021&orientation=landscape',
        badge: 'MEGA PROMO',
        badgeColor: 'bg-red-600',
        delivery: 'Instantané',
        rating: 4.9,
        stock: 'En stock'
      },
      {
        id: 10,
        name: 'Windows 11 Pro',
        price: '19.99',
        originalPrice: '259.99',
        image: 'https://readdy.ai/api/search-image?query=Windows%2011%20Professional%20operating%20system%20software%20license%20digital%20key%20dark%20background%20blue%20gradient%20modern%20professional%20product%20image&width=300&height=200&seq=win11-pro&orientation=landscape',
        badge: 'TOP VENTE',
        badgeColor: 'bg-blue-500',
        delivery: 'Instantané',
        rating: 4.8,
        stock: 'En stock'
      },
      {
        id: 11,
        name: 'Adobe Creative Cloud 1 an',
        price: '299.99',
        originalPrice: '659.88',
        image: 'https://readdy.ai/api/search-image?query=Adobe%20Creative%20Cloud%20subscription%201%20year%20creative%20software%20suite%20digital%20license%20dark%20background%20colorful%20gradient%20professional%20design%20product%20image&width=300&height=200&seq=adobe-cc-1y&orientation=landscape',
        badge: 'ÉCONOMIE',
        badgeColor: 'bg-green-500',
        delivery: '10-30 min',
        rating: 4.7,
        stock: 'Stock limité'
      },
      {
        id: 12,
        name: 'Kaspersky Total Security',
        price: '24.99',
        originalPrice: '49.99',
        image: 'https://readdy.ai/api/search-image?query=Kaspersky%20Total%20Security%20antivirus%20software%20license%20cybersecurity%20protection%20digital%20key%20dark%20background%20green%20colors%20professional%20product%20image&width=300&height=200&seq=kaspersky-total&orientation=landscape',
        badge: 'SÉCURITÉ',
        badgeColor: 'bg-green-600',
        delivery: 'Instantané',
        rating: 4.6,
        stock: 'En stock'
      }
    ]
  };

  const tabs = [
    { key: 'gaming', name: 'Gaming', icon: 'ri-gamepad-line' },
    { key: 'streaming', name: 'Streaming', icon: 'ri-play-circle-line' },
    { key: 'software', name: 'Logiciels', icon: 'ri-computer-line' }
  ];

  return (
    <section className="py-20 bg-gray-900">
      <div className="max-w-7xl mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="text-4xl font-bold text-white mb-4">
            Produits <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">populaires</span>
          </h2>
          <p className="text-xl text-gray-300">
            Les meilleures offres du moment avec livraison instantanée
          </p>
        </div>

        {/* Onglets */}
        <div className="flex justify-center mb-12">
          <div className="bg-gray-800 rounded-xl p-1 border border-gray-700">
            {tabs.map((tab) => (
              <button
                key={tab.key}
                onClick={() => setActiveTab(tab.key)}
                className={`px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 whitespace-nowrap ${
                  activeTab === tab.key
                    ? 'bg-blue-600 text-white shadow-lg'
                    : 'text-gray-300 hover:text-white hover:bg-gray-700'
                }`}
              >
                <i className={tab.icon}></i>
                <span>{tab.name}</span>
              </button>
            ))}
          </div>
        </div>

        {/* Grille de produits */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {products[activeTab].map((product) => (
            <div key={product.id} className="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
              <div className="relative">
                <img
                  src={product.image}
                  alt={product.name}
                  className="w-full h-48 object-cover rounded-t-xl"
                />
                {product.badge && (
                  <span className={`absolute top-3 left-3 ${product.badgeColor} text-white px-2 py-1 rounded-full text-xs font-bold`}>
                    {product.badge}
                  </span>
                )}
                <div className="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                  <i className="ri-flashlight-line mr-1"></i>
                  {product.delivery}
                </div>
              </div>

              <div className="p-6">
                <h3 className="font-semibold text-white mb-2 group-hover:text-blue-400 transition-colors">
                  {product.name}
                </h3>
                
                <div className="flex items-center justify-between mb-3">
                  <div className="flex items-center space-x-2">
                    <span className="text-2xl font-bold text-white">€{product.price}</span>
                    {product.originalPrice !== product.price && (
                      <span className="text-sm text-gray-400 line-through">€{product.originalPrice}</span>
                    )}
                  </div>
                  <div className="flex items-center space-x-1">
                    <i className="ri-star-fill text-yellow-500 text-sm"></i>
                    <span className="text-sm text-gray-300">{product.rating}</span>
                  </div>
                </div>

                <div className="flex items-center justify-between mb-4">
                  <span className={`text-sm px-2 py-1 rounded ${
                    product.stock === 'En stock' ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/20 text-orange-400'
                  }`}>
                    {product.stock}
                  </span>
                </div>

                <div className="space-y-2">
                  <button className="w-full bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform group-hover:scale-105 whitespace-nowrap">
                    <i className="ri-shopping-cart-line mr-2"></i>
                    Acheter maintenant
                  </button>
                  <button className="w-full bg-gray-700 hover:bg-gray-600 text-white py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i className="ri-heart-line mr-2"></i>
                    Ajouter aux favoris
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="text-center mt-12">
          <Link
            href="/catalogue"
            className="inline-flex items-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl whitespace-nowrap"
          >
            <i className="ri-eye-line mr-2"></i>
            Voir tous les produits
          </Link>
        </div>
      </div>
    </section>
  );
}
