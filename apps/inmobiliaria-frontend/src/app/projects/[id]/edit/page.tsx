'use client';

import { use, useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { apiClient } from '@/app/lib/apiClient';
import { toast } from 'react-hot-toast';
import Image from 'next/image';
import BackButton from '@/components/BackButton';
import { useAuth } from '@/context/AuthContext';
import { getImageUrl } from '@/utils/getImageUrl';
import { HiOutlineLocationMarker, HiOutlineDocumentText, HiOutlineHome, HiOutlineMap } from 'react-icons/hi';
import { MapComponent } from '@/components/Map';

type Project = {
  id: number;
  name: string;
  description: string;
  city: string;
  status: 'DISPONIBLE' | 'VENDIDO';
  imageUrl?: string;
  logoUrl?: string;
  galleryUrls?: { url: string }[];
  latitude: number;
  longitude: number;
  logo?: File;
  image?: File;
  galleryFiles?: File[];
  address?: string;
  country: string;
  department: string;
};

type FormData = {
  name: string;
  description: string;
  city: string;
  status: 'DISPONIBLE' | 'VENDIDO';
  latitude: string;
  longitude: string;
  country: string;
  department: string;
  address?: string;
};

export default function EditProjectPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const { user } = useAuth();
  const router = useRouter();
  const [project, setProject] = useState<Project | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState<FormData>({
    name: '',
    description: '',
    city: '',
    status: 'DISPONIBLE',
    latitude: '',
    longitude: '',
    country: '',
    department: '',
    address: '',
  });

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }
    fetchProject();
  }, [user]);

  const fetchProject = async () => {
    try {
      const response = await apiClient.get<Project>(`/projects/${id}`);
      setProject(response.data);
      setFormData({
        name: response.data.name,
        description: response.data.description || '',
        city: response.data.city,
        status: response.data.status,
        latitude: response.data.latitude?.toString() || '',
        longitude: response.data.longitude?.toString() || '',
        country: response.data.country,
        department: response.data.department,
        address: response.data.address || '',
      });
    } catch (error) {
      console.error('Error al cargar el proyecto:', error);
      toast.error('Error al cargar el proyecto');
    } finally {
      setIsLoading(false);
    }
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) {
      toast.error('Debes iniciar sesión para actualizar el proyecto');
      router.push('/login');
      return;
    }

    if (!project) {
      toast.error('No hay datos del proyecto para actualizar');
      return;
    }

    setSaving(true);
    try {
      const projectData = {
        ...formData,
        latitude: formData.latitude ? parseFloat(formData.latitude) : undefined,
        longitude: formData.longitude ? parseFloat(formData.longitude) : undefined,
      };

      const formDataToSend = new FormData();
      
      // Agregar campos del formulario
      formDataToSend.append('name', projectData.name);
      formDataToSend.append('description', projectData.description);
      formDataToSend.append('city', projectData.city);
      formDataToSend.append('status', projectData.status);
      formDataToSend.append('latitude', projectData.latitude?.toString() || '');
      formDataToSend.append('longitude', projectData.longitude?.toString() || '');
      formDataToSend.append('country', projectData.country);
      formDataToSend.append('department', projectData.department);
      if (projectData.address) {
        formDataToSend.append('address', projectData.address);
      }

      // Agregar archivos si existen
      if (project.logo instanceof File) {
        formDataToSend.append('logo', project.logo);
      }
      if (project.image instanceof File) {
        formDataToSend.append('image', project.image);
      }
      if (Array.isArray(project.galleryFiles)) {
        project.galleryFiles.forEach((file: File) => {
          formDataToSend.append('gallery', file);
        });
      }

      await apiClient.put(`/projects/${id}`, formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      toast.success('Proyecto actualizado exitosamente');
      router.push(`/projects/${id}`);
    } catch (error) {
      console.error('Error al actualizar el proyecto:', error);
      toast.error('Error al actualizar el proyecto');
    } finally {
      setSaving(false);
    }
  };

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-tr from-white to-gray-100">
        <div className="w-12 h-12 border-4 border-green-600 border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  if (!project) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-tr from-white to-gray-100">
        <div className="bg-red-100 text-red-700 px-8 py-6 rounded-xl shadow-lg text-center">
          <p className="text-lg font-semibold">Proyecto no encontrado</p>
          <button
            onClick={() => router.push('/projects')}
            className="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
          >
            Volver a Proyectos
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-tr from-white to-gray-100 p-8 pt-28 font-sans">
      <div className="max-w-5xl mx-auto">
        {/* Header */}
        <div className="flex items-center justify-between mb-8">
          <BackButton href={`/projects/${id}`} />
          <div className="text-center flex-1">
            <h1 className="text-4xl font-extrabold text-slate-800 tracking-tight">
              Editar <span className="text-green-600">Proyecto</span>
            </h1>
            <p className="mt-2 text-gray-600">
              Actualiza la información del proyecto inmobiliario
            </p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-8">
          {/* Información General */}
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <div className="flex items-center gap-3 mb-6">
              <HiOutlineHome className="text-2xl text-green-600" />
              <h2 className="text-xl font-bold text-slate-800">Información General</h2>
            </div>
            <div className="space-y-6">
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">
                  Nombre del Proyecto
                </label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  value={formData.name}
                  onChange={handleChange}
                  required
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  placeholder="Ingrese el nombre del proyecto"
                />
              </div>

              <div>
                <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">
                  Descripción
                </label>
                <textarea
                  id="description"
                  name="description"
                  value={formData.description}
                  onChange={handleChange}
                  rows={4}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  placeholder="Describa los detalles del proyecto"
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label htmlFor="city" className="block text-sm font-medium text-gray-700 mb-1">
                    Ciudad
                  </label>
                  <input
                    type="text"
                    id="city"
                    name="city"
                    value={formData.city}
                    onChange={handleChange}
                    required
                    className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                    placeholder="Ciudad del proyecto"
                  />
                </div>

                <div>
                  <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1">
                    Estado
                  </label>
                  <select
                    id="status"
                    name="status"
                    value={formData.status}
                    onChange={handleChange}
                    className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  >
                    <option value="DISPONIBLE">Disponible</option>
                    <option value="VENDIDO">Vendido</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {/* Ubicación */}
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <div className="flex items-center gap-3 mb-6">
              <HiOutlineLocationMarker className="text-2xl text-green-600" />
              <h2 className="text-xl font-bold text-slate-800">Ubicación</h2>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label htmlFor="country" className="block text-sm font-medium text-gray-700 mb-1">
                  País
                </label>
                <input
                  type="text"
                  id="country"
                  name="country"
                  value={formData.country}
                  onChange={handleChange}
                  required
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  placeholder="País del proyecto"
                />
              </div>

              <div>
                <label htmlFor="department" className="block text-sm font-medium text-gray-700 mb-1">
                  Departamento
                </label>
                <input
                  type="text"
                  id="department"
                  name="department"
                  value={formData.department}
                  onChange={handleChange}
                  required
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  placeholder="Departamento del proyecto"
                />
              </div>

              <div>
                <label htmlFor="address" className="block text-sm font-medium text-gray-700 mb-1">
                  Dirección
                </label>
                <input
                  type="text"
                  id="address"
                  name="address"
                  value={formData.address}
                  onChange={handleChange}
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                  placeholder="Dirección exacta del proyecto"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label htmlFor="latitude" className="block text-sm font-medium text-gray-700 mb-1">
                    Latitud
                  </label>
                  <input
                    type="number"
                    id="latitude"
                    name="latitude"
                    value={formData.latitude}
                    onChange={handleChange}
                    step="any"
                    className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                    placeholder="0.000000"
                  />
                </div>

                <div>
                  <label htmlFor="longitude" className="block text-sm font-medium text-gray-700 mb-1">
                    Longitud
                  </label>
                  <input
                    type="number"
                    id="longitude"
                    name="longitude"
                    value={formData.longitude}
                    onChange={handleChange}
                    step="any"
                    className="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                    placeholder="0.000000"
                  />
                </div>
              </div>
            </div>

            {(formData.latitude || formData.longitude) && (
              <div className="mt-8">
                <div className="flex items-center gap-2 mb-4">
                  <HiOutlineMap className="text-xl text-green-600" />
                  <h3 className="font-semibold text-gray-700">Vista Previa del Mapa</h3>
                </div>
                <div className="w-full h-80 rounded-xl overflow-hidden shadow-lg border border-gray-200">
                  <MapComponent
                    projects={[
                      {
                        id: parseInt(id),
                        name: formData.name,
                        latitude: parseFloat(formData.latitude) || 0,
                        longitude: parseFloat(formData.longitude) || 0,
                      },
                    ]}
                  />
                </div>
              </div>
            )}
          </div>

          {/* Botones de Acción */}
          <div className="flex justify-end space-x-4">
            <button
              type="button"
              onClick={() => router.push(`/projects/${id}`)}
              className="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-400 focus:outline-none transition font-semibold shadow"
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={saving}
              className="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-400 focus:outline-none transition font-semibold shadow disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              {saving ? (
                <>
                  <div className="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin" />
                  <span>Guardando...</span>
                </>
              ) : (
                <>
                  <HiOutlineDocumentText className="text-xl" />
                  <span>Guardar Cambios</span>
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
} 