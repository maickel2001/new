
'use client';

import Navigation from './components/Navigation';
import HeroDigital from './components/HeroDigital';
import CategoriesGrid from './components/CategoriesGrid';
import FeaturedDigital from './components/FeaturedDigital';
import TrustSection from './components/TrustSection';
import FooterDigital from './components/FooterDigital';

export default function Home() {
  return (
    <div className="min-h-screen bg-gray-900">
      <Navigation />
      <HeroDigital />
      <CategoriesGrid />
      <FeaturedDigital />
      <TrustSection />
      <FooterDigital />
    </div>
  );
}
