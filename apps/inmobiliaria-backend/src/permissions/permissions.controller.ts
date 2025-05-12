import {
    Controller, Get, Post, Put, Delete,
    Param, Body, ParseIntPipe, UseGuards
  } from '@nestjs/common';
  import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
  import { RolesGuard }   from '../auth/roles.guard';
  import { Roles }        from '../auth/roles.decorator';
  import { PermissionsService } from './permissions.service';
  import { CreatePermissionDto } from './dto/create-permission.dto';
  import { UpdatePermissionDto } from './dto/update-permission.dto';
  
  @Controller('permissions')
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('Admin')
  export class PermissionsController {
    constructor(private svc: PermissionsService) {}
  
    @Post() create(@Body() dto: CreatePermissionDto) { return this.svc.create(dto); }
    @Get() findAll() { return this.svc.findAll(); }
    @Get(':id') findOne(@Param('id', ParseIntPipe) id: number) { return this.svc.findOne(id); }
    @Put(':id') update(@Param('id', ParseIntPipe) id: number, @Body() dto: UpdatePermissionDto) {
      return this.svc.update(id, dto);
    }
    @Delete(':id') remove(@Param('id', ParseIntPipe) id: number) {
      return this.svc.remove(id);
    }
  }
  