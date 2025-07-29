
'use client';

import Link from 'next/link';

export default function CategorySection() {
  const categories = [
    {
      name: 'Smartphones',
      description: 'Derniers modèles iPhone, Samsung, Google Pixel',
      image: 'https://readdy.ai/api/search-image?query=Premium%20smartphone%20collection%20featuring%20latest%20iPhone%20and%20Samsung%20models%20on%20clean%20white%20background%2C%20professional%20product%20photography%20with%20soft%20lighting%2C%20modern%20minimal%20aesthetic%2C%20high-end%20mobile%20devices%20arranged%20elegantly%2C%20commercial%20product%20showcase%20style&width=400&height=300&seq=cat-phones&orientation=landscape',
      link: '/smartphones'
    },
    {
      name: 'Tablettes',
      description: 'iPad, Galaxy Tab, et tablettes Android',
      image: 'https://readdy.ai/api/search-image?query=Modern%20tablet%20collection%20including%20iPad%20and%20Android%20tablets%20on%20pristine%20white%20surface%2C%20professional%20product%20photography%20with%20elegant%20lighting%2C%20sleek%20contemporary%20design%2C%20premium%20tablet%20devices%20displayed%20beautifully%2C%20commercial%20showcase%20aesthetic&width=400&height=300&seq=cat-tablets&orientation=landscape',
      link: '/tablettes'
    },
    {
      name: 'Accessoires',
      description: 'Coques, chargeurs, écouteurs et plus',
      image: 'https://readdy.ai/api/search-image?query=Premium%20mobile%20accessories%20collection%20including%20wireless%20earbuds%2C%20phone%20cases%2C%20chargers%20and%20cables%20on%20clean%20white%20background%2C%20professional%20product%20photography%2C%20modern%20minimalist%20style%2C%20high-quality%20tech%20accessories%20elegantly%20arranged&width=400&height=300&seq=cat-accessories&orientation=landscape',
      link: '/accessoires'
    }
  ];

  return (
    <section className="py-20 bg-gray-50">
      <div className="container mx-auto px-4">
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-gray-900 mb-4">
            Nos catégories
          </h2>
          <p className="text-xl text-gray-600 max-w-2xl mx-auto">
            Explorez notre sélection d'appareils électroniques de qualité premium
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {categories.map((category, index) => (
            <Link href={category.link} key={index}>
              <div className="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer group">
                <div className="relative h-64 overflow-hidden">
                  <img
                    src={category.image}
                    alt={category.name}
                    className="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300"
                  />
                  <div className="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-10 transition-opacity"></div>
                </div>
                <div className="p-6">
                  <h3 className="text-2xl font-bold text-gray-900 mb-2">
                    {category.name}
                  </h3>
                  <p className="text-gray-600 mb-4">
                    {category.description}
                  </p>
                  <div className="flex items-center text-blue-600 font-semibold">
                    <span className="whitespace-nowrap">Découvrir</span>
                    <div className="w-4 h-4 flex items-center justify-center ml-2">
                      <i className="ri-arrow-right-line"></i>
                    </div>
                  </div>
                </div>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
