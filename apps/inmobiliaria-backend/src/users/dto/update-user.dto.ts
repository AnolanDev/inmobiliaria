import { PartialType } from '@nestjs/mapped-types';
import { CreateUserDto } from './create-user.dto';
import { IsOptional, IsInt, IsString } from 'class-validator';

export class UpdateUserDto extends PartialType(CreateUserDto) {
  @IsString()
  @IsOptional()
  password?: string;   // opcionalmente permitir cambiar

  @IsInt()
  @IsOptional()
  roleId?: number;
}
