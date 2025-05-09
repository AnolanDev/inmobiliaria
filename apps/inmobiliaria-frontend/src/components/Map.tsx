'use client';

import { useState } from 'react';
import Map, { Marker, Popup } from 'react-map-gl';

export type Project = {
  id: number;
  name: string;
  latitude: number;
  longitude: number;
};

export function MapComponent({ projects }: { projects: Project[] }) {
  const [viewState, setViewState] = useState<ViewState>({
    latitude: projects[0]?.latitude || 0,
    longitude: projects[0]?.longitude || 0,
    zoom: 10,
  });

  const [selectedProject, setSelectedProject] = useState<Project | null>(null);

  const handleMove = (evt: { viewState: ViewState }) => {
    setViewState(evt.viewState);
  };

  return (
    <div className="w-full h-[500px] rounded-lg overflow-hidden shadow-lg">
      <Map
        {...viewState}
        onMove={handleMove}
        mapboxAccessToken={process.env.NEXT_PUBLIC_MAPBOX_TOKEN}
        mapStyle="mapbox://styles/mapbox/streets-v11"
        style={{ width: '100%', height: '100%' }}
      >
        {projects.map((project) => (
          <Marker
            key={project.id}
            longitude={project.longitude}
            latitude={project.latitude}
            anchor="bottom"
            onClick={(e) => {
              e.originalEvent.stopPropagation();
              setSelectedProject(project);
            }}
          >
            <div className="bg-green-600 p-2 rounded-full cursor-pointer hover:bg-green-700 transition">
              📍
            </div>
          </Marker>
        ))}

        {selectedProject && (
          <Popup
            longitude={selectedProject.longitude}
            latitude={selectedProject.latitude}
            onClose={() => setSelectedProject(null)}
            closeOnClick={false}
            anchor="top"
          >
            <div className="text-center">
              <h3 className="font-semibold">{selectedProject.name}</h3>
            </div>
          </Popup>
        )}
      </Map>
    </div>
  );
}
