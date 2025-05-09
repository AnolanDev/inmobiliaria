// src/app.module.ts
import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { ServeStaticModule } from '@nestjs/serve-static';
import { join } from 'path';

import { AppController } from './app.controller';
import { AppService }    from './app.service';

import { PrismaModule }   from '../prisma/prisma.module';

import { AuthModule }        from './auth/auth.module';
import { UsersModule }       from './users/users.module';
import { AdvisorsModule }    from './advisors/advisors.module';
import { ProjectsModule }    from './projects/projects.module';
import { PropertiesModule }  from './properties/properties.module';
import { SalesModule }       from './sales/sales.module';
import { RolesModule }       from './roles/roles.module';
import { PermissionsModule } from './permissions/permissions.module';

import { UploadService }     from './common/upload/upload.service';
import { UploadController }  from './common/upload/upload.controller';

@Module({
  imports: [
    // Variables de entorno (isGlobal permite acceder a process.env.* en cualquier módulo)
    ConfigModule.forRoot({ isGlobal: true }),

    // Sirve archivos estáticos
    ServeStaticModule.forRoot({
      rootPath: join(process.cwd(), 'uploads'),
      serveRoot: '/uploads',
    }),

    // Prisma + cliente
    PrismaModule,

    // Auth (JWT + RolesGuard global)
    AuthModule,

    // Módulos de negocio
    UsersModule,
    AdvisorsModule,
    ProjectsModule,
    PropertiesModule,
    SalesModule,

    // Roles & Permisos
    RolesModule,
    PermissionsModule,
  ],
  controllers: [
    AppController,
    UploadController,
  ],
  providers: [
    AppService,
    UploadService,
    // ← NO es necesario volver a listar PrismaService aquí
  ],
})
export class AppModule {}
