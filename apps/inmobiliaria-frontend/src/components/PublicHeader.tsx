'use client';
import Link from 'next/link';
import Image from 'next/image';
import { useState, useRef, useLayoutEffect } from 'react';
import { HiMenu, HiX, HiOutlineUser } from 'react-icons/hi';

interface PublicHeaderProps {
  onHeightChange?: (height: number) => void;
}

export default function PublicHeader({ onHeightChange }: PublicHeaderProps) {
  const [isOpen, setIsOpen] = useState(false);
  const headerRef = useRef<HTMLDivElement>(null);

  useLayoutEffect(() => {
    if (headerRef.current && onHeightChange) {
      const baseHeight = headerRef.current.offsetHeight;
      const totalHeight = baseHeight + 16;
      onHeightChange(totalHeight);
    }
  }, [isOpen, onHeightChange]);

  return (
    <header className="fixed top-4 left-0 right-0 z-50">
      <div
        ref={headerRef}
        className="bg-white/80 backdrop-blur-md border shadow-xl rounded-xl mx-4 md:mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between transition-all"
      >
        <Link href="/" className="flex items-center h-full">
          <Image
            src="/logo.png"
            alt="Logo"
            width={120}
            height={60}
            priority
            className="h-full w-auto object-contain"
          />
        </Link>

        <nav className="hidden md:flex gap-6 items-center text-gray-700 font-medium">
          <NavLink href="/">Inicio</NavLink>
          <NavLink href="/conocenos">Conócenos</NavLink>
          <NavLink href="/proyectos">Proyectos</NavLink>
          <NavLink href="/contactanos">Contáctanos</NavLink>
          <NavLink href="/blog">Blog</NavLink>
          <Link
            href="/login"
            className="ml-2 text-2xl text-[#3c5ca0] hover:text-[#5ea546] transition-colors"
          >
            <HiOutlineUser />
          </Link>
        </nav>

        <button
          onClick={() => setIsOpen(prev => !prev)}
          className="md:hidden text-2xl text-gray-700 hover:text-[#5ea546] transition-colors"
          aria-label="Menú móvil"
        >
          {isOpen ? <HiX /> : <HiMenu />}
        </button>
      </div>

      {isOpen && (
        <div className="md:hidden bg-white/90 backdrop-blur-md mx-4 mt-2 p-4 rounded-xl shadow-md text-gray-700 font-medium space-y-2">
          <MobileLink href="/" onClick={() => setIsOpen(false)}>Inicio</MobileLink>
          <MobileLink href="/conocenos" onClick={() => setIsOpen(false)}>Conócenos</MobileLink>
          <MobileLink href="/proyectos" onClick={() => setIsOpen(false)}>Proyectos</MobileLink>
          <MobileLink href="/contactanos" onClick={() => setIsOpen(false)}>Contáctanos</MobileLink>
          <MobileLink href="/blog" onClick={() => setIsOpen(false)}>Blog</MobileLink>
          <MobileLink href="/login" onClick={() => setIsOpen(false)}>Iniciar sesión</MobileLink>
        </div>
      )}
    </header>
  );
}

function NavLink({ href, children }: { href: string; children: React.ReactNode }) {
  return (
    <Link href={href} className="hover:text-[#5ea546] transition-colors">
      {children}
    </Link>
  );
}

function MobileLink({ href, children, onClick }: { href: string; children: React.ReactNode; onClick?: () => void }) {
  return (
    <Link href={href} onClick={onClick} className="block py-1 px-2 rounded hover:bg-[#5ea546]/10 transition">
      {children}
    </Link>
  );
}
