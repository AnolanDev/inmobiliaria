'use client';

import { useSafeBack } from '@/hooks/useSafeBack';
import { HiArrowNarrowLeft } from 'react-icons/hi';

export default function BackButton() {
  const { goBackOrDefault } = useSafeBack();

  return (
    <button
      onClick={goBackOrDefault}
      className="fixed top-4 left-4 z-50 flex items-center gap-2 px-4 py-2 
                 bg-black/70 text-white border border-white/30 shadow-xl 
                 rounded-full hover:bg-black/80 transition-all backdrop-blur"
    >
      <HiArrowNarrowLeft className="text-lg" />
      <span className="hidden md:inline">Volver</span>
    </button>
  );
}
