export type Project = {
  id: number;
  name: string;
  description?: string;
  latitude?: number;
  longitude?: number;
  address?: string;
  country: string;
  department: string;
  city: string;
  logoUrl?: string;
  gallery: { url: string; type: 'image' | 'video' }[];
};
