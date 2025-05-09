// prisma/seed.ts
import { PrismaClient, Permission, Role } from '@prisma/client';
const prisma = new PrismaClient();

async function main() {
  const perms = ['CREATE_PROJECT','EDIT_PROJECT','VIEW_PROJECT','DELETE_PROJECT'];
  for (const name of perms) {
    await prisma.permission.upsert({
      where: { name },
      update: {},
      create: { name },
    });
  }

  // Roles
  await prisma.role.upsert({
    where: { name: 'Admin' },
    update: {},
    create: { name: 'Admin' },
  });
  await prisma.role.upsert({
    where: { name: 'User' },
    update: {},
    create: { name: 'User' },
  });

  // Asigna todos los permisos al rol Admin
  const admin = await prisma.role.findUnique({ where: { name: 'Admin' } });
  const allPerms = await prisma.permission.findMany();
  if (admin) {
    await prisma.rolePermission.deleteMany({ where: { roleId: admin.id } });
    await prisma.rolePermission.createMany({
      data: allPerms.map(p => ({ roleId: admin.id, permissionId: p.id })),
    });
  }

  console.log('✅ Seed completo');
}

main()
  .catch(e => { console.error(e); process.exit(1); })
  .finally(() => prisma.$disconnect());
