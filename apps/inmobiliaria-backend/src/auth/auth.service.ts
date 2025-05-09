// src/auth/auth.service.ts
import {
  Injectable,
  UnauthorizedException,
  ConflictException,
} from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcrypt';

import { UsersService } from '../users/users.service';
import { RegisterDto }  from './dto/register.dto';
import { LoginDto }     from './dto/login.dto';

@Injectable()
export class AuthService {
  constructor(
    private readonly usersService: UsersService,
    private readonly jwtService: JwtService
  ) {}

  async register(dto: RegisterDto) {
    // Reusar UsersService.create (ya hace hash de pwd y conecta rol)
    try {
      const user = await this.usersService.create(dto);
      // no devolvemos contraseña
      delete (user as any).password;
      return { message: 'Usuario registrado', user };
    } catch (e: any) {
      throw new ConflictException(e.message);
    }
  }

  private async validateUser(email: string, pass: string) {
    const user = await this.usersService.findByEmailWithPassword(email);
    if (!user) throw new UnauthorizedException('Credenciales inválidas');

    const match = await bcrypt.compare(pass, user.password);
    if (!match) throw new UnauthorizedException('Credenciales inválidas');

    // retiramos password antes de devolver
    const { password, ...rest } = user;
    return rest;
  }

  async login(dto: LoginDto) {
    const user = await this.validateUser(dto.email, dto.password);
    const payload = { sub: user.id, email: user.email, roleId: user.roleId };
    return {
      access_token: this.jwtService.sign(payload),
      user,
    };
  }
}
