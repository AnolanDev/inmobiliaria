// src/app/projects/[id]/page.tsx
'use client';

import { use, useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import BackButton from '@/components/BackButton';
import Gallery from '@/components/Gallery';
import Image from 'next/image';
import { apiClient } from '@/app/lib/apiClient';
import { toast } from 'react-hot-toast';
import { HiOutlineLocationMarker, HiOutlineUser, HiOutlineCheckCircle, HiOutlineXCircle } from 'react-icons/hi';
import { MapComponent } from '@/components/Map';

export type Project = {
  id: number;
  name: string;
  description?: string;
  latitude?: number;
  longitude?: number;
  address?: string;
  country: string;
  department: string;
  city: string;
  status: 'DISPONIBLE' | 'VENDIDO';
  logoUrl?: string;
  imageUrl?: string;
  gallery: { url: string; type: string }[];
};

export default function ProjectDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const router = useRouter();
  const [project, setProject] = useState<Project | null>(null);
  const [loading, setLoading] = useState(true);
  const [deleting, setDeleting] = useState(false);

  const fetchProject = async () => {
    try {
      const response = await apiClient.get<Project>(`/projects/${id}`);
      setProject(response.data);
    } catch (error) {
      console.error('Error al obtener proyecto:', error);
      toast.error('Error al cargar el proyecto');
      router.push('/projects');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProject();
    // eslint-disable-next-line
  }, [id]);

  const handleDelete = async () => {
    if (!confirm('¿Está seguro de que desea eliminar este proyecto?')) return;
    setDeleting(true);
    try {
      await apiClient.delete(`/projects/${id}`);
      toast.success('Proyecto eliminado correctamente');
      router.push('/projects');
    } catch (error) {
      console.error('Error al eliminar proyecto:', error);
      toast.error('Error al eliminar el proyecto');
    } finally {
      setDeleting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="w-10 h-10 border-4 border-green-600 border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  if (!project) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="bg-red-100 text-red-700 px-6 py-4 rounded-md shadow">Proyecto no encontrado</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-tr from-white to-gray-100 p-8 pt-28 font-sans">
      <div className="max-w-4xl mx-auto">
        <div className="flex items-center justify-between mb-8">
          <BackButton href="/projects" />
          <div className="flex space-x-4">
            <button
              onClick={() => router.push(`/projects/${id}/edit`)}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold shadow"
            >
              Editar
            </button>
            <button
              onClick={handleDelete}
              disabled={deleting}
              className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50 font-semibold shadow"
            >
              {deleting ? 'Eliminando...' : 'Eliminar'}
            </button>
          </div>
        </div>

        <div className="bg-white shadow-xl rounded-2xl p-8 mb-10 flex flex-col md:flex-row gap-8 items-center md:items-start">
          {project.logoUrl && (
            <div className="relative w-32 h-32 mb-4 md:mb-0 flex-shrink-0 rounded-xl overflow-hidden shadow-lg border border-gray-100 bg-gray-50">
              <Image
                src={project.logoUrl}
                alt={`${project.name} logo`}
                fill
                sizes="128px"
                className="object-contain"
              />
            </div>
          )}
          <div className="flex-1 space-y-3">
            <h1 className="text-3xl font-extrabold text-slate-800 mb-2 tracking-tight flex items-center gap-2">
              <HiOutlineUser className="text-blue-500 text-2xl" />
              {project.name}
            </h1>
            <span
              className={`inline-block text-xs font-bold px-3 py-1 rounded-full shadow-sm mb-2 ${
                project.status === 'DISPONIBLE'
                  ? 'bg-green-100 text-green-700'
                  : 'bg-red-100 text-red-700'
              }`}
            >
              {project.status === 'DISPONIBLE' ? (
                <span className="inline-flex items-center gap-1"><HiOutlineCheckCircle className="inline text-lg" /> Disponible</span>
              ) : (
                <span className="inline-flex items-center gap-1"><HiOutlineXCircle className="inline text-lg" /> Vendido</span>
              )}
            </span>
            {project.description && (
              <div>
                <h2 className="text-lg font-semibold text-slate-700 mb-1">Descripción</h2>
                <p className="text-gray-600 text-sm leading-relaxed">{project.description}</p>
              </div>
            )}
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
          <div className="bg-white rounded-2xl shadow-lg p-6 space-y-4">
            <h2 className="text-xl font-bold text-slate-700 mb-2 flex items-center gap-2"><HiOutlineLocationMarker className="text-green-500 text-2xl" /> Ubicación</h2>
            <div className="space-y-2 text-gray-700 text-sm">
              <div><span className="font-semibold">País:</span> {project.country}</div>
              <div><span className="font-semibold">Departamento:</span> {project.department}</div>
              <div><span className="font-semibold">Ciudad:</span> {project.city}</div>
              {project.address && <div><span className="font-semibold">Dirección:</span> {project.address}</div>}
            </div>
            {project.latitude && project.longitude && (
              <div className="mt-4">
                <h3 className="font-semibold text-gray-600 mb-1">Coordenadas</h3>
                <div className="flex gap-4 text-xs text-gray-500 mb-2">
                  <span>Latitud: <span className="font-mono text-gray-700">{project.latitude}</span></span>
                  <span>Longitud: <span className="font-mono text-gray-700">{project.longitude}</span></span>
                </div>
                <div className="w-full h-56 rounded-lg overflow-hidden shadow">
                  <MapComponent projects={[{ id: project.id, name: project.name, latitude: project.latitude, longitude: project.longitude }]} />
                </div>
              </div>
            )}
          </div>
          <div className="bg-white rounded-2xl shadow-lg p-6">
            <h2 className="text-xl font-bold text-slate-700 mb-4">Galería</h2>
            <Gallery items={project.gallery} />
          </div>
        </div>
      </div>
    </div>
  );
}
