'use client';

import Map, { Marker, Popup } from 'react-map-gl';
import { useState, useRef, useEffect } from 'react';
import Supercluster from 'supercluster';
import Image from 'next/image';

type Project = {
  id: number;
  name: string;
  latitude: number;
  longitude: number;
  imageUrl?: string;
};

export default function ProjectsMap({ projects }: { projects: Project[] }) {
  const [selectedProject, setSelectedProject] = useState<Project | null>(null);
  const mapRef = useRef<any>(null);
  const superclusterRef = useRef<any>(null);

  // Calcular el centro promedio de los proyectos
  const avgLatitude = projects.length
    ? projects.reduce((sum, p) => sum + p.latitude, 0) / projects.length
    : 4.7110;
  const avgLongitude = projects.length
    ? projects.reduce((sum, p) => sum + p.longitude, 0) / projects.length
    : -74.0721;

  const [viewport, setViewport] = useState({
    latitude: avgLatitude,
    longitude: avgLongitude,
    zoom: 5,
  });

  const points = projects.map((project) => ({
    type: 'Feature' as const,
    properties: {
      cluster: false,
      projectId: project.id,
      projectName: project.name,
      projectImageUrl: project.imageUrl || '/placeholder.jpg',
    },
    geometry: {
      type: 'Point' as const,
      coordinates: [project.longitude, project.latitude],
    },
  }));

  useEffect(() => {
    if (points.length > 0) {
      superclusterRef.current = new Supercluster({
        radius: 75,
        maxZoom: 20,
      }).load(points);
    }
  }, [points]);

  const clusters = superclusterRef.current
    ? superclusterRef.current.getClusters([-180, -85, 180, 85], Math.round(viewport.zoom))
    : [];

  const getImageUrl = (url: string) => {
    if (url.startsWith('http')) return url
    // Asegurarnos de que la URL comience con /
    const cleanUrl = url.startsWith('/') ? url : `/${url}`
    return `http://localhost:3001${cleanUrl}`
  };

  return (
    <div className="w-full h-[500px] rounded-lg overflow-hidden">
      <Map
        ref={mapRef}
        mapboxAccessToken={process.env.NEXT_PUBLIC_MAPBOX_ACCESS_TOKEN}
        initialViewState={viewport}
        mapStyle="mapbox://styles/mapbox/streets-v11"
        style={{ width: '100%', height: '100%' }}
        onMove={(evt) => {
          setViewport({
            latitude: evt.viewState.latitude,
            longitude: evt.viewState.longitude,
            zoom: evt.viewState.zoom,
          });
        }}
      >
        {clusters.map((cluster: any) => {
          const [longitude, latitude] = cluster.geometry.coordinates;
          const { cluster: isCluster, point_count } = cluster.properties;

          if (isCluster) {
            return (
              <Marker key={`cluster-${cluster.id}`} longitude={longitude} latitude={latitude}>
                <div
                  className="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center cursor-pointer shadow"
                  onClick={() => {
                    const expansionZoom = Math.min(
                      superclusterRef.current.getClusterExpansionZoom(cluster.id),
                      20
                    );
                    mapRef.current?.flyTo({
                      center: [longitude, latitude],
                      zoom: expansionZoom,
                      speed: 1,
                    });
                  }}
                >
                  {point_count}
                </div>
              </Marker>
            );
          }

          return (
            <Marker
              key={`project-${cluster.properties.projectId}`}
              longitude={longitude}
              latitude={latitude}
            >
              <div
                className="w-4 h-4 bg-red-600 rounded-full border-2 border-white cursor-pointer"
                onClick={() => {
                  setSelectedProject({
                    id: cluster.properties.projectId,
                    name: cluster.properties.projectName,
                    latitude,
                    longitude,
                    imageUrl: cluster.properties.projectImageUrl,
                  });
                }}
              />
            </Marker>
          );
        })}

        {selectedProject && (
          <Popup
            longitude={selectedProject.longitude}
            latitude={selectedProject.latitude}
            closeOnClick={false}
            onClose={() => setSelectedProject(null)}
            offset={25}
          >
            <div className="p-3 w-64 bg-white rounded-lg shadow space-y-2">
              <div className="relative w-full h-28">
                <Image
                  src={getImageUrl(selectedProject.imageUrl || '/placeholder.jpg')}
                  alt={selectedProject.name}
                  fill
                  sizes="256px"
                  className="object-cover rounded"
                  unoptimized
                />
              </div>
              <h3 className="text-lg font-semibold text-gray-800">{selectedProject.name}</h3>
              <button
                className="w-full bg-green-600 hover:bg-green-700 text-white text-sm py-2 rounded-md transition"
                onClick={() => window.location.href = `/projects/${selectedProject.id}`}
              >
                Ver detalles
              </button>
            </div>
          </Popup>
        )}
      </Map>
    </div>
  );
}
