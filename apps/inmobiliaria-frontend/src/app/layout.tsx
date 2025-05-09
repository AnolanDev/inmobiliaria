'use client';

import './globals.css';
import 'mapbox-gl/dist/mapbox-gl.css';

import { AuthProvider, useAuth } from '@/context/AuthContext';
import { Toaster } from 'react-hot-toast';
import PublicHeader from '@/components/PublicHeader';
import PrivateHeader from '@/components/PrivateHeader';
import Footer from '@/components/Footer';

function DynamicHeader() {
  const { user } = useAuth();
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
