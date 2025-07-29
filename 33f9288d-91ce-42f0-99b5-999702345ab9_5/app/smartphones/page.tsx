
'use client';

import Header from '../components/Header';
import Footer from '../components/Footer';
import ProductGrid from './ProductGrid';
import FilterSidebar from './FilterSidebar';
import { useState } from 'react';

export default function SmartphonesPage() {
  const [filters, setFilters] = useState({
    brand: '',
    priceRange: '',
    storage: '',
    sortBy: 'popular'
  });

  return (
    <div className="min-h-screen bg-white">
      <Header />
      
      <div className="bg-gray-50 py-12">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            Smartphones
          </h1>
          <p className="text-xl text-gray-600">
            Découvrez notre collection de smartphones dernière génération
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <div className="flex flex-col lg:flex-row gap-8">
          <div className="lg:w-1/4">
            <FilterSidebar filters={filters} setFilters={setFilters} />
          </div>
          <div className="lg:w-3/4">
            <ProductGrid filters={filters} />
          </div>
        </div>
      </div>

      <Footer />
    </div>
  );
}
