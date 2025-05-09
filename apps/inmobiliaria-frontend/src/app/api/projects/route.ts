// apps/inmobiliaria-frontend/src/app/api/projects/route.ts
import { NextResponse } from 'next/server';

export async function GET() {
  const projects = [
    { id: 1, name: 'Lote Campestre El Bosque', latitude: 4.711, longitude: -74.0721 },
    { id: 2, name: 'Conjunto Mirador de la Sabana', latitude: 4.612, longitude: -74.070 },
  ];

  return NextResponse.json(projects);
}
