import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { NestExpressApplication } from '@nestjs/platform-express';
import { join } from 'path';
import { ValidationPipe } from '@nestjs/common';
import 'dotenv/config';

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);

  // ✅ CORS para el frontend
  app.enableCors({
    origin: 'http://localhost:3000',
    credentials: true,
  });

  // ✅ Validación global + transformación automática (para FormData)
  app.useGlobalPipes(
    new ValidationPipe({
      transform: true,
      whitelist: true,
      forbidNonWhitelisted: false, // puedes poner true en producción
    }),
  );

  // ✅ Servir archivos estáticos
  app.useStaticAssets(join(process.cwd(), 'uploads'), {
    prefix: '/uploads/',
  });

  await app.listen(3001);
  console.log(`🚀 Backend escuchando en: http://localhost:3001`);
}
bootstrap();
