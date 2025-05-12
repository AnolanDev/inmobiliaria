// src/context/AuthContext.tsx
'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import { apiClient } from '@/app/lib/apiClient';

interface User {
  id: number;
  email: string;
  fullName: string;
  role: {
    id: number;
    name: string;
  };
}

type AuthContextType = {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string, remember: boolean) => Promise<void>;
  logout: () => void;
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  // Al montar, intenta rehidratar usuario si hay token
  useEffect(() => {
    if (!mounted) return;

    const token =
      typeof window !== 'undefined'
        ? localStorage.getItem('access_token') ?? sessionStorage.getItem('access_token')
        : null;
    
    if (token) {
      apiClient.get<User>('/auth/me')
        .then((res) => {
          setUser(res.data);
        })
        .catch(() => {
          localStorage.removeItem('access_token');
          sessionStorage.removeItem('access_token');
          setUser(null);
        })
        .finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, [mounted]);

  const login = async (
    email: string,
    password: string,
    remember: boolean
  ) => {
    setLoading(true);
    try {
      const res = await apiClient.post<{ access_token: string, user: User }>('/auth/login', { email, password });
      const { access_token, user } = res.data;

      // Guardar token según preferencia
      if (remember) {
        localStorage.setItem('access_token', access_token);
      } else {
        sessionStorage.setItem('access_token', access_token);
      }

      setUser(user);
    } catch (error: any) {
      setUser(null);
      throw new Error(error.response?.data?.message || 'Error al iniciar sesión');
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    setLoading(true);
    localStorage.removeItem('access_token');
    sessionStorage.removeItem('access_token');
    setUser(null);
    setLoading(false);
  };

  if (!mounted) {
    return null;
  }

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth debe usarse dentro de AuthProvider');
  }
  return context;
}
