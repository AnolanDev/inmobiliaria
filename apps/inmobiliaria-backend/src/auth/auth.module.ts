// src/auth/auth.module.ts
import { Module }      from '@nestjs/common';
import { JwtModule }   from '@nestjs/jwt';
import { PassportModule } from '@nestjs/passport';
import { APP_GUARD }   from '@nestjs/core';

import { AuthService }    from './auth.service';
import { AuthController } from './auth.controller';
import { JwtStrategy }    from './strategies/jwt.strategy';
import { JwtAuthGuard }   from './jwt-auth.guard';
import { RolesGuard }     from './roles.guard';

import { UsersModule } from '../users/users.module';

@Module({
  imports: [
    PassportModule.register({ defaultStrategy: 'jwt' }),
    JwtModule.register({
      secret: process.env.JWT_SECRET as string,
      signOptions: { expiresIn: process.env.JWT_EXPIRES_IN },
    }),
    UsersModule,
  ],
  providers: [
    AuthService,
    JwtStrategy,
    // JWT guard global
    { provide: APP_GUARD, useClass: JwtAuthGuard },
    // Roles guard global
    { provide: APP_GUARD, useClass: RolesGuard },
  ],
  controllers: [AuthController],
  exports: [AuthService],
})
export class AuthModule {}
