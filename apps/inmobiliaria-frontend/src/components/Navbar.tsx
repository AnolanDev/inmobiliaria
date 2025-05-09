'use client';

import { useAuth } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import { HiUserCircle } from 'react-icons/hi';

export default function Navbar() {
  const { user, logout } = useAuth();
  const router = useRouter();

  const handleLogout = () => {
    logout();
    router.push('/login');
  };

  return (
    <nav className="w-full bg-white shadow-sm border-b px-4 sm:px-8 py-3 flex justify-between items-center">
      {/* Logo */}
      <div className="flex items-center gap-2 cursor-pointer" onClick={() => router.push('/projects')}>
        <Image src="/logo.png" alt="Logo" width={32} height={32} priority style={{ height: 'auto' }} />
        <span className="text-xl font-semibold text-[#3c5ca0]">Inmobiliaria</span>
      </div>

      {/* Navegación */}
      <div className="hidden md:flex items-center gap-6 text-gray-700 font-medium">
        <button onClick={() => router.push('/')} className="hover:text-green-600">Inicio</button>
        <button onClick={() => router.push('/projects')} className="hover:text-green-600">Proyectos</button>
        <button onClick={() => router.push('/agents')} className="hover:text-green-600">Agentes</button>
        <button onClick={() => router.push('/blog')} className="hover:text-green-600">Blog</button>
      </div>

      {/* Usuario y logout */}
      {user && (
        <div className="flex items-center gap-3">
          <HiUserCircle className="text-3xl text-gray-500" />
          <button
            onClick={handleLogout}
            className="bg-[#3c5ca0] text-white px-3 py-1.5 rounded-md text-sm font-medium hover:bg-[#2e4980] transition"
          >
            Cerrar sesión
          </button>
        </div>
      )}
    </nav>
  );
}
