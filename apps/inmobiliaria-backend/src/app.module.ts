import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { ServeStaticModule } from '@nestjs/serve-static';
import { join } from 'path';

import { AppController } from './app.controller';
import { AppService }     from './app.service';

import { PrismaModule }    from '../prisma/prisma.module';
import { AuthModule }      from './auth/auth.module';
import { UsersModule }     from './users/users.module';
import { AdvisorsModule }  from './advisors/advisors.module';
import { ProjectsModule }  from './projects/projects.module';
import { PropertiesModule } from './properties/properties.module';
import { SalesModule }     from './sales/sales.module';
import { RolesModule }     from './roles/roles.module';
import { PermissionsModule } from './permissions/permissions.module';

import { UploadService }    from './common/upload/upload.service';
import { UploadController } from './common/upload/upload.controller';

@Module({
  imports: [
    ConfigModule.forRoot({ isGlobal: true }),

    // Sirve todo el directorio uploads/* como estático en /uploads/*
    ServeStaticModule.forRoot({
      serveRoot: '/uploads',                  // URL pública
      rootPath: join(process.cwd(), 'uploads'), // carpeta en disco
      serveStaticOptions: {
        index: false,    // no busca ni sirve index.html
        redirect: false, // sin redirecciones automáticas
        extensions: [],  // no añade extensiones
      },
    }),

    PrismaModule,
    AuthModule,
    UsersModule,
    AdvisorsModule,
    ProjectsModule,
    PropertiesModule,
    SalesModule,
    RolesModule,
    PermissionsModule,
  ],
  controllers: [AppController, UploadController],
  providers: [AppService, UploadService],
})
export class AppModule {}