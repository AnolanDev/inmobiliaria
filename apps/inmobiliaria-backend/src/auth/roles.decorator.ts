// src/auth/roles.decorator.ts
import { SetMetadata } from '@nestjs/common';

export const ROLES_KEY = 'roles';
/**
 * @Roles('Admin', 'VIEW_PROJECT')
 * Permite tanto por nombre de rol como por permiso simple.
 */
export const Roles = (...roles: string[]) => SetMetadata(ROLES_KEY, roles);
