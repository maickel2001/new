
'use client';

interface FilterSidebarProps {
  filters: {
    brand: string;
    priceRange: string;
    storage: string;
    sortBy: string;
  };
  setFilters: (filters: any) => void;
}

export default function FilterSidebar({ filters, setFilters }: FilterSidebarProps) {
  const brands = ['Apple', 'Samsung', 'Google', 'OnePlus', 'Xiaomi', 'Huawei'];
  const priceRanges = ['0-300€', '300-600€', '600-900€', '900-1200€', '1200€+'];
  const storageOptions = ['128GB', '256GB', '512GB', '1TB'];
  const sortOptions = [
    { value: 'popular', label: 'Plus populaires' },
    { value: 'price-low', label: 'Prix croissant' },
    { value: 'price-high', label: 'Prix décroissant' },
    { value: 'newest', label: 'Plus récents' }
  ];

  return (
    <div className="bg-white p-6 rounded-lg shadow-md">
      <h3 className="text-xl font-bold text-gray-900 mb-6">Filtres</h3>
      
      <div className="space-y-6">
        <div>
          <h4 className="text-lg font-semibold text-gray-800 mb-3">Marque</h4>
          <div className="space-y-2">
            {brands.map((brand) => (
              <label key={brand} className="flex items-center cursor-pointer">
                <input
                  type="radio"
                  name="brand"
                  value={brand}
                  checked={filters.brand === brand}
                  onChange={(e) => setFilters({ ...filters, brand: e.target.value })}
                  className="mr-3"
                />
                <span className="text-gray-700">{brand}</span>
              </label>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-lg font-semibold text-gray-800 mb-3">Prix</h4>
          <div className="space-y-2">
            {priceRanges.map((range) => (
              <label key={range} className="flex items-center cursor-pointer">
                <input
                  type="radio"
                  name="priceRange"
                  value={range}
                  checked={filters.priceRange === range}
                  onChange={(e) => setFilters({ ...filters, priceRange: e.target.value })}
                  className="mr-3"
                />
                <span className="text-gray-700">{range}</span>
              </label>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-lg font-semibold text-gray-800 mb-3">Stockage</h4>
          <div className="space-y-2">
            {storageOptions.map((storage) => (
              <label key={storage} className="flex items-center cursor-pointer">
                <input
                  type="radio"
                  name="storage"
                  value={storage}
                  checked={filters.storage === storage}
                  onChange={(e) => setFilters({ ...filters, storage: e.target.value })}
                  className="mr-3"
                />
                <span className="text-gray-700">{storage}</span>
              </label>
            ))}
          </div>
        </div>

        <div>
          <h4 className="text-lg font-semibold text-gray-800 mb-3">Trier par</h4>
          <div className="relative">
            <select
              value={filters.sortBy}
              onChange={(e) => setFilters({ ...filters, sortBy: e.target.value })}
              className="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-8"
            >
              {sortOptions.map((option) => (
                <option key={option.value} value={option.value}>
                  {option.label}
                </option>
              ))}
            </select>
          </div>
        </div>

        <button
          onClick={() => setFilters({ brand: '', priceRange: '', storage: '', sortBy: 'popular' })}
          className="w-full bg-gray-600 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors whitespace-nowrap cursor-pointer"
        >
          Réinitialiser les filtres
        </button>
      </div>
    </div>
  );
}
