'use client';
import { useState, useRef, useLayoutEffect } from 'react';
import { useRouter } from 'next/navigation';
import { useAuth } from '@/context/AuthContext';
import Image from 'next/image';
import { HiMenu, HiX, HiChevronDown, HiUserCircle } from 'react-icons/hi';
import Link from 'next/link';

interface PrivateHeaderProps {
  onHeightChange?: (height: number) => void;
}

export default function PrivateHeader({ onHeightChange }: PrivateHeaderProps) {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [isOpen, setIsOpen] = useState(false);
  const headerRef = useRef<HTMLDivElement>(null);

  useLayoutEffect(() => {
    if (headerRef.current && onHeightChange) {
      const baseHeight = headerRef.current.offsetHeight;
      const totalHeight = baseHeight + 16;
      onHeightChange(totalHeight);
    }
  }, [isOpen, onHeightChange]);

  const renderDropdown = (title: string, base: string) => (
    <details className="group relative cursor-pointer">
      <summary className="flex items-center gap-1 cursor-pointer text-gray-700 hover:text-[#5ea546]">
        {title}
        <HiChevronDown className="text-xs" />
      </summary>
      <div className="absolute z-50 bg-white rounded-md shadow-md mt-1 group-open:block hidden">
        <ul className="text-sm text-gray-700 min-w-[160px] py-1">
          <li>
            <Link href={`/${base}`} className="block px-4 py-2 hover:bg-gray-100">Listar</Link>
          </li>
          <li>
            <Link href={`/${base}/create`} className="block px-4 py-2 hover:bg-gray-100">Crear</Link>
          </li>
        </ul>
      </div>
    </details>
  );

  return (
    <header className="fixed top-4 left-0 right-0 z-50">
      <div
        ref={headerRef}
        className="bg-white/80 backdrop-blur-md border shadow-xl rounded-xl mx-4 md:mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center transition-all"
      >
        <div className="flex items-center gap-2 cursor-pointer" onClick={() => router.push('/dashboard')}>
          <Image src="/logo.png" alt="Logo" width={32} height={32} className="h-auto w-auto object-contain" />
          <span className="text-xl font-semibold text-[#3c5ca0]">Inmobiliaria</span>
        </div>

        <nav className="hidden md:flex items-center gap-6 text-gray-700 font-medium">
          <Link href="/dashboard" className="hover:text-[#5ea546]">Inicio</Link>
          {renderDropdown('Proyectos', 'projects')}
          {renderDropdown('Agentes', 'agents')}
          {renderDropdown('Usuarios', 'users')}
          <button onClick={logout} className="text-red-600 hover:underline">Salir</button>
        </nav>

        {user && <HiUserCircle className="hidden md:block text-3xl text-gray-500" />}

        <button onClick={() => setIsOpen(!isOpen)} className="md:hidden text-2xl text-gray-700">
          {isOpen ? <HiX /> : <HiMenu />}
        </button>
      </div>

      {isOpen && (
        <div className="md:hidden bg-white/90 backdrop-blur-md mx-4 mt-2 p-4 rounded-xl shadow-md text-gray-700 font-medium space-y-2">
          <Link href="/dashboard">Inicio</Link>
          <details>
            <summary>Proyectos</summary>
            <div className="ml-4 space-y-1">
              <Link href="/projects">Listar</Link>
              <Link href="/projects/create">Crear</Link>
            </div>
          </details>
          <details>
            <summary>Agentes</summary>
            <div className="ml-4 space-y-1">
              <Link href="/agents">Listar</Link>
              <Link href="/agents/create">Crear</Link>
            </div>
          </details>
          <details>
            <summary>Usuarios</summary>
            <div className="ml-4 space-y-1">
              <Link href="/users">Listar</Link>
              <Link href="/users/create">Crear</Link>
            </div>
          </details>
          <button onClick={logout} className="text-red-600">Salir</button>
        </div>
      )}
    </header>
  );
}
