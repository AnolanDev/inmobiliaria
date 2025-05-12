import {
    Controller,
    Get,
    Post,
    Put,
    Delete,
    Body,
    Param,
    ParseIntPipe,
    UseGuards,
  } from '@nestjs/common';
  import { UsersService } from './users.service';
  import { CreateUserDto } from './dto/create-user.dto';
  import { UpdateUserDto } from './dto/update-user.dto';
  import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
  import { RolesGuard }   from '../auth/roles.guard';
  import { Roles }        from '../auth/roles.decorator';
  
  @Controller('users')
  @UseGuards(JwtAuthGuard, RolesGuard)
  export class UsersController {
    constructor(private readonly svc: UsersService) {}
  
    @Post()
    @Roles('Admin')
    create(@Body() dto: CreateUserDto) {
      return this.svc.create(dto);
    }
  
    @Get()
    @Roles('Admin')
    findAll() {
      return this.svc.findAll();
    }
  
    @Get(':id')
    @Roles('Admin')
    findOne(@Param('id', ParseIntPipe) id: number) {
      return this.svc.findOne(id);
    }
  
    @Put(':id')
    @Roles('Admin')
    update(
      @Param('id', ParseIntPipe) id: number,
      @Body() dto: UpdateUserDto
    ) {
      return this.svc.update(id, dto);
    }
  
    @Delete(':id')
    @Roles('Admin')
    remove(@Param('id', ParseIntPipe) id: number) {
      return this.svc.remove(id);
    }
  }
  