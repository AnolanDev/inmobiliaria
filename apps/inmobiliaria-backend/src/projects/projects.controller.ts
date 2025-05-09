// src/projects/projects.controller.ts
import {
  Controller,
  Get,
  Post,
  Put,
  Param,
  Body,
  UseInterceptors,
  UploadedFile,
  UploadedFiles,
  ParseIntPipe,
} from '@nestjs/common';
import { FileInterceptor, FilesInterceptor } from '@nestjs/platform-express';
import { diskStorage } from 'multer';
import { extname } from 'path';

import { ProjectsService } from './projects.service';
import { CreateProjectDto } from './dto/create-project.dto';
import { UpdateProjectDto } from './dto/update-project.dto';

import { Public } from '../auth/public.decorator';
import { Roles } from '../auth/roles.decorator';

/** Helper para no repetir la config de multer */
function storageConfig(folder: 'logos' | 'gallery') {
  return diskStorage({
    destination: `./uploads/${folder}`,
    filename: (_req, file, cb) => {
      const name = `${Date.now()}-${Math.round(Math.random() * 1e9)}`;
      cb(null, `${name}${extname(file.originalname)}`);
    },
  });
}

@Controller('projects')
export class ProjectsController {
  constructor(private readonly projectsService: ProjectsService) {}

  /** GET /projects — público */
  @Public()
  @Get()
  findAll() {
    return this.projectsService.findAll();
  }

  /** GET /projects/:id — público */
  @Public()
  @Get(':id')
  findOne(@Param('id', ParseIntPipe) id: number) {
    return this.projectsService.findOne(id);
  }

  /** POST /projects — requiere permiso */
  @Post()
  @Roles('Admin', 'CREATE_PROJECT')
  @UseInterceptors(
    FileInterceptor('logo', { storage: storageConfig('logos') }),
    FilesInterceptor('gallery', 10, { storage: storageConfig('gallery') }),
  )
  create(
    @Body() dto: CreateProjectDto,
    @UploadedFile() logo: Express.Multer.File,
    @UploadedFiles() gallery: Express.Multer.File[],
  ) {
    if (logo) dto.logoUrl = `/uploads/logos/${logo.filename}`;
    if (gallery?.length) {
      dto.galleryUrls = gallery.map((file) => ({
        url: `/uploads/gallery/${file.filename}`,
        type: file.mimetype,
      }));
    }
    return this.projectsService.create(dto);
  }

  /** PUT /projects/:id — requiere permiso */
  @Put(':id')
  @Roles('Admin', 'UPDATE_PROJECT')
  @UseInterceptors(
    FileInterceptor('logo', { storage: storageConfig('logos') }),
    FilesInterceptor('gallery', 10, { storage: storageConfig('gallery') }),
  )
  update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: UpdateProjectDto,
    @UploadedFile() logo: Express.Multer.File,
    @UploadedFiles() gallery: Express.Multer.File[],
  ) {
    if (logo) dto.logoUrl = `/uploads/logos/${logo.filename}`;
    if (gallery?.length) {
      dto.galleryUrls = gallery.map((file) => ({
        url: `/uploads/gallery/${file.filename}`,
        type: file.mimetype,
      }));
    }
    return this.projectsService.update(id, dto);
  }
}
