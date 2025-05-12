'use client';

import Link from 'next/link';
import { ArrowLeftIcon } from '@heroicons/react/24/solid';

interface BackButtonProps {
  href: string;
  label?: string;
}

export default function BackButton({ href, label = 'Volver' }: BackButtonProps) {
  return (
    <Link href={href} className="inline-flex items-center text-[#3c5ca0] hover:text-[#5ea546] mb-4">
      <ArrowLeftIcon className="w-5 h-5" />
      <span className="ml-2 font-medium">{label}</span>
    </Link>
  );
}
