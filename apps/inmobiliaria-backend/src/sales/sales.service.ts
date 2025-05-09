import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { CreateSaleDto } from './dto/create-sale.dto';
import { UpdateSaleDto } from './dto/update-sale.dto';

@Injectable()
export class SalesService {
  constructor(private readonly prisma: PrismaService) {}

  async create(dto: CreateSaleDto) {
    return this.prisma.sale.create({ data: dto });
  }

  async findAll() {
    return this.prisma.sale.findMany({
      include: { property: { include: { project: true } }, advisor: true },
    });
  }

  async findOne(id: number) {
    const sale = await this.prisma.sale.findUnique({
      where: { id },
      include: { property: { include: { project: true } }, advisor: true },
    });
    if (!sale) throw new NotFoundException('Sale not found');
    return sale;
  }

  async update(id: number, dto: UpdateSaleDto) {
    await this.findOne(id);
    return this.prisma.sale.update({ where: { id }, data: dto });
  }

  async remove(id: number) {
    await this.findOne(id);
    await this.prisma.sale.delete({ where: { id } });
    return { message: 'Deleted successfully' };
  }

  async getSalesByAdvisor(advisorId: number) {
    return this.prisma.sale.findMany({
      where: { advisorId },
      include: { property: { include: { project: true } }, advisor: true },
    });
  }

  async getSalesByProject(projectId: number) {
    return this.prisma.sale.findMany({
      where: { property: { projectId } },
      include: { property: { include: { project: true } }, advisor: true },
    });
  }

  async getKpiSales(start: Date, end: Date) {
    const advisorStats = await this.prisma.sale.groupBy({
      by: ['advisorId'],
      where: { saleDate: { gte: start, lte: end } },
      _sum: { salePrice: true },
      _count: { id: true },
    });
    const projectStats = await this.prisma.sale.groupBy({
      by: ['propertyId'],
      where: { saleDate: { gte: start, lte: end } },
      _sum: { salePrice: true },
      _count: { id: true },
    });
    return { advisorStats, projectStats };
  }
}

