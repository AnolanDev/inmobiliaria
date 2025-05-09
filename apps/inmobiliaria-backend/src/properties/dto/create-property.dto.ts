// src/properties/dto/create-property.dto.ts
import { IsString, IsNumber, IsEnum, IsOptional } from 'class-validator';
import { PropertyStatus } from '@prisma/client';

export class CreatePropertyDto {
  @IsString()
  title: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsNumber()
  price: number;

  @IsEnum(PropertyStatus)
  status: PropertyStatus;

  @IsOptional()
  @IsString()
  imageUrl?: string;

  @IsNumber()
  projectId: number;   // obligatorio al crear
}

  