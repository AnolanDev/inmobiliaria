import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { CreateProjectDto } from './dto/create-project.dto';
import { UpdateProjectDto } from './dto/update-project.dto';
import { ProjectStatus } from '@prisma/client';

@Injectable()
export class ProjectsService {
  constructor(private readonly prisma: PrismaService) {}

  async create(dto: CreateProjectDto) {
    const { galleryUrls = [], ...rest } = dto;

    return this.prisma.project.create({
      data: {
        ...rest,
        gallery: {
          create: galleryUrls.map(({ url, type }) => ({ url, type })),
        },
      },
      include: {
        gallery: true,
      },
    });
  }

  async findAll() {
    return this.prisma.project.findMany({
      include: {
        gallery: true,
      },
      orderBy: {
        createdAt: 'desc',
      },
    });
  }

  async findOne(id: number) {
    const project = await this.prisma.project.findUnique({
      where: { id },
      include: { gallery: true },
    });

    if (!project) {
      throw new NotFoundException('Proyecto no encontrado');
    }

    return project;
  }

  async update(id: number, dto: UpdateProjectDto) {
    const { galleryUrls = [], status, ...rest } = dto;

    const safeStatus =
      status && Object.values(ProjectStatus).includes(status)
        ? status
        : undefined;

    await this.findOne(id); // Asegura que existe antes de actualizar

    return this.prisma.$transaction(async (tx) => {
      await tx.projectGallery.deleteMany({
        where: { projectId: id },
      });

      return tx.project.update({
        where: { id },
        data: {
          ...rest,
          ...(safeStatus && { status: safeStatus }),
          gallery: {
            create: galleryUrls.map(({ url, type }) => ({ url, type })),
          },
        },
        include: { gallery: true },
      });
    });
  }

  async remove(id: number) {
    await this.findOne(id); // Valida existencia
    await this.prisma.project.delete({ where: { id } });
    return { message: 'Proyecto eliminado correctamente' };
  }
}
