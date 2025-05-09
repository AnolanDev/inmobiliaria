'use client';
import { useState } from 'react';
import PublicHeader from '@/components/PublicHeader';

export default function PublicLayout({ children }: { children: React.ReactNode }) {
  const [headerHeight, setHeaderHeight] = useState(96); // fallback

  return (
    <>
      <PublicHeader onHeightChange={setHeaderHeight} />
      <main style={{ paddingTop: `${headerHeight}px` }} className="px-4">
        {children}
      </main>
    </>
  );
}
