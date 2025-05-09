import {
    IsOptional,
    IsString,
    IsNumber,
    IsArray,
    ValidateNested,
    IsEnum,
  } from 'class-validator';
  import { Type } from 'class-transformer';
  import { ProjectStatus } from '@prisma/client';
  
  export class GalleryItemDto {
    @IsString()
    url: string;
  
    @IsString()
    type: string;
  }
  
  export class UpdateProjectDto {
    @IsOptional()
    @IsString()
    name?: string;
  
    @IsOptional()
    @IsString()
    description?: string;
  
    @IsOptional()
    @IsNumber()
    latitude?: number;
  
    @IsOptional()
    @IsNumber()
    longitude?: number;
  
    @IsOptional()
    @IsString()
    address?: string;
  
    @IsOptional()
    @IsString()
    country?: string;
  
    @IsOptional()
    @IsString()
    department?: string;
  
    @IsOptional()
    @IsString()
    city?: string;
  
    @IsOptional()
    @IsString()
    logoUrl?: string;
  
    @IsOptional()
    @IsString()
    imageUrl?: string;
  
    @IsOptional()
    @IsEnum(ProjectStatus, {
      message: 'El estado debe ser DISPONIBLE o VENDIDO',
    })
    status?: ProjectStatus;
  
    @IsOptional()
    @IsArray()
    @ValidateNested({ each: true })
    @Type(() => GalleryItemDto)
    galleryUrls?: GalleryItemDto[];
  }
  