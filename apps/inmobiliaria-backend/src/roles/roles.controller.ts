import {
    Controller, Get, Post, Put, Delete,
    Param, Body, ParseIntPipe, UseGuards
  } from '@nestjs/common';
  import { JwtAuthGuard } from '../auth/jwt-auth.guard';
  import { RolesGuard }   from '../auth/roles.guard';
  import { Roles }        from '../auth/roles.decorator';
  import { RolesService } from './roles.service';
  import { CreateRoleDto } from './dto/create-role.dto';
  import { UpdateRoleDto } from './dto/update-role.dto';
  
  @Controller('roles')
  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles('Admin')  // sólo Admin maneja roles
  export class RolesController {
    constructor(private svc: RolesService) {}
  
    @Post() create(@Body() dto: CreateRoleDto) { return this.svc.create(dto); }
    @Get() findAll() { return this.svc.findAll(); }
    @Get(':id') findOne(@Param('id', ParseIntPipe) id: number) { return this.svc.findOne(id); }
    @Put(':id') update(@Param('id', ParseIntPipe) id: number, @Body() dto: UpdateRoleDto) {
      return this.svc.update(id, dto);
    }
    @Delete(':id') remove(@Param('id', ParseIntPipe) id: number) {
      return this.svc.remove(id);
    }
  
    @Put(':id/permissions')
    setPermissions(
      @Param('id', ParseIntPipe) id: number,
      @Body('permIds') permIds: number[]
    ) {
      return this.svc.setPermissions(id, permIds);
    }
  }
  