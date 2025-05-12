'use client';

import { useState } from 'react';
import Map, { Marker, Popup, type ViewState } from 'react-map-gl';

export type Project = {
  id: number;
  name: string;
  latitude: number;
  longitude: number;
};

interface Props {
  projects: Project[];
}

export function MapComponent({ projects }: Props) {
  const initialState: ViewState = {
    latitude: projects[0]?.latitude ?? 0,
    longitude: projects[0]?.longitude ?? 0,
    zoom: 10,
    bearing: 0,
    pitch: 0,
    padding: { top: 0, bottom: 0, left: 0, right: 0 },
  };

  const [viewState, setViewState] = useState<ViewState>(initialState);
  const [selectedProject, setSelectedProject] = useState<Project | null>(null);

  return (
    <div className="w-full h-[500px] rounded-lg overflow-hidden shadow-lg">
      <Map
        initialViewState={viewState}
        onMove={(evt) => setViewState(evt.viewState)}
        mapboxAccessToken={process.env.NEXT_PUBLIC_MAPBOX_ACCESS_TOKEN}
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
