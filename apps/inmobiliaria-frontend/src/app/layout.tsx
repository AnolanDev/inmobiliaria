'use client';

import './globals.css';
import 'mapbox-gl/dist/mapbox-gl.css';

import { AuthProvider, useAuth } from '@/context/AuthContext';
import { Toaster } from 'react-hot-toast';
import PublicHeader from '@/components/PublicHeader';
import PrivateHeader from '@/components/PrivateHeader';
import Footer from '@/components/Footer';
import { useEffect, useState } from 'react';

function DynamicHeader() {
  const { user, loading } = useAuth();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  // Si no está montado o está cargando, mostrar un placeholder
  if (!mounted || loading) {
    return (
      <div className="fixed top-4 left-0 right-0 z-50">
        <div className="bg-white/80 backdrop-blur-md border shadow-xl rounded-xl mx-4 md:mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
          <div className="w-8 h-8 bg-gray-200 rounded-full animate-pulse" />
          <div className="hidden md:flex items-center gap-6">
            <div className="w-20 h-4 bg-gray-200 rounded animate-pulse" />
            <div className="w-20 h-4 bg-gray-200 rounded animate-pulse" />
            <div className="w-20 h-4 bg-gray-200 rounded animate-pulse" />
          </div>
          <div className="w-8 h-8 bg-gray-200 rounded-full animate-pulse" />
        </div>
      </div>
    );
  }

  // Una vez montado y cargado, mostrar el header correspondiente
  return user ? <PrivateHeader /> : <PublicHeader />;
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="es">
      <body className="bg-gray-100 min-h-screen">
        <AuthProvider>
          <DynamicHeader />
          <main className="min-h-[calc(100vh-120px)] px-4 py-6">{children}</main>
          <Footer />
          <Toaster position="top-right" />
        </AuthProvider>
      </body>
    </html>
  );
}
