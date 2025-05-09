// src/properties/dto/update-property.dto.ts
import { PartialType } from '@nestjs/mapped-types';
import { CreatePropertyDto } from './create-property.dto';

export class UpdatePropertyDto extends PartialType(CreatePropertyDto) {
  // Hereda todos los campos como opcionales,
  // pero en el servicio eliminaremos projectId antes de actualizar.
}