import {
    Controller,
    Get,
    Post,
    Body,
    Patch,
    Param,
    Delete,
    UseGuards,
    ParseIntPipe,
  } from '@nestjs/common';
  import { AdvisorsService } from './advisors.service';
  import { CreateAdvisorDto } from './dto/create-advisor.dto';
  import { UpdateAdvisorDto } from './dto/update-advisor.dto';
  import { AuthGuard } from '@nestjs/passport';
  import { RolesGuard } from 'src/common/guards/roles.guards';
  import { Roles } from 'src/common/decorators/roles.decorators';
  @UseGuards(AuthGuard('jwt'), RolesGuard)
  @Controller('advisors')
  export class AdvisorsController {
    constructor(private readonly advisorsService: AdvisorsService) {}
  
    @Post()
    @Roles(1) // solo Admin puede crear
    create(@Body() dto: CreateAdvisorDto) {
      return this.advisorsService.create(dto);
    }
  
    @Get()
    findAll() {
      return this.advisorsService.findAll();
    }
  
    @Get(':id')
    findOne(@Param('id', ParseIntPipe) id: number) {
      return this.advisorsService.findOne(id);
    }
  
    @Patch(':id')
    @Roles(1) // solo Admin puede actualizar
    update(
      @Param('id', ParseIntPipe) id: number,
      @Body() dto: UpdateAdvisorDto,
    ) {
      return this.advisorsService.update(id, dto);
    }
  
    @Delete(':id')
    @Roles(1) // solo Admin puede eliminar
    remove(@Param('id', ParseIntPipe) id: number) {
      return this.advisorsService.remove(id);
    }
  }