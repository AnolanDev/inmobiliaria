import {
    Controller,
    Get,
    Post,
    Body,
    Patch,
    Param,
    Delete,
    Query,
    UseGuards,
    ParseIntPipe,
  } from '@nestjs/common';
  import { SalesService } from './sales.service';
  import { CreateSaleDto } from './dto/create-sale.dto';
  import { UpdateSaleDto } from './dto/update-sale.dto';
  import { AuthGuard } from '@nestjs/passport';
  import { RolesGuard } from 'src/common/guards/roles.guards';
  import { Roles } from 'src/common/decorators/roles.decorators';
  
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Controller('sales')
  export class SalesController {
    constructor(private readonly salesService: SalesService) {}
  
    @Post()
    @Roles(1) // solo Admin
    create(@Body() dto: CreateSaleDto) {
      return this.salesService.create(dto);
    }
  
    @Get()
    findAll() {
      return this.salesService.findAll();
    }
  
    @Get(':id')
    findOne(@Param('id', ParseIntPipe) id: number) {
      return this.salesService.findOne(id);
    }
  
    @Patch(':id')
    @Roles(1)
    update(
      @Param('id', ParseIntPipe) id: number,
      @Body() dto: UpdateSaleDto,
    ) {
      return this.salesService.update(id, dto);
    }
  
    @Delete(':id')
    @Roles(1)
    remove(@Param('id', ParseIntPipe) id: number) {
      return this.salesService.remove(id);
    }
  
    @Get('advisor/:advisorId')
    getByAdvisor(@Param('advisorId', ParseIntPipe) advisorId: number) {
      return this.salesService.getSalesByAdvisor(advisorId);
    }
  
    @Get('project/:projectId')
    getByProject(@Param('projectId', ParseIntPipe) projectId: number) {
      return this.salesService.getSalesByProject(projectId);
    }
  
    @Get('kpi')
    getKpi(
      @Query('start') start: string,
      @Query('end') end: string,
    ) {
      const startDate = new Date(start);
      const endDate = new Date(end);
      return this.salesService.getKpiSales(startDate, endDate);
    }
  }