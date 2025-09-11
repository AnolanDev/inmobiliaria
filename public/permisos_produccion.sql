-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: inmobiliaria_db
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6b7280',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`),
  KEY `roles_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrador','super-admin','Acceso completo a todas las funcionalidades del sistema','#dc2626',1,100,'{\"can_be_deleted\": false, \"is_system_role\": true, \"auto_permissions\": true}','2025-08-13 10:55:58','2025-08-13 10:55:58'),(2,'Administrador','admin','Administrador del sistema con acceso a la mayoría de funciones','#ea580c',1,90,'{\"can_be_deleted\": false, \"is_system_role\": true}','2025-08-13 10:55:58','2025-08-13 10:55:58'),(3,'Gerente','manager','Gerente con acceso a gestión de propiedades y reportes','#d97706',1,80,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(4,'Agente','agent','Agente inmobiliario con acceso a propiedades y visitas','#059669',1,70,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(5,'Asistente','assistant','Asistente con acceso limitado para apoyo operativo','#0891b2',1,60,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(6,'Cliente','client','Cliente con acceso a sus propias visitas e información','#7c3aed',1,50,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_module_action_index` (`module`,`action`),
  KEY `permissions_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Ver Dashboard','dashboard-view','Permite ver el dashboard principal con métricas y estadísticas','dashboard','view',1,1,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(2,'Ver Usuarios','users-view','Permite ver la lista de usuarios del sistema','users','view',1,1,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(3,'Crear Usuarios','users-create','Permite crear nuevos usuarios en el sistema','users','create',1,2,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(4,'Editar Usuarios','users-edit','Permite editar información de usuarios existentes','users','edit',1,3,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(5,'Eliminar Usuarios','users-delete','Permite eliminar usuarios del sistema','users','delete',1,4,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(6,'Gestionar Usuarios','users-manage','Permite gestión completa de usuarios incluyendo activar/desactivar','users','manage',1,5,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(7,'Ver Roles','roles-view','Permite ver la lista de roles del sistema','roles','view',1,1,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(8,'Crear Roles','roles-create','Permite crear nuevos roles en el sistema','roles','create',1,2,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(9,'Editar Roles','roles-edit','Permite editar roles existentes','roles','edit',1,3,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(10,'Eliminar Roles','roles-delete','Permite eliminar roles del sistema','roles','delete',1,4,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(11,'Gestionar Roles','roles-manage','Permite gestión completa de roles y asignación de permisos','roles','manage',1,5,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(12,'Ver Permisos','permissions-view','Permite ver la lista de permisos del sistema','permissions','view',1,1,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(13,'Gestionar Permisos','permissions-manage','Permite gestión completa de permisos del sistema','permissions','manage',1,2,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(14,'Ver Propiedades','properties-view','Permite ver la lista de propiedades','properties','view',1,1,NULL,'2025-08-13 10:55:57','2025-08-13 10:55:57'),(15,'Crear Propiedades','properties-create','Permite crear nuevas propiedades','properties','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(16,'Editar Propiedades','properties-edit','Permite editar propiedades existentes','properties','edit',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(17,'Eliminar Propiedades','properties-delete','Permite eliminar propiedades','properties','delete',1,4,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(18,'Gestionar Propiedades','properties-manage','Permite gestión completa de propiedades','properties','manage',1,5,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(19,'Exportar Propiedades','properties-export','Permite exportar datos de propiedades','properties','export',1,6,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(20,'Importar Propiedades','properties-import','Permite importar datos de propiedades','properties','import',1,7,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(21,'Ver Proyectos','projects-view','Permite ver la lista de proyectos','projects','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(22,'Crear Proyectos','projects-create','Permite crear nuevos proyectos','projects','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(23,'Editar Proyectos','projects-edit','Permite editar proyectos existentes','projects','edit',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(24,'Eliminar Proyectos','projects-delete','Permite eliminar proyectos','projects','delete',1,4,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(25,'Gestionar Proyectos','projects-manage','Permite gestión completa de proyectos','projects','manage',1,5,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(26,'Ver Clientes','clients-view','Permite ver la lista de clientes','clients','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(27,'Crear Clientes','clients-create','Permite crear nuevos clientes','clients','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(28,'Editar Clientes','clients-edit','Permite editar clientes existentes','clients','edit',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(29,'Eliminar Clientes','clients-delete','Permite eliminar clientes','clients','delete',1,4,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(30,'Gestionar Clientes','clients-manage','Permite gestión completa de clientes','clients','manage',1,5,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(31,'Exportar Clientes','clients-export','Permite exportar datos de clientes','clients','export',1,6,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(32,'Importar Clientes','clients-import','Permite importar datos de clientes','clients','import',1,7,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(33,'Ver Agentes','agents-view','Permite ver la lista de agentes','agents','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(34,'Crear Agentes','agents-create','Permite crear nuevos agentes','agents','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(35,'Editar Agentes','agents-edit','Permite editar agentes existentes','agents','edit',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(36,'Eliminar Agentes','agents-delete','Permite eliminar agentes','agents','delete',1,4,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(37,'Gestionar Agentes','agents-manage','Permite gestión completa de agentes','agents','manage',1,5,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(38,'Ver Visitas','visits-view','Permite ver el calendario y lista de visitas','visits','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(39,'Crear Visitas','visits-create','Permite agendar nuevas visitas','visits','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(40,'Editar Visitas','visits-edit','Permite modificar visitas existentes','visits','edit',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(41,'Eliminar Visitas','visits-delete','Permite eliminar visitas','visits','delete',1,4,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(42,'Gestionar Visitas','visits-manage','Permite gestión completa del calendario de visitas','visits','manage',1,5,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(43,'Ver Reportes','reports-view','Permite ver reportes y estadísticas del sistema','reports','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(44,'Crear Reportes','reports-create','Permite crear reportes personalizados','reports','create',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(45,'Exportar Reportes','reports-export','Permite exportar reportes en diferentes formatos','reports','export',1,3,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(46,'Ver Configuración','settings-view','Permite ver la configuración del sistema','settings','view',1,1,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(47,'Gestionar Configuración','settings-manage','Permite gestionar la configuración general del sistema','settings','manage',1,2,NULL,'2025-08-13 10:55:58','2025-08-13 10:55:58'),(48,'Ver Marketing','marketing-view','Permite acceder al módulo de marketing','marketing','view',1,1,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(49,'Ver Campañas','campaigns-view','Permite ver la lista de campañas de marketing','campaigns','view',1,1,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(50,'Crear Campañas','campaigns-create','Permite crear nuevas campañas de marketing','campaigns','create',1,2,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(51,'Editar Campañas','campaigns-edit','Permite editar campañas de marketing existentes','campaigns','edit',1,3,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(52,'Eliminar Campañas','campaigns-delete','Permite eliminar campañas de marketing','campaigns','delete',1,4,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(53,'Gestionar Campañas','campaigns-manage','Permite gestión completa de campañas de marketing','campaigns','manage',1,5,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(54,'Ver Leads','leads-view','Permite ver la lista de leads','leads','view',1,1,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(55,'Crear Leads','leads-create','Permite crear nuevos leads','leads','create',1,2,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(56,'Editar Leads','leads-edit','Permite editar leads existentes','leads','edit',1,3,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(57,'Eliminar Leads','leads-delete','Permite eliminar leads','leads','delete',1,4,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(58,'Gestionar Leads','leads-manage','Permite gestión completa de leads','leads','manage',1,5,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(59,'Exportar Leads','leads-export','Permite exportar datos de leads','leads','export',1,6,NULL,'2025-08-13 11:28:28','2025-08-13 11:28:28'),(60,'Ver Actividades','activities-view','Permite ver la lista de actividades y timeline','activities','view',1,1,NULL,'2025-08-13 12:45:39','2025-08-13 12:45:39'),(61,'Crear Actividades','activities-create','Permite crear nuevas actividades y tareas','activities','create',1,2,NULL,'2025-08-13 12:45:39','2025-08-13 12:45:39'),(62,'Editar Actividades','activities-edit','Permite editar actividades y cambiar su estado','activities','edit',1,3,NULL,'2025-08-13 12:45:39','2025-08-13 12:45:39'),(63,'Eliminar Actividades','activities-delete','Permite eliminar actividades','activities','delete',1,4,NULL,'2025-08-13 12:45:39','2025-08-13 12:45:39'),(64,'Gestionar Actividades','activities-manage','Permite gestión completa del sistema de actividades','activities','manage',1,5,NULL,'2025-08-13 12:45:39','2025-08-13 12:45:39'),(65,'Ver Email Marketing','email-marketing-view','Ver templates y campañas de email','email-marketing','view',1,0,NULL,'2025-08-14 00:18:48','2025-08-14 00:18:48'),(66,'Crear Email Marketing','email-marketing-create','Crear templates y campañas de email','email-marketing','create',1,0,NULL,'2025-08-14 00:18:48','2025-08-14 00:18:48'),(67,'Editar Email Marketing','email-marketing-edit','Editar templates y campañas de email','email-marketing','edit',1,0,NULL,'2025-08-14 00:18:49','2025-08-14 00:18:49'),(68,'Eliminar Email Marketing','email-marketing-delete','Eliminar templates y campañas de email','email-marketing','delete',1,0,NULL,'2025-08-14 00:18:49','2025-08-14 00:18:49'),(69,'Configurar Email Marketing','email-marketing-config','Configurar servicios y parámetros de email marketing','email-marketing','config',1,0,NULL,'2025-08-14 01:23:38','2025-08-14 01:23:38'),(70,'Ver Blogs','blogs-view','Permite ver la lista de blogs y artículos','blogs','view',1,1,NULL,'2025-08-18 20:52:15','2025-08-18 20:52:15'),(71,'Crear Blogs','blogs-create','Permite crear nuevos blogs y artículos','blogs','create',1,2,NULL,'2025-08-18 20:52:15','2025-08-18 20:52:15'),(72,'Editar Blogs','blogs-edit','Permite editar blogs existentes y cambiar su estado','blogs','edit',1,3,NULL,'2025-08-18 20:52:15','2025-08-18 20:52:15'),(73,'Eliminar Blogs','blogs-delete','Permite eliminar blogs y artículos','blogs','delete',1,4,NULL,'2025-08-18 20:52:15','2025-08-18 20:52:15');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (48,2,1,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(49,2,2,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(50,2,3,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(51,2,4,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(52,2,5,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(53,2,6,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(54,2,7,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(55,2,8,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(56,2,9,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(57,2,10,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(58,2,11,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(59,2,12,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(60,2,14,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(61,2,15,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(62,2,16,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(63,2,17,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(64,2,18,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(65,2,19,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(66,2,20,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(67,2,21,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(68,2,22,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(69,2,23,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(70,2,24,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(71,2,25,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(72,2,26,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(73,2,27,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(74,2,28,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(75,2,29,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(76,2,30,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(77,2,31,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(78,2,32,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(79,2,33,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(80,2,34,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(81,2,35,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(82,2,36,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(83,2,37,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(84,2,38,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(85,2,39,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(86,2,40,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(87,2,41,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(88,2,42,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(89,2,43,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(90,2,44,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(91,2,45,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(92,2,46,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(93,3,1,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(94,3,2,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(95,3,3,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(96,3,4,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(97,3,14,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(98,3,15,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(99,3,16,'2025-08-13 10:55:59','2025-08-13 10:55:59'),(100,3,18,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(101,3,19,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(102,3,21,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(103,3,22,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(104,3,23,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(105,3,25,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(106,3,26,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(107,3,27,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(108,3,28,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(109,3,30,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(110,3,31,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(111,3,33,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(112,3,34,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(113,3,35,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(114,3,37,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(115,3,38,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(116,3,39,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(117,3,40,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(118,3,42,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(119,3,43,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(120,3,44,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(121,3,45,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(122,4,1,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(123,4,14,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(124,4,15,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(125,4,16,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(126,4,21,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(127,4,26,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(128,4,27,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(129,4,28,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(130,4,38,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(131,4,39,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(132,4,40,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(133,4,42,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(134,4,43,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(135,5,27,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(136,5,28,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(137,5,26,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(138,5,1,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(139,5,21,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(140,5,14,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(141,5,39,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(142,5,40,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(143,5,38,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(144,6,38,'2025-08-13 10:56:00','2025-08-13 10:56:00'),(157,2,48,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(158,2,49,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(159,2,50,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(160,2,51,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(161,2,52,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(162,2,53,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(163,2,54,'2025-08-13 11:28:36','2025-08-13 11:28:36'),(164,2,55,'2025-08-13 11:28:37','2025-08-13 11:28:37'),(165,2,56,'2025-08-13 11:28:37','2025-08-13 11:28:37'),(166,2,57,'2025-08-13 11:28:37','2025-08-13 11:28:37'),(167,2,58,'2025-08-13 11:28:37','2025-08-13 11:28:37'),(168,2,59,'2025-08-13 11:28:37','2025-08-13 11:28:37'),(169,1,66,'2025-08-14 00:25:59','2025-08-14 00:25:59'),(170,1,68,'2025-08-14 00:25:59','2025-08-14 00:25:59'),(171,1,67,'2025-08-14 00:25:59','2025-08-14 00:25:59'),(172,1,65,'2025-08-14 00:25:59','2025-08-14 00:25:59'),(173,1,69,'2025-08-14 01:23:55','2025-08-14 01:23:55');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  KEY `user_roles_assigned_by_foreign` (`assigned_by`),
  KEY `user_roles_expires_at_index` (`expires_at`),
  CONSTRAINT `user_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,1,'2025-08-13 10:56:01',NULL,NULL,NULL,'2025-08-13 10:56:01','2025-08-13 10:56:01');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-03 20:18:16
