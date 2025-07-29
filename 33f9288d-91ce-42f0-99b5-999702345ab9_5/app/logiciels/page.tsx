
'use client';

import Navigation from '../components/Navigation';
import FooterDigital from '../components/FooterDigital';
import { useState } from 'react';

export default function LogicielsPage() {
  const [filters, setFilters] = useState({
    category: '',
    brand: '',
    sortBy: 'popular'
  });
  const [favorites, setFavorites] = useState<number[]>([]);

  const softwareProducts = [
    {
      id: 1,
      name: 'Microsoft Office 2021 Pro',
      price: '49.99',
      originalPrice: '439.99',
      category: 'Bureautique',
      brand: 'Microsoft',
      image: 'https://readdy.ai/api/search-image?query=Microsoft%20Office%202021%20Professional%20software%20license%20productivity%20suite%20digital%20key%20dark%20background%20blue%20Microsoft%20colors%20business%20professional%20product%20image&width=300&height=200&seq=office-2021-pro&orientation=landscape',
      badge: 'MEGA PROMO',
      badgeColor: 'bg-red-600',
      delivery: 'Instantané',
      rating: 4.9,
      stock: 'En stock',
      description: 'Suite bureautique complète Word, Excel, PowerPoint',
      features: ['Word', 'Excel', 'PowerPoint', 'Outlook', 'Access', 'Publisher']
    },
    {
      id: 2,
      name: 'Windows 11 Pro',
      price: '19.99',
      originalPrice: '259.99',
      category: 'Système',
      brand: 'Microsoft',
      image: 'https://readdy.ai/api/search-image?query=Windows%2011%20Professional%20operating%20system%20software%20license%20digital%20key%20dark%20background%20blue%20gradient%20modern%20professional%20product%20image%20with%20Windows%20logo&width=300&height=200&seq=windows-11-pro&orientation=landscape',
      badge: 'TOP VENTE',
      badgeColor: 'bg-blue-500',
      delivery: 'Instantané',
      rating: 4.8,
      stock: 'En stock',
      description: 'Système d\'exploitation Windows 11 Professionnel',
      features: ['Interface moderne', 'Sécurité renforcée', 'Microsoft Teams', 'Widgets', 'Snap Layouts']
    },
    {
      id: 3,
      name: 'Adobe Creative Cloud 1 an',
      price: '299.99',
      originalPrice: '659.88',
      category: 'Créatif',
      brand: 'Adobe',
      image: 'https://readdy.ai/api/search-image?query=Adobe%20Creative%20Cloud%20subscription%201%20year%20creative%20software%20suite%20digital%20license%20dark%20background%20colorful%20gradient%20professional%20design%20product%20image%20with%20Adobe%20logo&width=300&height=200&seq=adobe-cc-annual&orientation=landscape',
      badge: 'CRÉATIF',
      badgeColor: 'bg-purple-500',
      delivery: '10-30 min',
      rating: 4.7,
      stock: 'Stock limité',
      description: 'Suite créative complète Adobe CC',
      features: ['Photoshop', 'Illustrator', 'Premiere Pro', 'After Effects', 'InDesign', '20+ apps']
    },
    {
      id: 4,
      name: 'Kaspersky Total Security',
      price: '24.99',
      originalPrice: '49.99',
      category: 'Sécurité',
      brand: 'Kaspersky',
      image: 'https://readdy.ai/api/search-image?query=Kaspersky%20Total%20Security%20antivirus%20software%20license%20cybersecurity%20protection%20digital%20key%20dark%20background%20green%20colors%20professional%20product%20image%20with%20Kaspersky%20logo&width=300&height=200&seq=kaspersky-total-security&orientation=landscape',
      badge: 'SÉCURITÉ',
      badgeColor: 'bg-green-600',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Protection complète contre les menaces',
      features: ['Antivirus', 'Firewall', 'VPN', 'Contrôle parental', 'Safe Money', 'Password Manager']
    },
    {
      id: 5,
      name: 'Autodesk AutoCAD 2024',
      price: '199.99',
      originalPrice: '1690.00',
      category: 'CAO',
      brand: 'Autodesk',
      image: 'https://readdy.ai/api/search-image?query=Autodesk%20AutoCAD%202024%20professional%20CAD%20software%20license%20digital%20key%20dark%20background%20blue%20engineering%20colors%20professional%20product%20image%20with%20Autodesk%20logo&width=300&height=200&seq=autocad-2024&orientation=landscape',
      badge: 'PROFESSIONNEL',
      badgeColor: 'bg-blue-600',
      delivery: 'Manuel',
      rating: 4.8,
      stock: 'En stock',
      description: 'Logiciel de conception assistée par ordinateur',
      features: ['Dessin 2D/3D', 'Bibliothèques', 'Collaboration', 'API', 'Spécialisations', 'Applications mobiles']
    },
    {
      id: 6,
      name: 'Norton 360 Deluxe',
      price: '39.99',
      originalPrice: '79.99',
      category: 'Sécurité',
      brand: 'Norton',
      image: 'https://readdy.ai/api/search-image?query=Norton%20360%20Deluxe%20antivirus%20security%20software%20license%20digital%20key%20dark%20background%20yellow%20Norton%20colors%20professional%20product%20image%20with%20Norton%20logo&width=300&height=200&seq=norton-360-deluxe&orientation=landscape',
      badge: 'PROTECTION',
      badgeColor: 'bg-yellow-500',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Sécurité complète pour 5 appareils',
      features: ['Antivirus', 'VPN illimité', 'Dark Web Monitoring', 'Sauvegarde cloud', 'Contrôle parental']
    },
    {
      id: 7,
      name: 'VMware Workstation Pro',
      price: '149.99',
      originalPrice: '249.99',
      category: 'Virtualisation',
      brand: 'VMware',
      image: 'https://readdy.ai/api/search-image?query=VMware%20Workstation%20Pro%20virtualization%20software%20license%20digital%20key%20dark%20background%20blue%20VMware%20colors%20professional%20product%20image%20with%20VMware%20logo&width=300&height=200&seq=vmware-workstation-pro&orientation=landscape',
      badge: 'DÉVELOPPEUR',
      badgeColor: 'bg-indigo-500',
      delivery: 'Instantané',
      rating: 4.7,
      stock: 'En stock',
      description: 'Virtualisation professionnelle',
      features: ['Machines virtuelles', 'Snapshots', 'Clones', 'Networking', '3D Graphics', 'vSphere']
    },
    {
      id: 8,
      name: 'Corel VideoStudio Ultimate',
      price: '79.99',
      originalPrice: '129.99',
      category: 'Créatif',
      brand: 'Corel',
      image: 'https://readdy.ai/api/search-image?query=Corel%20VideoStudio%20Ultimate%20video%20editing%20software%20license%20digital%20key%20dark%20background%20red%20Corel%20colors%20professional%20product%20image%20with%20Corel%20logo&width=300&height=200&seq=corel-videostudio-ultimate&orientation=landscape',
      badge: 'MONTAGE',
      badgeColor: 'bg-red-500',
      delivery: 'Instantané',
      rating: 4.4,
      stock: 'En stock',
      description: 'Montage vidéo professionnel',
      features: ['Montage multi-pistes', 'Effets 3D', 'Titrage', 'Motion Tracking', 'Écran vert', 'Audio']
    },
    {
      id: 9,
      name: 'Malwarebytes Premium',
      price: '29.99',
      originalPrice: '39.99',
      category: 'Sécurité',
      brand: 'Malwarebytes',
      image: 'https://readdy.ai/api/search-image?query=Malwarebytes%20Premium%20anti-malware%20security%20software%20license%20digital%20key%20dark%20background%20orange%20colors%20professional%20product%20image%20with%20Malwarebytes%20logo&width=300&height=200&seq=malwarebytes-premium&orientation=landscape',
      badge: 'ANTI-MALWARE',
      badgeColor: 'bg-orange-500',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Protection avancée contre les malwares',
      features: ['Détection temps réel', 'Quarantaine', 'Blocage web', 'Programmation', 'Rapports', 'Support']
    },
    {
      id: 10,
      name: 'Acronis True Image',
      price: '49.99',
      originalPrice: '59.99',
      category: 'Sauvegarde',
      brand: 'Acronis',
      image: 'https://readdy.ai/api/search-image?query=Acronis%20True%20Image%20backup%20software%20license%20digital%20key%20dark%20background%20blue%20Acronis%20colors%20professional%20product%20image%20with%20Acronis%20logo&width=300&height=200&seq=acronis-true-image&orientation=landscape',
      badge: 'SAUVEGARDE',
      badgeColor: 'bg-blue-500',
      delivery: 'Instantané',
      rating: 4.5,
      stock: 'En stock',
      description: 'Sauvegarde et récupération complète',
      features: ['Sauvegarde complète', 'Clonage disque', 'Cloud backup', 'Restauration', 'Chiffrement', 'Planification']
    },
    {
      id: 11,
      name: 'JetBrains IntelliJ IDEA',
      price: '89.99',
      originalPrice: '149.00',
      category: 'Développement',
      brand: 'JetBrains',
      image: 'https://readdy.ai/api/search-image?query=JetBrains%20IntelliJ%20IDEA%20development%20IDE%20software%20license%20digital%20key%20dark%20background%20orange%20JetBrains%20colors%20professional%20product%20image%20with%20JetBrains%20logo&width=300&height=200&seq=intellij-idea&orientation=landscape',
      badge: 'DÉVELOPPEUR',
      badgeColor: 'bg-orange-600',
      delivery: 'Instantané',
      rating: 4.8,
      stock: 'En stock',
      description: 'IDE Java le plus intelligent',
      features: ['Java/Kotlin', 'Débogage', 'Refactoring', 'Git', 'Frameworks', 'Plugins']
    },
    {
      id: 12,
      name: 'Parallels Desktop Mac',
      price: '79.99',
      originalPrice: '99.99',
      category: 'Virtualisation',
      brand: 'Parallels',
      image: 'https://readdy.ai/api/search-image?query=Parallels%20Desktop%20Mac%20virtualization%20software%20license%20digital%20key%20dark%20background%20blue%20Parallels%20colors%20professional%20product%20image%20with%20Parallels%20logo&width=300&height=200&seq=parallels-desktop-mac&orientation=landscape',
      badge: 'MAC',
      badgeColor: 'bg-gray-600',
      delivery: 'Instantané',
      rating: 4.6,
      stock: 'En stock',
      description: 'Virtualisation sur Mac',
      features: ['Windows sur Mac', 'Coherence', 'Snapshots', 'Drag & Drop', 'Shared folders', 'Performance']
    }
  ];

  const toggleFavorite = (productId: number) => {
    setFavorites(prev => 
      prev.includes(productId) 
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const filteredProducts = softwareProducts.filter(product => {
    if (filters.category && product.category !== filters.category) return false;
    if (filters.brand && product.brand !== filters.brand) return false;
    return true;
  });

  const sortedProducts = [...filteredProducts].sort((a, b) => {
    switch (filters.sortBy) {
      case 'price-low':
        return parseFloat(a.price) - parseFloat(b.price);
      case 'price-high':
        return parseFloat(b.price) - parseFloat(a.price);
      case 'rating':
        return b.rating - a.rating;
      default:
        return 0;
    }
  });

  return (
    <div className="min-h-screen bg-gray-900">
      <Navigation />
      
      <div className="bg-gradient-to-r from-blue-900 to-green-900 py-20">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold text-white mb-6">
            Logiciels & <span className="bg-gradient-to-r from-blue-400 to-green-400 bg-clip-text text-transparent">Outils</span>
          </h1>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Licences logicielles originales à prix réduits. Windows, Office, Adobe, antivirus et plus !
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-12">
        <div className="flex flex-col lg:flex-row gap-8">
          {/* Filtres */}
          <div className="lg:w-1/4">
            <div className="bg-gray-800 rounded-xl p-6 border border-gray-700 sticky top-24">
              <h3 className="text-xl font-bold text-white mb-6">Filtres</h3>
              
              <div className="space-y-6">
                <div>
                  <h4 className="font-semibold text-white mb-3">Catégorie</h4>
                  <div className="space-y-2">
                    {['Bureautique', 'Système', 'Créatif', 'Sécurité', 'CAO', 'Développement'].map(category => (
                      <label key={category} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="category"
                          value={category}
                          checked={filters.category === category}
                          onChange={(e) => setFilters({...filters, category: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{category}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div>
                  <h4 className="font-semibold text-white mb-3">Marque</h4>
                  <div className="space-y-2">
                    {['Microsoft', 'Adobe', 'Kaspersky', 'Norton', 'Autodesk', 'VMware'].map(brand => (
                      <label key={brand} className="flex items-center cursor-pointer">
                        <input
                          type="radio"
                          name="brand"
                          value={brand}
                          checked={filters.brand === brand}
                          onChange={(e) => setFilters({...filters, brand: e.target.value})}
                          className="mr-2"
                        />
                        <span className="text-gray-300">{brand}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <button 
                  onClick={() => setFilters({category: '', brand: '', sortBy: 'popular'})}
                  className="w-full bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors cursor-pointer whitespace-nowrap"
                >
                  Effacer les filtres
                </button>
              </div>
            </div>
          </div>

          {/* Produits */}
          <div className="lg:w-3/4">
            <div className="flex items-center justify-between mb-8">
              <p className="text-gray-300">
                {sortedProducts.length} logiciels disponibles
              </p>
              <select 
                value={filters.sortBy} 
                onChange={(e) => setFilters({...filters, sortBy: e.target.value})}
                className="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white pr-8"
              >
                <option value="popular">Popularité</option>
                <option value="price-low">Prix croissant</option>
                <option value="price-high">Prix décroissant</option>
                <option value="rating">Meilleures notes</option>
              </select>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {sortedProducts.map((product) => (
                <div key={product.id} className="bg-gray-800 rounded-xl border border-gray-700 hover:border-gray-600 transition-all duration-300 group hover:-translate-y-2 hover:shadow-2xl">
                  <div className="relative">
                    <img
                      src={product.image}
                      alt={product.name}
                      className="w-full h-48 object-cover rounded-t-xl"
                    />
                    {product.badge && (
                      <span className={`absolute top-3 left-3 ${product.badgeColor} text-white px-3 py-1 rounded-full text-sm font-bold`}>
                        {product.badge}
                      </span>
                    )}
                    <div className="absolute top-3 right-3 bg-black/50 text-white px-2 py-1 rounded-full text-xs flex items-center">
                      <i className="ri-flashlight-line mr-1"></i>
                      {product.delivery}
                    </div>
                    <button 
                      onClick={() => toggleFavorite(product.id)}
                      className="absolute top-12 right-3 p-2 bg-black/50 rounded-full hover:bg-black/70 transition-colors cursor-pointer"
                    >
                      <div className="w-4 h-4 flex items-center justify-center">
                        <i className={`${favorites.includes(product.id) ? 'ri-heart-fill text-red-500' : 'ri-heart-line text-white'}`}></i>
                      </div>
                    </button>
                  </div>

                  <div className="p-6">
                    <h3 className="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">
                      {product.name}
                    </h3>
                    <p className="text-gray-400 text-sm mb-3">{product.description}</p>
                    
                    <div className="flex items-center justify-between mb-4">
                      <div className="flex items-center space-x-2">
                        <span className="text-2xl font-bold text-white">€{product.price}</span>
                        {product.originalPrice !== product.price && (
                          <span className="text-sm text-gray-400 line-through">€{product.originalPrice}</span>
                        )}
                      </div>
                      <div className="flex items-center space-x-1">
                        <div className="w-4 h-4 flex items-center justify-center">
                          <i className="ri-star-fill text-yellow-500"></i>
                        </div>
                        <span className="text-sm text-gray-300">{product.rating}</span>
                      </div>
                    </div>

                    <div className="mb-4">
                      <div className="flex flex-wrap gap-1">
                        {product.features.slice(0, 3).map((feature, index) => (
                          <span key={index} className="text-xs bg-gray-700 text-gray-300 px-2 py-1 rounded">
                            {feature}
                          </span>
                        ))}
                      </div>
                    </div>

                    <div className="flex items-center justify-between mb-4">
                      <span className="text-sm bg-green-500/20 text-green-400 px-3 py-1 rounded-full">
                        {product.stock}
                      </span>
                      <span className="text-sm text-blue-400">
                        {product.category}
                      </span>
                    </div>

                    <button className="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 whitespace-nowrap cursor-pointer">
                      <i className="ri-shopping-cart-line mr-2"></i>
                      Acheter maintenant
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <FooterDigital />
    </div>
  );
}
