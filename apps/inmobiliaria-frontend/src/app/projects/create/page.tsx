'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { apiClient } from '@/app/lib/apiClient';
import { toast } from 'react-hot-toast';

export default function CreateProjectPage() {
  const router = useRouter();
  const [form, setForm] = useState({
    name: '',
    description: '',
    latitude: '',
    longitude: '',
    address: '',
    country: '',
    department: '',
    city: '',
    status: 'DISPONIBLE',
  });

  const [logo, setLogo] = useState<File | null>(null);
  const [image, setImage] = useState<File | null>(null);
  const [gallery, setGallery] = useState<File[]>([]);
  const [loading, setLoading] = useState(false);

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);

    const data = new FormData();
    Object.entries(form).forEach(([key, value]) => {
      if (value) data.append(key, value.toString());
    });

    if (logo) data.append('logo', logo);
    if (image) data.append('image', image);
    gallery.forEach((file) => data.append('gallery', file));

    try {
      const res = await apiClient.post('/projects/create', data);
      toast.success('✅ Proyecto creado correctamente');
      router.push(`/projects/${res.data.id}`);
    } catch {
      toast.error('❌ Error al crear el proyecto');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-3xl mx-auto px-4 py-10">
      <h1 className="text-3xl font-bold mb-6 text-center text-slate-800">Crear Nuevo Proyecto</h1>

      <form onSubmit={handleSubmit} className="space-y-6 bg-white shadow-lg rounded-xl p-6">
        <div>
          <label htmlFor="name" className="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
          <input
            id="name"
            name="name"
            value={form.name}
            onChange={handleChange}
            required
            className="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>

        <div>
          <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea
            id="description"
            name="description"
            value={form.description}
            onChange={handleChange}
            className="w-full px-4 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <label htmlFor="latitude" className="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
            <input
              id="latitude"
              name="latitude"
              value={form.latitude}
              onChange={handleChange}
              type="number"
              className="w-full px-4 py-3 border rounded-md"
            />
          </div>
          <div>
            <label htmlFor="longitude" className="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
            <input
              id="longitude"
              name="longitude"
              value={form.longitude}
              onChange={handleChange}
              type="number"
              className="w-full px-4 py-3 border rounded-md"
            />
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label htmlFor="country" className="block text-sm font-medium text-gray-700 mb-1">País</label>
            <input id="country" name="country" value={form.country} onChange={handleChange} className="w-full px-4 py-3 border rounded-md" />
          </div>
          <div>
            <label htmlFor="department" className="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
            <input id="department" name="department" value={form.department} onChange={handleChange} className="w-full px-4 py-3 border rounded-md" />
          </div>
          <div>
            <label htmlFor="city" className="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
            <input id="city" name="city" value={form.city} onChange={handleChange} className="w-full px-4 py-3 border rounded-md" />
          </div>
        </div>

        <div>
          <label htmlFor="address" className="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
          <input id="address" name="address" value={form.address} onChange={handleChange} className="w-full px-4 py-3 border rounded-md" />
        </div>

        <div>
          <label htmlFor="status" className="block text-sm font-medium text-gray-700 mb-1">Estado del Proyecto</label>
          <select
            id="status"
            name="status"
            value={form.status}
            onChange={handleChange}
            className="w-full px-4 py-3 border rounded-md"
          >
            <option value="DISPONIBLE">DISPONIBLE</option>
            <option value="VENDIDO">VENDIDO</option>
          </select>
        </div>

        <div>
          <label className="block mb-1 font-medium">Logo del Proyecto</label>
          <input type="file" accept="image/*" onChange={(e) => setLogo(e.target.files?.[0] || null)} />
        </div>

        <div>
          <label className="block mb-1 font-medium">Imagen destacada</label>
          <input type="file" accept="image/*" onChange={(e) => setImage(e.target.files?.[0] || null)} />
        </div>

        <div>
          <label className="block mb-1 font-medium">Galería (imágenes o videos)</label>
          <input
            type="file"
            accept="image/*,video/*"
            multiple
            onChange={(e) => setGallery(Array.from(e.target.files || []))}
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition"
        >
          {loading ? 'Guardando...' : 'Crear Proyecto'}
        </button>
      </form>
    </div>
  );
}
