
'use client';

import { useState } from 'react';

export default function Newsletter() {
  const [email, setEmail] = useState('');
  const [isSubscribed, setIsSubscribed] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (email) {
      setIsSubscribed(true);
      setEmail('');
      setTimeout(() => setIsSubscribed(false), 3000);
    }
  };

  return (
    <section className="py-20 bg-blue-600">
      <div className="container mx-auto px-4">
        <div className="text-center">
          <h2 className="text-4xl font-bold text-white mb-4">
            Restez informé des nouveautés
          </h2>
          <p className="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Recevez nos offres exclusives, nouveaux produits et conseils tech directement dans votre boîte mail
          </p>
          
          <form onSubmit={handleSubmit} className="max-w-md mx-auto">
            <div className="flex gap-4">
              <input
                type="email"
                placeholder="Votre adresse email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="flex-1 px-6 py-4 rounded-full text-gray-900 focus:outline-none focus:ring-2 focus:ring-white text-lg"
                required
              />
              <button
                type="submit"
                className="bg-white text-blue-600 px-8 py-4 rounded-full font-semibold hover:bg-gray-100 transition-colors whitespace-nowrap cursor-pointer"
              >
                S'abonner
              </button>
            </div>
          </form>

          {isSubscribed && (
            <div className="mt-6 bg-green-500 text-white px-6 py-3 rounded-full inline-block">
              <div className="flex items-center gap-2">
                <div className="w-5 h-5 flex items-center justify-center">
                  <i className="ri-check-line"></i>
                </div>
                <span>Merci pour votre inscription !</span>
              </div>
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
