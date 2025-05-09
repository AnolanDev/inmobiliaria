import { Injectable, ConflictException, NotFoundException } from '@nestjs/common';
import { PrismaService } from 'prisma/prisma.service';
import { CreateUserDto } from './dto/create-user.dto';
import { UpdateUserDto } from './dto/update-user.dto';
import * as bcrypt from 'bcrypt';

@Injectable()
export class UsersService {
  constructor(private readonly prisma: PrismaService) {}

  async create(dto: CreateUserDto) {
    // Verificar email único
    const exists = await this.prisma.user.findUnique({ where: { email: dto.email } });
    if (exists) throw new ConflictException('Email ya registrado');

    // Hashear password
    const hash = await bcrypt.hash(dto.password, 10);

    const user = await this.prisma.user.create({
      data: {
        email: dto.email,
        password: hash,
        fullName: dto.fullName,
        role: { connect: { id: dto.roleId } },
      },
      include: { role: true },
    });
    delete (user as any).password;
    return user;
  }

  findAll() {
    return this.prisma.user.findMany({
      select: { id: true, email: true, fullName: true, role: { select: { id: true, name: true } }, createdAt: true },
    });
  }

  async findOne(id: number) {
    const user = await this.prisma.user.findUnique({
      where: { id },
      select: { id: true, email: true, fullName: true, role: { select: { id: true, name: true } }, createdAt: true },
    });
    if (!user) throw new NotFoundException('Usuario no encontrado');
    return user;
  }

  async update(id: number, dto: UpdateUserDto) {
    const data: any = { fullName: dto.fullName };
    if (dto.password) {
      data.password = await bcrypt.hash(dto.password, 10);
    }
    if (dto.roleId) {
      data.role = { connect: { id: dto.roleId } };
    }

    const updated = await this.prisma.user.update({
      where: { id },
      data,
      select: { id: true, email: true, fullName: true, role: { select: { id: true, name: true } }, updatedAt: true },
    });
    return updated;
  }

  remove(id: number) {
    return this.prisma.user.delete({ where: { id } });
  }

  async findByEmailWithPassword(email: string) {
    return this.prisma.user.findUnique({
      where: { email },
      include: { role: true },
    });
  }
  
}
