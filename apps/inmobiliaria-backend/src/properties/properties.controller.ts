// src/properties/properties.controller.ts
import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Param,
  Body,
  ParseIntPipe,
} from '@nestjs/common';
import { PropertiesService } from './properties.service';
import { CreatePropertyDto } from './dto/create-property.dto';
import { UpdatePropertyDto } from './dto/update-property.dto';

@Controller('properties')
export class PropertiesController {
  constructor(private readonly propertiesService: PropertiesService) {}

  // GET /properties
  @Get()
  async getAll() {
    return this.propertiesService.findAll();
  }

  // GET /properties/:id
  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    return this.propertiesService.findOne(id);
  }

  // POST /properties
  @Post()
  async create(@Body() dto: CreatePropertyDto) {
    // dto.status ya es un enum PropertyStatus válido
    return this.propertiesService.create(dto);
  }

  // PUT /properties/:id
  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: UpdatePropertyDto,
  ) {
    return this.propertiesService.update(id, dto);
  }

  // DELETE /properties/:id
  @Delete(':id')
  async remove(@Param('id', ParseIntPipe) id: number) {
    return this.propertiesService.remove(id);
  }
}
