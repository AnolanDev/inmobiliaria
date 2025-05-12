// next.config.ts
import type { NextConfig } from 'next'
import type { Configuration } from 'webpack'
import path from 'path'

const nextConfig: NextConfig = {
  reactStrictMode: true,

  images: {
    domains: ['localhost'],
    remotePatterns: [
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '3001',
        pathname: '/uploads/**',
      },
    ],
    unoptimized: true,
  },

  webpack(config: Configuration) {
    config.resolve = config.resolve || {}
    config.resolve.alias = {
      ...(config.resolve.alias || {}),
      '@': path.resolve(__dirname, 'src'),
    }
    return config
  },
}

export default nextConfig
