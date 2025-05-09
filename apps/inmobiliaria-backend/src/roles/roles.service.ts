import { Injectable, ConflictException, NotFoundException } from '@nestjs/common';
import { PrismaService } from 'prisma/prisma.service';
import { CreateRoleDto } from './dto/create-role.dto';
  import { UpdateRoleDto } from './dto/update-role.dto';

@Injectable()
export class RolesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateRoleDto) {
    if (await this.prisma.role.findUnique({ where: { name: dto.name } }))
      throw new ConflictException('Rol ya existe');
    return this.prisma.role.create({ data: { name: dto.name } });
  }

  findAll() {
    return this.prisma.role.findMany({
      include: { permissions: { include: { permission: true } } }
    });
  }

  async findOne(id: number) {
    const r = await this.prisma.role.findUnique({
      where: { id },
      include: { permissions: { include: { permission: true } } }
    });
    if (!r) throw new NotFoundException('Rol no encontrado');
    return r;
  }

  update(id: number, dto: UpdateRoleDto) {
    return this.prisma.role.update({ where: { id }, data: { name: dto.name } });
  }

  remove(id: number) {
    return this.prisma.role.delete({ where: { id } });
  }

  /** Asigna permisos */
  async setPermissions(roleId: number, permIds: number[]) {
    await this.prisma.rolePermission.deleteMany({ where: { roleId } });
    return this.prisma.rolePermission.createMany({
      data: permIds.map(pid => ({ roleId, permissionId: pid }))
    });
  }
}
