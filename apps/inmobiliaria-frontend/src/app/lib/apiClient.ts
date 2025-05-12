// src/app/lib/apiClient.ts
'use client';

import { toast } from 'react-hot-toast';
import axios from 'axios';

const API_URL = process.env.NEXT_PUBLIC_BACKEND_URL || 'http://localhost:3001';

const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

// Interceptor para agregar el access_token a todas las peticiones
apiClient.interceptors.request.use(
  (config) => {
    const token =
      typeof window !== 'undefined'
        ? localStorage.getItem('access_token') ?? sessionStorage.getItem('access_token')
        : null;
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor para manejar errores
apiClient.interceptors.response.use(
  response => response,
  async error => {
    if (error.response?.status === 401) {
      // Limpiar tokens y redirigir al login
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
      sessionStorage.removeItem('access_token');
      sessionStorage.removeItem('refresh_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const request = async <T>(config: AxiosRequestConfig): Promise<T> => {
  try {
    const response = await apiClient(config);
    return response as T;
  } catch (error) {
    console.error('Error en la petición:', error);
    throw error;
  }
};

export const get = <T>(url: string, config: AxiosRequestConfig = {}): Promise<T> => {
  return request<T>({ ...config, method: 'get', url });
};

export const post = <T>(url: string, data = {}, config: AxiosRequestConfig = {}): Promise<T> => {
  return request<T>({ ...config, method: 'post', url, data });
};

export const put = <T>(url: string, data = {}, config: AxiosRequestConfig = {}): Promise<T> => {
  // Asegurarse de que el token se envía en el header
  const token = localStorage.getItem('token');
  const headers = {
    ...config.headers,
    Authorization: token ? `Bearer ${token}` : '',
  };
  
  return request<T>({
    ...config,
    method: 'put',
    url,
    data,
    headers,
  });
};

export const del = <T>(url: string, config: AxiosRequestConfig = {}): Promise<T> => {
  return request<T>({ ...config, method: 'delete', url });
};

export { apiClient };
