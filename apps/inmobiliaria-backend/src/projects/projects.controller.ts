// src/projects/projects.controller.ts
import {
  Controller, Get, Post, Put, Param, Body,
  UseInterceptors, UploadedFiles, ParseIntPipe, Query
} from '@nestjs/common';
import { FileFieldsInterceptor } from '@nestjs/platform-express';
import { diskStorage } from 'multer';
import { join, extname } from 'path';
import { existsSync, mkdirSync } from 'fs';


import { ProjectsService } from './projects.service';
import { CreateProjectDto } from './dto/create-project.dto';
import { UpdateProjectDto } from './dto/update-project.dto';
import { Public } from '../auth/public.decorator';
import { Roles } from '../auth/roles.decorator';

function storageConfig() {
  return diskStorage({
    destination: (_req, file, cb) => {
      // Determina la carpeta absoluta bajo apps/inmobiliaria-backend/uploads
      const folder =
        file.fieldname === 'logo'
          ? 'logos'
          : file.fieldname === 'image'
            ? 'images'
            : 'gallery';

      // Usamos process.cwd() para obtener la ruta raíz del proyecto
      const uploadPath = join(
        process.cwd(),
        'uploads',
        folder
      );

      // Crea la carpeta si no existe
      if (!existsSync(uploadPath)) {
        mkdirSync(uploadPath, { recursive: true });
      }

      cb(null, uploadPath);
    },
    filename: (_req, file, cb) => {
      const name = `${Date.now()}-${Math.round(Math.random() * 1e9)}`;
      cb(null, `${name}${extname(file.originalname)}`);
    },
  });
}

@Controller('projects')
export class ProjectsController {
  constructor(private readonly projectsService: ProjectsService) {}

  @Public()
  @Get()
  findAll(@Query('status') status?: string) {
    return this.projectsService.findAll(status);
  }

  @Public()
  @Get(':id')
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.projectsService.findOne(id);
  }

  @Post()
  @Roles('Admin', 'CREATE_PROJECT')
  @UseInterceptors(
    FileFieldsInterceptor(
      [
        { name: 'logo', maxCount: 1 },
        { name: 'image', maxCount: 1 },
        { name: 'gallery', maxCount: 10 },
      ],
      { storage: storageConfig() }
    )
  )
  create(
    @Body() dto: CreateProjectDto,
    @UploadedFiles()
    files: {
      logo?: Express.Multer.File[];
      image?: Express.Multer.File[];
      gallery?: Express.Multer.File[];
    }
  ) {
    if (files.logo?.[0]) {
      dto.logoUrl = `/uploads/logos/${files.logo[0].filename}`;
    }
    if (files.image?.[0]) {
      dto.imageUrl = `/uploads/images/${files.image[0].filename}`;
    }
    if (files.gallery?.length) {
      dto.galleryUrls = files.gallery.map((file) => ({
        url: `/uploads/gallery/${file.filename}`,
        type: file.mimetype,
      }));
    }
    return this.projectsService.create(dto);
  }

  @Put(':id')
  @Roles('Admin', 'UPDATE_PROJECT')
  @UseInterceptors(
    FileFieldsInterceptor(
      [
        { name: 'logo', maxCount: 1 },
        { name: 'image', maxCount: 1 },
        { name: 'gallery', maxCount: 10 },
      ],
      { storage: storageConfig() }
    )
  )
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: UpdateProjectDto,
    @UploadedFiles()
    files: {
      logo?: Express.Multer.File[];
      image?: Express.Multer.File[];
      gallery?: Express.Multer.File[];
    }
  ) {
    if (files.logo?.[0]) {
      dto.logoUrl = `/uploads/logos/${files.logo[0].filename}`;
    }
    if (files.image?.[0]) {
      dto.imageUrl = `/uploads/images/${files.image[0].filename}`;
    }
    if (files.gallery?.length) {
      dto.galleryUrls = files.gallery.map((file) => ({
        url: `/uploads/gallery/${file.filename}`,
        type: file.mimetype,
      }));
    }
    return this.projectsService.update(id, dto);
  }
}
