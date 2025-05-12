// Componente de la pagina inicial (publica)
'use client'

import Image from 'next/image'
import { Project } from '@/app/page'
import { getImageUrl } from '@/utils/getImageUrl'

type Props = {
  project: Project
  onClose: () => void
}

export default function ProjectModal({ project, onClose }: Props) {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg shadow-lg max-w-2xl w-full relative">
        <button
          onClick={onClose}
          className="absolute top-2 right-2 text-gray-600 hover:text-red-500 text-2xl"
        >
          ×
        </button>
        <Image
          src={getImageUrl(project.imageUrl)}
          alt={project.name}
          width={800}
          height={400}
          className="w-full h-64 object-cover rounded-t-lg"
        />
        <div className="p-6">
          <h2 className="text-2xl font-bold text-[#3c5ca0]">
            {project.name}
          </h2>
          <p className="text-gray-600 mt-2">Ciudad: {project.city}</p>
          {project.description && (
            <p className="text-gray-700 mt-4">{project.description}</p>
          )}
        </div>
      </div>
    </div>
  )
}
