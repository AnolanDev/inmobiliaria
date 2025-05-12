// src/app/auth/login/page.tsx
'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import { HiMail, HiLockClosed } from 'react-icons/hi';
import { useAuth } from '@/context/AuthContext';

export default function LoginPage() {
  const { login } = useAuth();
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await login(email, password, remember);
      router.push('/dashboard');
    } catch (err: any) {
      setError(err?.response?.data?.message || '❌ Usuario o contraseña incorrectos.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-tr from-green-200 via-green-300 to-green-200 flex items-center justify-center px-4">
      <div className="w-full max-w-sm bg-white shadow-xl rounded-2xl px-6 py-8 space-y-6">
        <div className="flex justify-center">
          <Image
            src="/logo.png"
            alt="Logo"
            width={150}
            height={75}
            priority
            className="w-auto h-auto"
          />
        </div>

        <h2 className="text-center text-2xl font-bold text-slate-900">Iniciar sesión</h2>

        {error && (
          <div
            role="alert"
            className="bg-red-100 text-red-700 border border-red-300 rounded px-4 py-2 text-sm text-center"
          >
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4" aria-busy={loading}>
          <fieldset disabled={loading} className="space-y-4">
            <div className="flex items-center gap-2 border rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-green-500">
              <HiMail className="text-gray-400 text-lg" />
              <label htmlFor="email" className="sr-only">Correo electrónico</label>
              <input
                id="email"
                name="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Correo electrónico"
                className="flex-1 outline-none bg-transparent text-sm"
                required
              />
            </div>

            <div className="flex items-center gap-2 border rounded-lg px-3 py-2 focus-within:ring-2 focus-within:ring-offset-1 focus-within:ring-green-500">
              <HiLockClosed className="text-gray-400 text-lg" />
              <label htmlFor="password" className="sr-only">Contraseña</label>
              <input
                id="password"
                name="password"
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Contraseña"
                className="flex-1 outline-none bg-transparent text-sm"
                required
              />
            </div>

            <div className="flex justify-between items-center text-sm">
              <label htmlFor="remember" className="flex items-center gap-2">
                <input
                  id="remember"
                  type="checkbox"
                  checked={remember}
                  onChange={(e) => setRemember(e.target.checked)}
                  className="form-checkbox text-green-600 focus:ring-green-500"
                />
                Recordar sesión
              </label>
              <Link href="/reset-password" className="text-green-600 hover:underline">
                ¿Olvidaste tu contraseña?
              </Link>
            </div>
          </fieldset>

          <button
            type="submit"
            disabled={loading}
            className="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 rounded-lg transition-all"
            aria-live="polite"
          >
            {loading ? 'Ingresando...' : 'Acceder'}
          </button>
        </form>
      </div>
    </div>
  );
}
