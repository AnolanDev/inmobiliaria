import { IsEmail, IsNotEmpty, IsString, IsOptional, IsInt } from 'class-validator';

export class CreateUserDto {
  @IsEmail()  
  email: string;

  @IsString()
  @IsNotEmpty()
  password: string;

  @IsString()
  @IsOptional()
  fullName?: string;

  @IsInt()
  roleId: number;
}
