// src/properties/properties.service.ts
import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService }           from '../../prisma/prisma.service';
import { CreatePropertyDto }       from './dto/create-property.dto';
import { UpdatePropertyDto }       from './dto/update-property.dto';
import { PropertyStatus }          from '@prisma/client';

@Injectable()
export class PropertiesService {
  constructor(private readonly prisma: PrismaService) {}

  /** Lista todas las propiedades */
  async findAll() {
    return this.prisma.property.findMany({
      orderBy: { createdAt: 'desc' },
      // si necesitas incluir relaciones, por ejemplo proyecto:
      // include: { project: true }
    });
  }

  /** Crea una nueva propiedad */
  async create(dto: CreatePropertyDto) {
    const { title, description, price, status, imageUrl, projectId } = dto;
    return this.prisma.property.create({
      data: {
        title,
        description,
        price,
        status: status as PropertyStatus,
        imageUrl,
        project: { connect: { id: projectId } },
      },
    });
  }

  /** Busca una propiedad por su ID */
  async findOne(id: number) {
    const prop = await this.prisma.property.findUnique({ where: { id } });
    if (!prop) throw new NotFoundException('Propiedad no encontrada');
    return prop;
  }

  /** Actualiza una propiedad */
  async update(id: number, dto: UpdatePropertyDto) {
    const { projectId, ...rest } = dto;
    if (rest.status && !Object.values(PropertyStatus).includes(rest.status))
      throw new Error('Estado inválido');

    return this.prisma.property.update({
      where: { id },
      data: {
        ...(rest.title       !== undefined && { title: rest.title }),
        ...(rest.description !== undefined && { description: rest.description }),
        ...(rest.price       !== undefined && { price: rest.price }),
        ...(rest.status      !== undefined && { status: rest.status as PropertyStatus }),
        ...(rest.imageUrl    !== undefined && { imageUrl: rest.imageUrl }),
      },
    });
  }

  /** Elimina una propiedad */
  async remove(id: number) {
    await this.findOne(id);
    await this.prisma.property.delete({ where: { id } });
    return { message: 'Propiedad eliminada correctamente' };
  }
}
