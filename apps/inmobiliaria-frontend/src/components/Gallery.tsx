'use client';

import { useState } from 'react';
import Image from 'next/image';
import { getImageUrl } from '@/utils/getImageUrl';

export type GalleryItem = { url: string; type: string };

interface GalleryProps {
  items?: GalleryItem[];
}

export default function Gallery({ items = [] }: GalleryProps) {
  const [selected, setSelected] = useState<GalleryItem | null>(null);

  if (!items || items.length === 0) {
    return (
      <div className="text-center py-8 text-gray-500">
        No hay imágenes en la galería
      </div>
    );
  }

  return (
    <>
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        {items.map((item, idx) => (
          <div key={idx} className="cursor-pointer">
            {item.type.startsWith('video') ? (
              <video
                src={getImageUrl(item.url)}
                className="w-full h-32 object-cover rounded"
                muted
                loop
                onClick={() => setSelected(item)}
              />
            ) : (
              <div className="relative w-full h-32">
                <Image
                  src={getImageUrl(item.url)}
                  alt={`gallery-${idx}`}
                  fill
                  sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                  className="object-cover rounded"
                  onClick={() => setSelected(item)}
                  priority={idx === 0}
                  quality={idx === 0 ? 90 : 75}
                  loading={idx === 0 ? 'eager' : 'lazy'}
                  unoptimized={false}
                />
              </div>
            )}
          </div>
        ))}
      </div>

      {selected && (
        <div
          className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
          onClick={() => setSelected(null)}
        >
          <div className="max-h-full max-w-full p-4">
            {selected.type.startsWith('video') ? (
              <video
                src={getImageUrl(selected.url)}
                controls
                autoPlay
                className="max-h-screen max-w-screen"
              />
            ) : (
              <div className="relative w-full h-full">
                <Image
                  src={getImageUrl(selected.url)}
                  alt="Selected"
                  fill
                  sizes="100vw"
                  className="object-contain"
                  priority
                  quality={90}
                  loading="eager"
                  unoptimized={false}
                />
              </div>
            )}
          </div>
        </div>
      )}
    </>
  );
}
