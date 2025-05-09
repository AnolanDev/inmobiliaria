import { notFound } from 'next/navigation';
import BackButton from '@/components/BackButton';

type GalleryItem = {
  url: string;
  type: string;
};

type Project = {
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
  gallery: GalleryItem[];
};

async function getProject(id: string): Promise<Project | null> {
  try {
    const res = await fetch(`http://localhost:3001/projects/${id}`, {
      cache: 'no-store',
    });
    if (!res.ok) return null;
    return await res.json();
  } catch (error) {
    console.error('❌ Error al obtener el proyecto:', error);
    return null;
  }
}

export default async function ProjectDetailPage({
  params,
}: {
  params: { id: string };
}) {
  const project = await getProject(params.id);
  if (!project) return notFound();

  return (
    <div className="max-w-6xl mx-auto px-4 py-10 space-y-12">
      <BackButton />

      <header className="text-center space-y-2">
        <h1 className="text-4xl font-bold text-slate-800">{project.name}</h1>
        <p className="text-slate-500 text-sm">
          Proyecto ubicado en {project.city}, {project.department}, {project.country}
        </p>
      </header>

      <section className="grid md:grid-cols-2 gap-10 items-start">
        {/* Imagen principal */}
        <div className="flex justify-center">
          <img
            src={project.logoUrl ? `http://localhost:3001${project.logoUrl}` : '/placeholder.jpg'}
            alt={`Logo de ${project.name}`}
            className="w-64 h-auto object-contain rounded-xl shadow-lg border bg-white"
          />
        </div>

        {/* Detalles */}
        <div className="space-y-4 text-gray-700 text-base">
          <div>
            <h2 className="font-semibold text-slate-700">Descripción</h2>
            <p className="text-justify">{project.description || 'Sin descripción disponible.'}</p>
          </div>
          <p>
            <span className="font-medium">Dirección:</span>{' '}
            {project.address || 'No especificada'}
          </p>
          {(project.latitude || project.longitude) && (
            <p>
              <span className="font-medium">Coordenadas:</span>{' '}
              {project.latitude ?? '-'}, {project.longitude ?? '-'}
            </p>
          )}
        </div>
      </section>

      {/* Galería */}
      {project.gallery.length > 0 && (
        <section className="space-y-6">
          <h2 className="text-2xl font-semibold text-slate-800">Galería del Proyecto</h2>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
            {project.gallery.map((item, index) => (
              <div key={index} className="rounded-lg overflow-hidden shadow">
                {item.type.startsWith('image') ? (
                  <img
                    src={`http://localhost:3001${item.url}`}
                    alt={`Imagen ${index + 1}`}
                    className="w-full h-40 object-cover"
                  />
                ) : (
                  <video
                    src={`http://localhost:3001${item.url}`}
                    controls
                    className="w-full h-40 object-cover"
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
