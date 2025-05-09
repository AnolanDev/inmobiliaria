'use client';

import { useAuth } from '@/context/AuthContext';
import { useEffect, useState } from 'react';
import { apiClient } from '@/app/lib/apiClient';
import { useRouter } from 'next/navigation';
import ProjectsMap from '@/components/ProjectsMap';
import BackButton from '@/components/BackButton';

type Project = {
  id: number;
  name: string;
  city: string;
  status: 'DISPONIBLE' | 'VENDIDO';
  imageUrl?: string;
  latitude: number;
  longitude: number;
};

export default function ProjectsPage() {
  const { token } = useAuth();
  const router = useRouter();
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!token) {
      router.push('/login');
      return;
    }

    (async () => {
      try {
        // Solo proyectos DISPONIBLES
        const data: Project[] = await apiClient.get('/projects?status=DISPONIBLE');
        setProjects(data);
      } catch (err) {
        console.error(err);
        setError('No se pudieron cargar los proyectos.');
        setTimeout(() => router.push('/login'), 2000);
      } finally {
        setLoading(false);
      }
    })();
  }, [token, router]);

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
    <div className="bg-gradient-to-b from-white via-blue-50 to-white min-h-screen space-y-12 px-4 py-10 md:px-12 lg:px-20">
      {/* Back / Header */}
      <div className="flex items-center justify-between mb-6">
        <BackButton />
        <div className="text-center flex-1">
          <h1 className="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
            Proyectos <span className="text-green-600">Disponibles</span>
          </h1>
          <p className="mt-2 text-base md:text-lg text-slate-600">
            Explora nuestras ubicaciones activas y sus detalles
          </p>
        </div>
      </div>

      {/* Project Cards */}
      {projects.length === 0 ? (
        <p className="text-center text-gray-500">No hay proyectos registrados aún.</p>
      ) : (
        <div className="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {projects.map((project) => (
            <div
              key={project.id}
              onClick={() => router.push(`/projects/${project.id}`)}
              className="group bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition p-4 flex flex-col"
            >
              <div className="h-32 w-full mb-3 overflow-hidden rounded-lg">
                <img
                  src={project.imageUrl ?? '/placeholder.jpg'}
                  alt={project.name}
                  className="w-full h-full object-cover transform group-hover:scale-105 transition"
                />
              </div>
              <h2 className="text-lg font-semibold text-gray-800 truncate">
                {project.name}
              </h2>
              <p className="text-sm text-gray-500 mt-1 truncate">
                📍 {project.city}
              </p>
              <span
                className={`mt-3 inline-block text-xs font-medium px-2 py-1 rounded-full ${
                  project.status === 'DISPONIBLE'
                    ? 'bg-green-100 text-green-800'
                    : 'bg-gray-100 text-gray-700'
                }`}
              >
                {project.status}
              </span>
            </div>
          ))}
        </div>
      )}

      {/* Map */}
      {projects.length > 0 && (
        <div className="mt-12">
          <h2 className="text-2xl font-semibold text-center text-slate-700 mb-4">
            Mapa de Proyectos
          </h2>
          <ProjectsMap projects={projects} />
        </div>
      )}
    </div>
  );
}
