/*
  Warnings:

  - You are about to alter the column `status` on the `property` table. The data in that column could be lost. The data in that column will be cast from `Enum(EnumId(0))` to `Enum(EnumId(1))`.

*/
-- AlterTable
ALTER TABLE `project` ADD COLUMN `imageUrl` VARCHAR(191) NULL,
    ADD COLUMN `status` ENUM('DISPONIBLE', 'VENDIDO') NOT NULL DEFAULT 'DISPONIBLE',
    MODIFY `name` VARCHAR(512) NOT NULL,
    MODIFY `description` TEXT NULL,
    MODIFY `address` TEXT NULL;

-- AlterTable
ALTER TABLE `property` MODIFY `title` VARCHAR(512) NOT NULL,
    MODIFY `description` TEXT NULL,
    MODIFY `status` ENUM('DISPONIBLE', 'VENDIDO') NOT NULL DEFAULT 'DISPONIBLE';
