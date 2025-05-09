import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { CreateAdvisorDto } from './dto/create-advisor.dto';
import { UpdateAdvisorDto } from './dto/update-advisor.dto';

@Injectable()
export class AdvisorsService {
  constructor(private readonly prisma: PrismaService) {}

  async create(dto: CreateAdvisorDto) {
    return this.prisma.advisor.create({ data: dto });
  }

  async findAll() {
    return this.prisma.advisor.findMany();
  }

  async findOne(id: number) {
    const advisor = await this.prisma.advisor.findUnique({ where: { id } });
    if (!advisor) throw new NotFoundException('Advisor not found');
    return advisor;
  }

  async update(id: number, dto: UpdateAdvisorDto) {
    await this.findOne(id);
    return this.prisma.advisor.update({ where: { id }, data: dto });
  }

  async remove(id: number) {
    await this.findOne(id);
    await this.prisma.advisor.delete({ where: { id } });
    return { message: 'Deleted successfully' };
  }
}