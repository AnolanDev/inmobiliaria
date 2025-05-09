import {
    Injectable,
    CanActivate,
    ExecutionContext,
    ForbiddenException
  } from '@nestjs/common';
  import { Reflector } from '@nestjs/core';
  import { ROLES_KEY } from './roles.decorator';
  import { PrismaService } from 'prisma/prisma.service';
  
  @Injectable()
  export class RolesGuard implements CanActivate {
    constructor(
      private reflector: Reflector,
      private prisma: PrismaService
    ) {}
  
    async canActivate(ctx: ExecutionContext): Promise<boolean> {
      const required = this.reflector.get<string[]>(
        ROLES_KEY,
        ctx.getHandler()
      );
      if (!required || required.length === 0) {
        return true;
      }
  
      const req = ctx.switchToHttp().getRequest();
      const user = req.user;
      if (!user?.id) {
        throw new ForbiddenException('No autenticado');
      }
  
      const dbUser = await this.prisma.user.findUnique({
        where: { id: user.id },
        include: {
          role: {
            include: { 
              permissions: { include: { permission: true } } 
            }
          }
        }
      });
      if (!dbUser) {
        throw new ForbiddenException('Usuario no existe');
      }
  
      const roleName = dbUser.role.name;
      if (required.includes(roleName)) return true;
  
      const perms = dbUser.role.permissions.map(rp => rp.permission.name);
      if (perms.some(p => required.includes(p))) return true;
  
      throw new ForbiddenException('Permisos insuficientes');
    }
  }
  