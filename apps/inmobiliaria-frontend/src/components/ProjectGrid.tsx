'use client'

import Image from 'next/image'
import Link from 'next/link'
import { getImageUrl } from '@/utils/getImageUrl'

interface Project {
  id: number;
  name: string;
  city: string;
  department: string;
  country: string;
  imageUrl?: string;
}

interface Props {
  projects: Project[];
}

export default function ProjectGrid({ projects }: Props) {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      {projects.map((project, index) => (
        <Link
          key={project.id}
          href={`/projects/${project.id}`}
          className="block rounded-lg overflow-hidden shadow hover:shadow-lg transition"
        >
          <div className="relative w-full h-48 bg-gray-100">
            <Image
              src={getImageUrl(project.imageUrl)}
              alt={project.name}
              fill
              sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
              className="object-cover"
              priority={index === 0}
              quality={index === 0 ? 90 : 75}
              loading={index === 0 ? 'eager' : 'lazy'}
              unoptimized={false}
            />
          </div>
          <div className="p-4">
            <h2 className="text-xl font-semibold">{project.name}</h2>
            <p className="text-sm text-gray-600">
              {project.city}, {project.department}, {project.country}
            </p>
          </div>
        </Link>
      ))}
    </div>
  )
}
