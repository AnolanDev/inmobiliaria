'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import PublicLayout from '@/components/layouts/PublicLayout';

interface Project {
  id: number;
  name: string;
  city: string;
  imageUrl?: string;
  description: string;
}

export default function HomePage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [selected, setSelected] = useState<Project | null>(null);

  useEffect(() => {
    fetch('http://localhost:3001/projects?status=activo')
      .then((res) => res.json())
      .then(setProjects)
      .catch(() => setProjects([]));
  }, []);

  return (
    <PublicLayout>
      <div className="max-w-7xl mx-auto px-4 py-8">
        <h1 className="text-3xl font-bold text-center mb-8 text-[#3c5ca0]">
          Proyectos Activos
        </h1>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {projects.map((project) => (
            <div
              key={project.id}
              onClick={() => setSelected(project)}
              className="bg-white rounded-lg shadow-md cursor-pointer hover:shadow-lg transition overflow-hidden"
            >
              <Image
                src={project.imageUrl || '/placeholder.jpg'}
                alt={project.name}
                width={400}
                height={200}
                className="w-full h-48 object-cover"
              />
              <div className="p-4">
                <h2 className="text-xl font-semibold text-gray-800">{project.name}</h2>
                <p className="text-sm text-gray-500">{project.city}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Modal */}
        {selected && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-lg max-w-2xl w-full relative">
              <button
                onClick={() => setSelected(null)}
                className="absolute top-2 right-2 text-gray-600 hover:text-red-500 text-2xl"
              >
                ×
              </button>
              <Image
                src={selected.imageUrl || '/placeholder.jpg'}
                alt={selected.name}
                width={800}
                height={400}
                className="w-full h-64 object-cover rounded-t-lg"
              />
              <div className="p-6">
                <h2 className="text-2xl font-bold text-[#3c5ca0]">{selected.name}</h2>
                <p className="text-gray-600 mt-2">Ciudad: {selected.city}</p>
                <p className="text-gray-700 mt-4">{selected.description}</p>
              </div>
            </div>
          </div>
        )}
      </div>
    </PublicLayout>
  );
}
