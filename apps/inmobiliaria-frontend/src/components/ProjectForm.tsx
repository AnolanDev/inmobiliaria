'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

type Props = {
  onSubmit: (formData: FormData) => Promise<void>;
  initialData?: Partial<ProjectFormData>;
};

type ProjectFormData = {
  name: string;
  description?: string;
  latitude?: string;
  longitude?: string;
  address?: string;
  country: string;
  department: string;
  city: string;
  logoFile?: File;
  galleryFiles?: File[];
};

export default function ProjectForm({ onSubmit, initialData = {} }: Props) {
  const router = useRouter();
  const [form, setForm] = useState<ProjectFormData>({
    name: initialData.name || '',
    description: initialData.description || '',
    latitude: initialData.latitude || '',
    longitude: initialData.longitude || '',
    address: initialData.address || '',
    country: initialData.country || '',
    department: initialData.department || '',
    city: initialData.city || '',
  });
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [galleryFiles, setGalleryFiles] = useState<File[]>([]);
  const [loading, setLoading] = useState(false);

  const handleChange = (field: keyof ProjectFormData, value: string) => {
    setForm({ ...form, [field]: value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const data = new FormData();
    Object.entries(form).forEach(([key, value]) => {
      if (value) data.append(key, value.toString());
    });
    if (logoFile) data.append('logo', logoFile);
    galleryFiles.forEach((file) => data.append('gallery', file));
    setLoading(true);
    await onSubmit(data);
    setLoading(false);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <input className="border rounded px-3 py-2 w-full" placeholder="Nombre" required value={form.name} onChange={e => handleChange('name', e.target.value)} />
      <input className="border rounded px-3 py-2 w-full" placeholder="Descripción" value={form.description} onChange={e => handleChange('description', e.target.value)} />
      <div className="grid grid-cols-2 gap-2">
        <input className="border rounded px-3 py-2" placeholder="Latitud" value={form.latitude} onChange={e => handleChange('latitude', e.target.value)} />
        <input className="border rounded px-3 py-2" placeholder="Longitud" value={form.longitude} onChange={e => handleChange('longitude', e.target.value)} />
      </div>
      <input className="border rounded px-3 py-2 w-full" placeholder="Dirección" value={form.address} onChange={e => handleChange('address', e.target.value)} />
      <input className="border rounded px-3 py-2 w-full" placeholder="País" value={form.country} onChange={e => handleChange('country', e.target.value)} required />
      <input className="border rounded px-3 py-2 w-full" placeholder="Departamento" value={form.department} onChange={e => handleChange('department', e.target.value)} required />
      <input className="border rounded px-3 py-2 w-full" placeholder="Ciudad" value={form.city} onChange={e => handleChange('city', e.target.value)} required />
      <div>
        <label className="block text-sm">Logo:</label>
        <input type="file" accept="image/*" onChange={(e) => setLogoFile(e.target.files?.[0] || null)} />
      </div>
      <div>
        <label className="block text-sm">Galería:</label>
        <input type="file" multiple accept="image/*,video/*" onChange={(e) => setGalleryFiles(Array.from(e.target.files || []))} />
      </div>
      <button type="submit" disabled={loading} className="bg-green-600 text-white px-4 py-2 rounded">
        {loading ? 'Guardando...' : 'Guardar'}
      </button>
    </form>
  );
}
