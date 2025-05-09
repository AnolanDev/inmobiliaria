'use client';
import { useState } from 'react';
import PrivateHeader from '@/components/PrivateHeader';

export default function PrivateLayout({ children }: { children: React.ReactNode }) {
  const [headerHeight, setHeaderHeight] = useState(96);

  return (
    <>
      <PrivateHeader onHeightChange={setHeaderHeight} />
      <main style={{ paddingTop: `${headerHeight}px` }} className="px-4">
        {children}
      </main>
    </>
  );
}
