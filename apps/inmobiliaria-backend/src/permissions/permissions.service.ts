import { Injectable, ConflictException, NotFoundException } from '@nestjs/common';
import { PrismaService } from 'prisma/prisma.service';
import { CreatePermissionDto } from './dto/create-permission.dto';
import { UpdatePermissionDto } from './dto/update-permission.dto';

@Injectable()
export class PermissionsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreatePermissionDto) {
    if (await this.prisma.permission.findUnique({ where: { name: dto.name } }))
      throw new ConflictException('Permiso ya existe');
    return this.prisma.permission.create({ data: { name: dto.name } });
  }

  findAll() {
    return this.prisma.permission.findMany();
  }

  async findOne(id: number) {
    const p = await this.prisma.permission.findUnique({ where: { id } });
    if (!p) throw new NotFoundException('Permiso no encontrado');
    return p;
  }

  update(id: number, dto: UpdatePermissionDto) {
    return this.prisma.permission.update({
      where: { id },
      data: { name: dto.name }
    });
  }

  remove(id: number) {
    return this.prisma.permission.delete({ where: { id } });
  }
}
