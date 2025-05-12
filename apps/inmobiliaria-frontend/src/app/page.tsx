// Pagina inicial

import PublicLayout from '@/components/layouts/PublicLayout'
import ProjectGrid from '@/components/ProjectGrid'

export interface Project {
  id: number
  name: string
  city: string
  department: string
  country: string
  imageUrl?: string
  description?: string
}

export default async function HomePage() {
  try {
    const res = await fetch(
      `${process.env.NEXT_PUBLIC_BACKEND_URL}/projects?status=DISPONIBLE`,
      { 
        cache: 'no-store',
        headers: {
          'Content-Type': 'application/json',
        },
      }
    )
    
    if (!res.ok) {
      const error = await res.text()
      console.error('Error al cargar proyectos:', error)
      throw new Error(`Error al cargar proyectos: ${res.status} ${res.statusText}`)
    }
    
    const projects: Project[] = await res.json()

    return (
      <PublicLayout>
        <section className="max-w-7xl mx-auto px-4 py-8">
          <h1 className="text-3xl font-bold text-center mb-8 text-[#3c5ca0]">
            Proyectos Activos
          </h1>
          <ProjectGrid projects={projects} />
        </section>
      </PublicLayout>
    )
  } catch (error) {
    console.error('Error en HomePage:', error)
    return (
      <PublicLayout>
        <section className="max-w-7xl mx-auto px-4 py-8">
          <div className="text-center text-red-600">
            <h2 className="text-2xl font-bold mb-4">Error al cargar los proyectos</h2>
            <p>Por favor, intente nuevamente más tarde.</p>
          </div>
        </section>
      </PublicLayout>
    )
  }
}

