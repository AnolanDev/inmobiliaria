'use client';

import { toast } from 'react-hot-toast';

const BASE_URL = process.env.NEXT_PUBLIC_BACKEND_URL;

async function request(method: string, path: string, data?: any) {
  const token = typeof window !== 'undefined' ? localStorage.getItem('token') : null;

  const isFormData = typeof FormData !== 'undefined' && data instanceof FormData;

  const headers: Record<string, string> = {
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };

  if (!isFormData) {
    headers['Content-Type'] = 'application/json';
  }

  const res = await fetch(`${BASE_URL}${path}`, {
    method,
    headers,
    body: data ? (isFormData ? data : JSON.stringify(data)) : undefined,
  });

  if (res.status === 401) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    if (typeof window !== 'undefined') {
      toast.error('Sesión expirada, vuelve a iniciar sesión.');
      window.location.href = '/login';
    }
    throw new Error('No autorizado');
  }

  if (!res.ok) {
    let message = 'Error en la API';

    try {
      const errorData = await res.json();
      message = errorData?.message || message;
      console.error('📛 Error del backend:', errorData);
    } catch (e) {
      console.error('📛 Error sin JSON válido', e);
    }

    toast.error(message);
    throw new Error(message); // error.message será accesible en el catch
  }

  return res.json();
}

export const apiClient = {
  get: (path: string) => request('GET', path),
  post: (path: string, data?: any) => request('POST', path, data),
  put: (path: string, data?: any) => request('PUT', path, data),
  delete: (path: string) => request('DELETE', path),
};
