
'use client';

export default function TrustSection() {
  const features = [
    {
      icon: 'ri-shield-check-line',
      title: 'Paiement Sécurisé',
      description: 'Vos transactions sont protégées par un cryptage SSL 256 bits et KiaPay'
    },
    {
      icon: 'ri-flashlight-line',
      title: 'Livraison Instantanée',
      description: 'Recevez vos codes par email en quelques secondes après validation'
    },
    {
      icon: 'ri-customer-service-2-line',
      title: 'Support 24/7',
      description: 'Notre équipe est disponible 24h/24 pour vous accompagner'
    },
    {
      icon: 'ri-money-dollar-circle-line',
      title: 'Garantie Remboursement',
      description: '30 jours pour changer d\'avis, remboursement intégral garanti'
    }
  ];

  const stats = [
    { value: '500,000+', label: 'Clients satisfaits' },
    { value: '2M+', label: 'Codes vendus' },
    { value: '4.9/5', label: 'Note moyenne' },
    { value: '99.9%', label: 'Uptime garanti' }
  ];

  return (
    <section className="py-20 bg-gradient-to-br from-gray-800 to-gray-900">
      <div className="max-w-7xl mx-auto px-4">
        {/* En-tête */}
        <div className="text-center mb-16">
          <h2 className="text-4xl font-bold text-white mb-4">
            Pourquoi choisir <span className="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">CREE 2GK</span>
          </h2>
          <p className="text-xl text-gray-300 max-w-2xl mx-auto">
            La confiance de milliers de clients dans le monde entier
          </p>
        </div>

        {/* Statistiques */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          {stats.map((stat, index) => (
            <div key={index} className="text-center">
              <div className="text-3xl lg:text-4xl font-bold text-white mb-2">
                {stat.value}
              </div>
              <div className="text-gray-400">
                {stat.label}
              </div>
            </div>
          ))}
        </div>

        {/* Fonctionnalités */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((feature, index) => (
            <div key={index} className="bg-gray-900/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700/50 text-center group hover:border-blue-500/30 transition-all duration-300">
              <div className="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                <i className={`${feature.icon} text-2xl text-white`}></i>
              </div>
              <h3 className="text-lg font-semibold text-white mb-3 group-hover:text-blue-400 transition-colors">
                {feature.title}
              </h3>
              <p className="text-gray-400 text-sm leading-relaxed">
                {feature.description}
              </p>
            </div>
          ))}
        </div>

        {/* Section témoignages */}
        <div className="mt-20">
          <h3 className="text-2xl font-bold text-white text-center mb-12">
            Ce que disent nos clients
          </h3>
          
          <div className="grid md:grid-cols-3 gap-6">
            {[
              {
                name: 'Marie L.',
                rating: 5,
                comment: 'Service impeccable ! J\'ai reçu mon code Steam en moins de 2 minutes. Je recommande vivement !',
                product: 'Carte Steam 25€'
              },
              {
                name: 'Thomas R.',
                rating: 5,
                comment: 'Prix très compétitifs et livraison ultra rapide. Mon abonnement Netflix fonctionne parfaitement.',
                product: 'Netflix Premium 6 mois'
              },
              {
                name: 'Sarah M.',
                rating: 5,
                comment: 'Excellent support client. Problème résolu en quelques minutes via le chat. Très professionnel.',
                product: 'Office 2021 Pro'
              }
            ].map((testimonial, index) => (
              <div key={index} className="bg-gray-900/70 rounded-xl p-6 border border-gray-700/50">
                <div className="flex items-center mb-4">
                  <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {testimonial.name[0]}
                  </div>
                  <div className="ml-3">
                    <div className="font-medium text-white">{testimonial.name}</div>
                    <div className="flex items-center">
                      {[...Array(testimonial.rating)].map((_, i) => (
                        <i key={i} className="ri-star-fill text-yellow-500 text-sm"></i>
                      ))}
                    </div>
                  </div>
                </div>
                <p className="text-gray-300 text-sm mb-3 leading-relaxed">
                  "{testimonial.comment}"
                </p>
                <div className="text-xs text-gray-400 border-t border-gray-700 pt-3">
                  Achat vérifié : {testimonial.product}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
