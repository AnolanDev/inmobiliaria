// src/components/ProjectDetail.tsx
'use client';

import Image from 'next/image';
import { MapComponent } from './Map';
import { getImageUrl } from '@/utils/getImageUrl';

export type GalleryItem = {
  url: string;
  type: string;
};

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
  logoUrl?: string;
  gallery?: GalleryItem[];
};

interface Props {
  project: Project;
}

export default function ProjectDetail({ project }: Props) {
  return (
    <div className="space-y-10 pt-28">
      {/* Header Section */}
      <header className="text-center space-y-4">
        <h1 className="text-5xl font-extrabold text-gray-900">{project.name}</h1>
        <p className="text-lg text-gray-500">
          {project.city}, {project.department}, {project.country}
        </p>
      </header>

      {/* Main Content */}
      <section className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {/* Logo Card */}
        <div className="flex justify-center">
          <div className="bg-white p-6 rounded-2xl shadow-lg">
            {project.logoUrl ? (
              <Image
                src={getImageUrl(project.logoUrl)}
                alt={`Logo de ${project.name}`}
                width={192}
                height={192}
                className="object-contain"
              />
            ) : (
              <div className="w-48 h-48 bg-gray-100 flex items-center justify-center rounded-xl">
                <span className="text-gray-400">No Logo</span>
              </div>
            )}
          </div>
        </div>

        {/* Details Card */}
        <div className="md:col-span-2 bg-white p-6 rounded-2xl shadow-lg space-y-6">
          <div>
            <h2 className="text-2xl font-semibold text-gray-800">Descripción</h2>
            <p className="text-gray-600 text-justify">
              {project.description || 'No hay descripción disponible.'}
            </p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <h3 className="font-medium text-gray-700">Dirección</h3>
              <p className="text-gray-600">{project.address || 'No especificada'}</p>
            </div>
            {project.latitude !== undefined && project.longitude !== undefined && (
              <div>
                <h3 className="font-medium text-gray-700">Coordenadas</h3>
                <p className="text-gray-600">
                  {project.latitude.toFixed(6)}, {project.longitude.toFixed(6)}
                </p>
              </div>
            )}
          </div>
          {/* Map Component if coordinates exist */}
          {project.latitude !== undefined && project.longitude !== undefined && (
            <div className="h-60 rounded-lg overflow-hidden">
              <MapComponent
                projects={[
                  { id: project.id, name: project.name, latitude: project.latitude, longitude: project.longitude },
                ]}
              />
            </div>
          )}
        </section>

      {/* Gallery Section */}
      {project.gallery && project.gallery.length > 0 && (
        <section className="space-y-4">
          <h2 className="text-2xl font-semibold text-gray-800">Galería del Proyecto</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {project.gallery.map((item: GalleryItem, idx: number) => (
              <div key={idx} className="overflow-hidden rounded-xl shadow-lg hover:shadow-2xl transition">
                {item.type.startsWith('image') ? (
                  <Image
                    src={getImageUrl(item.url)}
                    alt={`Galería ${idx + 1}`}
                    width={400}
                    height={250}
                    className="object-cover w-full h-48"
                  />
                ) : (
                  <video
                    src={getImageUrl(item.url)}
                    controls
                    className="object-cover w-full h-48"
                  />
                )}
              </div>
            ))}
          </div>
        </section>
      )}
    </div>
  );
}
