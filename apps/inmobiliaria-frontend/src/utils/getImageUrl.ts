export function getImageUrl(url?: string): string {
  if (!url) return '/placeholder.jpg';
  if (url.startsWith('http')) return url;
  // Asegurarnos de que la URL comience con /
  const cleanUrl = url.startsWith('/') ? url : `/${url}`;
  return `http://localhost:3001${cleanUrl}`;
} 