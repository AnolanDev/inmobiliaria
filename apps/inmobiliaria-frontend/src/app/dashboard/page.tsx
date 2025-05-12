// src/app/dashboard/page.tsx
import Image from 'next/image'
import { HiOutlineClipboardList, HiOutlineCheckCircle, HiOutlineXCircle, HiOutlineEye } from 'react-icons/hi'
import { getImageUrl } from '@/utils/getImageUrl'

export interface Project {
  id: number
  name: string
  city: string
  department: string
  country: string
  status: 'DISPONIBLE' | 'VENDIDO' | string
  imageUrl?: string
}

export default async function DashboardPage() {
  // Fetch projects desde el backend
  const res = await fetch(
    `${process.env.NEXT_PUBLIC_BACKEND_URL}/projects`,
    { cache: 'no-store' }
  )
  if (!res.ok) {
    throw new Error('No se pudieron cargar los proyectos para el dashboard')
  }
  const projects: Project[] = await res.json()

  // Cálculos de resumen
  const total = projects.length
  const disponibles = projects.filter(p => p.status === 'DISPONIBLE').length
  const vendidos = projects.filter(p => p.status === 'VENDIDO').length

  return (
    <section className="max-w-7xl mx-auto px-4 py-8 space-y-10 mt-24 font-sans">
      <h1 className="text-4xl font-extrabold text-center text-[#3c5ca0] mb-8 tracking-tight">
        Dashboard de Proyectos
      </h1>

      {/* Cards de resumen */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div className="bg-gradient-to-br from-blue-100 to-blue-50 p-6 rounded-2xl shadow-lg text-center flex flex-col items-center transition-transform hover:-translate-y-1 hover:shadow-2xl duration-200">
          <HiOutlineClipboardList className="text-blue-500 text-4xl mb-2" />
          <h2 className="text-lg font-semibold text-gray-700">Total Proyectos</h2>
          <p className="mt-1 text-4xl font-extrabold text-gray-900">{total}</p>
        </div>
        <div className="bg-gradient-to-br from-green-100 to-green-50 p-6 rounded-2xl shadow-lg text-center flex flex-col items-center transition-transform hover:-translate-y-1 hover:shadow-2xl duration-200">
          <HiOutlineCheckCircle className="text-green-500 text-4xl mb-2" />
          <h2 className="text-lg font-semibold text-green-700">Disponibles</h2>
          <p className="mt-1 text-4xl font-extrabold text-green-700">{disponibles}</p>
        </div>
        <div className="bg-gradient-to-br from-red-100 to-red-50 p-6 rounded-2xl shadow-lg text-center flex flex-col items-center transition-transform hover:-translate-y-1 hover:shadow-2xl duration-200">
          <HiOutlineXCircle className="text-red-500 text-4xl mb-2" />
          <h2 className="text-lg font-semibold text-red-700">Vendidos</h2>
          <p className="mt-1 text-4xl font-extrabold text-red-700">{vendidos}</p>
        </div>
      </div>

      {/* Tabla de proyectos */}
      <div className="overflow-x-auto bg-white rounded-2xl shadow-lg">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gradient-to-r from-blue-50 to-green-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Imagen</th>
              <th className="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nombre</th>
              <th className="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ubicación</th>
              <th className="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Estado</th>
              <th className="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-100">
            {projects.map(project => (
              <tr key={project.id} className="hover:bg-blue-50 transition group">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="relative w-20 h-12 rounded-lg overflow-hidden shadow group-hover:shadow-md transition">
                    <Image
                      src={getImageUrl(project.imageUrl)}
                      alt={project.name}
                      fill
                      sizes="80px"
                      className="rounded-md object-cover"
                      unoptimized
                    />
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-gray-900 font-semibold text-base">
                  {project.name}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-gray-600 text-sm">
                  {project.city}, {project.department}, {project.country}
                </td>
                <td className="px-6 py-4 whitespace-nowrap font-bold">
                  <span className={
                    project.status === 'DISPONIBLE'
                      ? 'bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs shadow-sm'
                      : 'bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs shadow-sm'
                  }>
                    {project.status}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-center">
                  <a
                    href={`/projects/${project.id}`}
                    className="inline-flex items-center justify-center px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg shadow transition font-medium text-sm"
                    title="Ver detalles"
                  >
                    <HiOutlineEye className="text-lg mr-1" /> Ver
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  )
}
