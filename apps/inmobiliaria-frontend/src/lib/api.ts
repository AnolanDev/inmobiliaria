// apps/inmobiliaria-frontend/src/lib/api.ts

export async function apiFetch(path: string, options: RequestInit = {}) {
    const token = typeof window !== 'undefined' ? localStorage.getItem('token') : null;
  
    const headers = {
      ...(options.headers || {}),
      Authorization: token ? `Bearer ${token}` : '',
    };
  
    const res = await fetch(`http://localhost:3001${path}`, {
      ...options,
      headers,
    });
  
    if (!res.ok) {
      throw new Error('Error en la API');
    }
  
    return res.json();
  }
  