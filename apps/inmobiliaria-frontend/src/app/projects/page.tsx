'use client';

import { useAuth } from '@/context/AuthContext';
import { useEffect, useState } from 'react';
import { apiClient } from '@/app/lib/apiClient';
import { useRouter } from 'next/navigation';
import BackButton from '@/components/BackButton';
import Image from 'next/image';
import { getImageUrl } from '@/utils/getImageUrl';
import { HiOutlineLocationMarker, HiOutlineCheckCircle, HiOutlineXCircle } from 'react-icons/hi';

type Project = {
  id: number;
  name: string;
  city: string;
  status: 'DISPONIBLE' | 'VENDIDO';
  imageUrl?: string;
  latitude: number;
  longitude: number;
};

function ProjectCard({ project, onClick }: { project: Project; onClick: () => void }) {
  const [imgSrc, setImgSrc] = useState(getImageUrl(project.imageUrl));
  const [showAvatar, setShowAvatar] = useState(false);
  
  // Función para obtener las iniciales
  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(word => word[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  return (
    <div
      onClick={onClick}
      tabIndex={0}
      role="button"
      aria-label={`Ver detalles de ${project.name}`}
      className="group bg-white border border-gray-100 rounded-2xl shadow-lg hover:shadow-2xl transition p-6 flex flex-col cursor-pointer space-y-3 focus:ring-2 focus:ring-blue-400 focus:outline-none outline-none"
    >
      <div className="h-48 w-full mb-3 overflow-hidden rounded-xl relative shadow">
        {!showAvatar ? (
          <Image
            src={imgSrc}
            alt={project.name}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
            className="object-cover transform group-hover:scale-105 transition rounded-xl"
            onError={() => setShowAvatar(true)}
          />
        ) : (
          <div className="w-full h-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center">
            <span className="text-4xl font-bold text-white">
              {getInitials(project.name)}
            </span>
          </div>
        )}
      </div>
      <div className="space-y-2">
        <h2 className="text-xl font-extrabold text-slate-800 truncate group-hover:text-green-700 transition">
          {project.name}
        </h2>
        <div className="flex items-center gap-2 text-sm text-gray-500">
          <HiOutlineLocationMarker className="text-green-500" />
          <span className="truncate">{project.city}</span>
        </div>
        <span
          className={`inline-block text-xs font-bold px-3 py-1 rounded-full shadow-sm ${
            project.status === 'DISPONIBLE'
              ? 'bg-green-100 text-green-700'
              : 'bg-red-100 text-red-700'
          }`}
        >
          {project.status === 'DISPONIBLE' ? (
            <span className="inline-flex items-center gap-1">
              <HiOutlineCheckCircle className="inline text-lg" /> Disponible
            </span>
          ) : (
            <span className="inline-flex items-center gap-1">
              <HiOutlineXCircle className="inline text-lg" /> Vendido
            </span>
          )}
        </span>
      </div>
    </div>
  );
}

export default function ProjectsPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [visibleCount, setVisibleCount] = useState(12);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const fetchProjects = async () => {
      try {
        const response = await apiClient.get<Project[]>('/projects?status=DISPONIBLE');
        setProjects(response.data);
      } catch (err) {
        console.error('Error al cargar proyectos:', err);
        setError('No se pudieron cargar los proyectos.');
        setTimeout(() => router.push('/login'), 2000);
      } finally {
        setLoading(false);
      }
    };

    fetchProjects();
  }, [user, router]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="w-10 h-10 border-4 border-green-600 border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="bg-red-100 text-red-700 px-6 py-4 rounded-md shadow">{error}</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-tr from-white to-gray-100 p-8 pt-28 font-sans">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <BackButton href="/dashboard" />
          <div className="text-center flex-1">
            <h1 className="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
              Proyectos <span className="text-green-600">Disponibles</span>
            </h1>
            <p className="mt-2 text-base md:text-lg text-slate-600">
              Explora nuestras ubicaciones activas y sus detalles
            </p>
          </div>
          <button
            onClick={() => router.push('/projects/create')}
            className="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-400 focus:outline-none transition flex items-center space-x-2 shadow"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fillRule="evenodd"
                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                clipRule="evenodd"
              />
            </svg>
            <span>Nuevo Proyecto</span>
          </button>
        </div>

        {/* Project Cards */}
        {projects.length === 0 ? (
          <div className="bg-white rounded-2xl shadow-lg p-8 text-center">
            <p className="text-gray-500 text-lg">No hay proyectos registrados aún.</p>
          </div>
        ) : (
          <>
            <div className="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
              {projects.slice(0, visibleCount).map((project) => (
                <ProjectCard 
                  key={project.id} 
                  project={project} 
                  onClick={() => router.push(`/projects/${project.id}`)} 
                />
              ))}
            </div>
            {visibleCount < projects.length && (
              <div className="mt-8 text-center">
                <button
                  onClick={() => setVisibleCount((prev) => prev + 12)}
                  className="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition font-semibold shadow"
                >
                  Cargar más
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}

apiClient.interceptors.request.use(
  (config: AxiosRequestConfig) => {
    const token =
      typeof window !== 'undefined'
        ? localStorage.getItem('token') ?? sessionStorage.getItem('token')
        : null;
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error: AxiosError) => {
    return Promise.reject(error);
  }
);

apiClient.interceptors.response.use(
  response => response,
  async error => {
    const originalRequest = error.config;
    if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      // Intenta renovar el access token
      const refreshToken = localStorage.getItem('refresh_token');
      if (refreshToken) {
        try {
          const res = await apiClient.post('/auth/refresh', { refresh_token: refreshToken });
          const { access_token } = res.data;
          localStorage.setItem('access_token', access_token);
          // Actualiza el header y repite la petición original
          apiClient.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
          originalRequest.headers['Authorization'] = `Bearer ${access_token}`;
          return apiClient(originalRequest);
        } catch (refreshError) {
          // Si falla el refresh, hacer logout
          localStorage.removeItem('access_token');
          localStorage.removeItem('refresh_token');
          window.location.href = '/login';
        }
      }
    }
    return Promise.reject(error);
  }
);
