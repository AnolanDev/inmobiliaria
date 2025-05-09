'use client';

import { useRouter } from 'next/navigation';
import { useEffect, useRef } from 'react';

export function useSafeBack(defaultPath = '/projects') {
  const router = useRouter();
  const hasHistory = useRef(false);

  useEffect(() => {
    // Si hay historial previo (navegación hacia esta página), lo detectamos
    hasHistory.current = window.history.length > 1;
  }, []);

  function goBackOrDefault() {
    if (hasHistory.current) {
      router.back();
    } else {
      router.push(defaultPath);
    }
  }

  return { goBackOrDefault };
}
