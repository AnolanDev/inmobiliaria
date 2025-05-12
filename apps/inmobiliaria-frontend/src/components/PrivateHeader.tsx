'use client';
import { useState, useRef, useLayoutEffect, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { useAuth } from '@/context/AuthContext';
import Image from 'next/image';
import { HiMenu, HiX, HiChevronDown, HiUserCircle, HiHome, HiOfficeBuilding, HiUserGroup, HiUsers } from 'react-icons/hi';
import Link from 'next/link';

interface PrivateHeaderProps {
  onHeightChange?: (height: number) => void;
}

export default function PrivateHeader({ onHeightChange }: PrivateHeaderProps) {
  const { user, logout } = useAuth();
  const router = useRouter();
  const [isOpen, setIsOpen] = useState(false);
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);
  const headerRef = useRef<HTMLDivElement>(null);
  const dropdownRef = useRef<HTMLDivElement>(null);

  useLayoutEffect(() => {
    if (headerRef.current && onHeightChange) {
      const baseHeight = headerRef.current.offsetHeight;
      const totalHeight = baseHeight + 16;
      onHeightChange(totalHeight);
    }
  }, [isOpen, onHeightChange]);

  // Cerrar dropdowns al hacer clic fuera
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      const target = event.target as HTMLElement;
      if (dropdownRef.current && !dropdownRef.current.contains(target)) {
        setOpenDropdown(null);
      }
    };

    document.addEventListener('click', handleClickOutside);
    return () => document.removeEventListener('click', handleClickOutside);
  }, []);

  const handleDropdownClick = (title: string, event: React.MouseEvent) => {
    event.stopPropagation();
    setOpenDropdown(openDropdown === title ? null : title);
  };

  const renderDropdown = (title: string, base: string, icon: React.ReactNode) => {
    const isOpen = openDropdown === title;
    
    return (
      <div className="relative" ref={dropdownRef}>
        <button
          onClick={(e) => handleDropdownClick(title, e)}
          className="flex items-center gap-1 cursor-pointer text-gray-700 hover:text-[#5ea546]"
        >
          <span className="flex items-center gap-2">
            {icon}
            {title}
          </span>
          <HiChevronDown className={`text-xs transition-transform ${isOpen ? 'rotate-180' : ''}`} />
        </button>
        {isOpen && (
          <div className="absolute z-50 bg-white rounded-md shadow-lg mt-1 min-w-[200px] border">
            <ul className="text-sm text-gray-700 py-1">
              <li>
                <Link 
                  href={`/${base}`} 
                  className="flex items-center gap-2 px-4 py-2 hover:bg-gray-50"
                  onClick={() => setOpenDropdown(null)}
                >
                  <HiOfficeBuilding className="text-lg" />
                  Listar {title.toLowerCase()}
                </Link>
              </li>
              <li>
                <Link 
                  href={`/${base}/create`} 
                  className="flex items-center gap-2 px-4 py-2 hover:bg-gray-50"
                  onClick={() => setOpenDropdown(null)}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                  </svg>
                  Crear {title.toLowerCase().slice(0, -1)}
                </Link>
              </li>
            </ul>
          </div>
        )}
      </div>
    );
  };

  return (
    <header className="fixed top-4 left-0 right-0 z-50">
      <div
        ref={headerRef}
        className="bg-white/80 backdrop-blur-md border shadow-xl rounded-xl mx-4 md:mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center transition-all"
      >
        <div className="flex items-center gap-2 cursor-pointer" onClick={() => router.push('/dashboard')}>
          <div className="relative w-8 h-8">
            <Image 
              src="/logo.png" 
              alt="Logo" 
              fill
              sizes="32px"
              className="object-contain"
            />
          </div>
          <span className="text-xl font-semibold text-[#3c5ca0]">Inmobiliaria</span>
        </div>

        <nav className="hidden md:flex items-center gap-6 text-gray-700 font-medium">
          <Link href="/dashboard" className="flex items-center gap-2 hover:text-[#5ea546]">
            <HiHome className="text-lg" />
            Inicio
          </Link>
          {renderDropdown('Proyectos', 'projects', <HiOfficeBuilding className="text-lg" />)}
          {renderDropdown('Agentes', 'agents', <HiUserGroup className="text-lg" />)}
          {renderDropdown('Usuarios', 'users', <HiUsers className="text-lg" />)}
          <button onClick={logout} className="text-red-600 hover:underline">Salir</button>
        </nav>

        {user && <HiUserCircle className="hidden md:block text-3xl text-gray-500" />}

        <button onClick={() => setIsOpen(!isOpen)} className="md:hidden text-2xl text-gray-700">
          {isOpen ? <HiX /> : <HiMenu />}
        </button>
      </div>

      {isOpen && (
        <div className="md:hidden bg-white/90 backdrop-blur-md mx-4 mt-2 p-4 rounded-xl shadow-md text-gray-700 font-medium space-y-2">
          <Link href="/dashboard" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
            <HiHome className="text-lg" />
            Inicio
          </Link>
          <div>
            <button
              onClick={(e) => handleDropdownClick('Proyectos', e)}
              className="flex items-center gap-2 py-2 w-full"
            >
              <HiOfficeBuilding className="text-lg" />
              Proyectos
              <HiChevronDown className={`text-xs transition-transform ${openDropdown === 'Proyectos' ? 'rotate-180' : ''}`} />
            </button>
            {openDropdown === 'Proyectos' && (
              <div className="ml-8 space-y-2 mt-2">
                <Link href="/projects" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <HiOfficeBuilding className="text-lg" />
                  Listar Proyectos
                </Link>
                <Link href="/projects/create" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                  </svg>
                  Crear Proyecto
                </Link>
              </div>
            )}
          </div>
          <div>
            <button
              onClick={(e) => handleDropdownClick('Agentes', e)}
              className="flex items-center gap-2 py-2 w-full"
            >
              <HiUserGroup className="text-lg" />
              Agentes
              <HiChevronDown className={`text-xs transition-transform ${openDropdown === 'Agentes' ? 'rotate-180' : ''}`} />
            </button>
            {openDropdown === 'Agentes' && (
              <div className="ml-8 space-y-2 mt-2">
                <Link href="/agents" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <HiUserGroup className="text-lg" />
                  Listar Agentes
                </Link>
                <Link href="/agents/create" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                  </svg>
                  Crear Agente
                </Link>
              </div>
            )}
          </div>
          <div>
            <button
              onClick={(e) => handleDropdownClick('Usuarios', e)}
              className="flex items-center gap-2 py-2 w-full"
            >
              <HiUsers className="text-lg" />
              Usuarios
              <HiChevronDown className={`text-xs transition-transform ${openDropdown === 'Usuarios' ? 'rotate-180' : ''}`} />
            </button>
            {openDropdown === 'Usuarios' && (
              <div className="ml-8 space-y-2 mt-2">
                <Link href="/users" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <HiUsers className="text-lg" />
                  Listar Usuarios
                </Link>
                <Link href="/users/create" className="flex items-center gap-2 py-2" onClick={() => setIsOpen(false)}>
                  <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                  </svg>
                  Crear Usuario
                </Link>
              </div>
            )}
          </div>
          <button onClick={logout} className="text-red-600 flex items-center gap-2 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fillRule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v3a1 1 0 102 0V9z" clipRule="evenodd" />
            </svg>
            Salir
          </button>
        </div>
      )}
    </header>
  );
}
