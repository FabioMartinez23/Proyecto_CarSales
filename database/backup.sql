-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: car_sales_11_2024
-- ------------------------------------------------------
-- Server version	8.0.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `idbanners` int NOT NULL,
  `URL_banners` varchar(45) NOT NULL,
  `descripcion_banners` varchar(45) NOT NULL,
  `activo_banner` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idbanners`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barrios`
--

DROP TABLE IF EXISTS `barrios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barrios` (
  `idbarrios` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_barrio` tinyint(1) NOT NULL DEFAULT '1',
  `localidades_idlocalidades` int NOT NULL,
  PRIMARY KEY (`idbarrios`),
  KEY `fk_barrios_localidades1_idx` (`localidades_idlocalidades`),
  CONSTRAINT `fk_barrios_localidades1` FOREIGN KEY (`localidades_idlocalidades`) REFERENCES `localidades` (`idlocalidades`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barrios`
--

LOCK TABLES `barrios` WRITE;
/*!40000 ALTER TABLE `barrios` DISABLE KEYS */;
INSERT INTO `barrios` VALUES (1,'San Pedro',1,1),(2,'San Miguel',1,1),(3,'Independencia',1,1),(4,'Villa Lourdes',1,1),(5,'La Floresta',1,1),(6,'Facundo Quiroga',1,1);
/*!40000 ALTER TABLE `barrios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cantidades_cuotas`
--

DROP TABLE IF EXISTS `cantidades_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cantidades_cuotas` (
  `idcantidades_cuotas` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `intereses_idintereses` int NOT NULL,
  PRIMARY KEY (`idcantidades_cuotas`),
  KEY `fk_cantidades_cuotas_intereses1_idx` (`intereses_idintereses`),
  CONSTRAINT `fk_cantidades_cuotas_intereses1` FOREIGN KEY (`intereses_idintereses`) REFERENCES `intereses` (`idintereses`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cantidades_cuotas`
--

LOCK TABLES `cantidades_cuotas` WRITE;
/*!40000 ALTER TABLE `cantidades_cuotas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cantidades_cuotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carroceria`
--

DROP TABLE IF EXISTS `carroceria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carroceria` (
  `idcarroceria` int NOT NULL AUTO_INCREMENT,
  `descripcion_carroceria` varchar(45) DEFAULT NULL,
  `activo_carroceria` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idcarroceria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carroceria`
--

LOCK TABLES `carroceria` WRITE;
/*!40000 ALTER TABLE `carroceria` DISABLE KEYS */;
INSERT INTO `carroceria` VALUES (1,'Excelente',1),(2,'Buena',1),(3,'Regular',1),(4,'Mala',1);
/*!40000 ALTER TABLE `carroceria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colores`
--

DROP TABLE IF EXISTS `colores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colores` (
  `idcolores` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_color` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idcolores`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colores`
--

LOCK TABLES `colores` WRITE;
/*!40000 ALTER TABLE `colores` DISABLE KEYS */;
INSERT INTO `colores` VALUES (1,'Lila',1),(2,'Verde',1),(3,'Azul',1),(4,'Blanco',1),(5,'Negro',1),(6,'Amarillo',1),(7,'Naranja',1),(8,'Rosado',1),(9,'Gris',1),(10,'Violeta',1);
/*!40000 ALTER TABLE `colores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comisiones`
--

DROP TABLE IF EXISTS `comisiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comisiones` (
  `idcomisiones` int NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idcomisiones`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comisiones`
--

LOCK TABLES `comisiones` WRITE;
/*!40000 ALTER TABLE `comisiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `comisiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `idcompras` int NOT NULL AUTO_INCREMENT,
  `descripcIon` varchar(45) DEFAULT NULL,
  `fecha_compra` datetime DEFAULT NULL,
  `tipo_pago_idtipo_pago` int NOT NULL,
  `vehiculo_idvehiculo` int NOT NULL,
  `titular_vehiculo_idtitular_vehiculo` int NOT NULL,
  PRIMARY KEY (`idcompras`),
  KEY `fk_compras_tipo_pago1_idx` (`tipo_pago_idtipo_pago`),
  KEY `fk_compra_idvehiculo_idx` (`vehiculo_idvehiculo`),
  KEY `fk_compras_titular_vehiculo1_idx` (`titular_vehiculo_idtitular_vehiculo`),
  CONSTRAINT `fk_compra_idvehiculos` FOREIGN KEY (`vehiculo_idvehiculo`) REFERENCES `vehiculos` (`idvehiculos`),
  CONSTRAINT `fk_compras_tipo_pago1` FOREIGN KEY (`tipo_pago_idtipo_pago`) REFERENCES `tipo_pago` (`idtipo_pago`),
  CONSTRAINT `fk_compras_titular_vehiculo1` FOREIGN KEY (`titular_vehiculo_idtitular_vehiculo`) REFERENCES `titular_vehiculo` (`idtitular_vehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (1,'Todo perfecto.','2024-11-06 00:00:00',1,1,1),(2,'El buen estado.','2024-11-07 00:00:00',1,2,2);
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactos`
--

DROP TABLE IF EXISTS `contactos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contactos` (
  `idcontactos` int NOT NULL AUTO_INCREMENT,
  `valor` varchar(45) DEFAULT NULL,
  `tipo_contactos_idtipo_contactos` int NOT NULL,
  `Personas_idPersonas` int NOT NULL,
  PRIMARY KEY (`idcontactos`),
  KEY `fk_contactos_tipo_contactos1_idx` (`tipo_contactos_idtipo_contactos`),
  KEY `fk_contactos_Personas1_idx` (`Personas_idPersonas`),
  CONSTRAINT `fk_contactos_Personas1` FOREIGN KEY (`Personas_idPersonas`) REFERENCES `personas` (`idpersonas`),
  CONSTRAINT `fk_contactos_tipo_contactos1` FOREIGN KEY (`tipo_contactos_idtipo_contactos`) REFERENCES `tipo_contacto` (`idtipo_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos`
--

LOCK TABLES `contactos` WRITE;
/*!40000 ALTER TABLE `contactos` DISABLE KEYS */;
INSERT INTO `contactos` VALUES (1,'+54(370)407-31-60',1,1),(2,'3704552233',1,2),(3,'3704447788',1,3),(4,'+54(370)444-77-88',1,4),(5,'3704447788',1,4),(6,'3704778899',1,5),(7,'+54(370)455-22-33',1,6),(8,'+54(370)455-22-33',1,7),(9,'+54(370)462-68-66',1,8),(10,'+54(370)422-33-55',1,9),(11,'+54(370)488-65-53',1,10),(12,'+54(356)467-25-35',1,11),(13,'+54(370)421-16-25',1,12),(14,'+54(370)455-22-33',1,15);
/*!40000 ALTER TABLE `contactos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cristales`
--

DROP TABLE IF EXISTS `cristales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cristales` (
  `idcristales` int NOT NULL AUTO_INCREMENT,
  `descripcion_cristales` varchar(45) DEFAULT NULL,
  `activo_cristal` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idcristales`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cristales`
--

LOCK TABLES `cristales` WRITE;
/*!40000 ALTER TABLE `cristales` DISABLE KEYS */;
INSERT INTO `cristales` VALUES (1,'Sin Rayas',1),(2,'Con Rayas',1),(3,'Rotura',1);
/*!40000 ALTER TABLE `cristales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentaciones`
--

DROP TABLE IF EXISTS `documentaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentaciones` (
  `idDocumentaciones` int NOT NULL AUTO_INCREMENT,
  `URL_descripcion` varchar(255) DEFAULT NULL,
  `vehiculos_idvehiculos` int NOT NULL,
  PRIMARY KEY (`idDocumentaciones`),
  KEY `fk_Documentaciones_vehiculos1_idx` (`vehiculos_idvehiculos`),
  CONSTRAINT `fk_Documentaciones_vehiculos1` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentaciones`
--

LOCK TABLES `documentaciones` WRITE;
/*!40000 ALTER TABLE `documentaciones` DISABLE KEYS */;
INSERT INTO `documentaciones` VALUES (1,'../../uploads/img/672b69efb3afa_clio_mio_blanco_4.jpg',1),(2,'../../uploads/img/672b6ba7e9622_clio_mio_blanco2.jpg',1),(3,'../../uploads/img/672b6ba7eb5f6_clio_mio_blanco3.jpg',1),(4,'../../uploads/img/672cb86675002_clio_mio_3puertas1.jpg',2),(5,'../../uploads/img/672cb8667713c_clio_mio_3puertas2.jpg',2),(6,'../../uploads/img/672cb86678cfa_clio_mio_3puertas3.jpg',2),(7,'../../uploads/img/672ea137a839e_IMG-20241107-WA0033.jpg',1),(8,'../../uploads/doc/6736164b2609b_reporte - 2024-11-14T083805.623.pdf',1);
/*!40000 ALTER TABLE `documentaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `iddocumentos` int NOT NULL AUTO_INCREMENT,
  `valor` int NOT NULL,
  `activo_documento` tinyint(1) NOT NULL DEFAULT '1',
  `Tipo_documento_idTipo_documento` int NOT NULL,
  `Personas_idPersonas` int NOT NULL,
  PRIMARY KEY (`iddocumentos`),
  KEY `fk_documentos_Tipo_documento1_idx` (`Tipo_documento_idTipo_documento`),
  KEY `fk_documentos_Personas1_idx` (`Personas_idPersonas`),
  CONSTRAINT `fk_documentos_Personas1` FOREIGN KEY (`Personas_idPersonas`) REFERENCES `personas` (`idpersonas`),
  CONSTRAINT `fk_documentos_Tipo_documento1` FOREIGN KEY (`Tipo_documento_idTipo_documento`) REFERENCES `tipo_documento` (`idTipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
INSERT INTO `documentos` VALUES (1,38096227,1,1,1),(2,31235654,1,1,2),(3,39653214,1,1,3),(4,39653214,1,1,4),(5,39653214,1,1,4),(6,16456879,1,1,5),(7,42536523,1,1,7),(8,16716600,1,1,8),(9,42563546,1,1,9),(10,40256356,1,1,10),(11,38652355,1,1,11),(12,39132114,1,1,12),(13,38523562,1,1,15);
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domicilios`
--

DROP TABLE IF EXISTS `domicilios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domicilios` (
  `iddomicilios` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `activo_domicilio` tinyint(1) NOT NULL DEFAULT '1',
  `Personas_idPersonas` int NOT NULL,
  `barrios_idbarrios` int DEFAULT NULL,
  `tipo_domicilio_idtipo_domicilio` int NOT NULL,
  PRIMARY KEY (`iddomicilios`),
  KEY `fk_domicilios_Personas1_idx` (`Personas_idPersonas`),
  KEY `fk_domicilios_barrios1_idx` (`barrios_idbarrios`),
  KEY `fk_domicilios_tipo_domicilio1_idx` (`tipo_domicilio_idtipo_domicilio`),
  CONSTRAINT `fk_domicilios_barrios1` FOREIGN KEY (`barrios_idbarrios`) REFERENCES `barrios` (`idbarrios`),
  CONSTRAINT `fk_domicilios_Personas1` FOREIGN KEY (`Personas_idPersonas`) REFERENCES `personas` (`idpersonas`),
  CONSTRAINT `fk_domicilios_tipo_domicilio1` FOREIGN KEY (`tipo_domicilio_idtipo_domicilio`) REFERENCES `tipo_domicilio` (`idtipo_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domicilios`
--

LOCK TABLES `domicilios` WRITE;
/*!40000 ALTER TABLE `domicilios` DISABLE KEYS */;
INSERT INTO `domicilios` VALUES (1,'Arenales 1815',1,1,1,1),(2,'Arenales 2255',1,2,2,1),(3,'Moreno 425',1,3,3,1),(4,'Moreno 425',1,4,1,1),(5,'Moreno 425',1,4,3,1),(6,'Francisco Bosh 225',1,5,4,1),(7,'Arenales 1815',1,7,1,1),(8,'Arenales 1815',1,8,1,1),(9,'Facundo Quiroga Mz 10 Casa 99',1,9,1,1),(10,'Mz 77 Casa 15',1,10,2,1),(11,'Av. Independencia 2535',1,11,3,1),(12,'Arenales 1815',1,12,1,1),(13,'Blas Parera 1253',1,15,4,1);
/*!40000 ALTER TABLE `domicilios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `idempleados` int NOT NULL AUTO_INCREMENT,
  `legajo` varchar(45) DEFAULT NULL,
  `tipo_de_puestos_idtipo_de_puestos` int NOT NULL,
  `Usuarios_idUsuarios` int NOT NULL,
  PRIMARY KEY (`idempleados`),
  KEY `fk_empleados_tipo_de_puestos1_idx` (`tipo_de_puestos_idtipo_de_puestos`),
  KEY `fk_empleados_Usuarios1_idx` (`Usuarios_idUsuarios`),
  CONSTRAINT `fk_empleados_tipo_de_puestos1` FOREIGN KEY (`tipo_de_puestos_idtipo_de_puestos`) REFERENCES `tipo_de_puestos` (`idtipo_de_puestos`),
  CONSTRAINT `fk_empleados_Usuarios1` FOREIGN KEY (`Usuarios_idUsuarios`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_vehiculo`
--

DROP TABLE IF EXISTS `estado_vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_vehiculo` (
  `idestado_vehiculo` int NOT NULL AUTO_INCREMENT,
  `estado_vehiculo` varchar(45) DEFAULT NULL,
  `activo_estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idestado_vehiculo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_vehiculo`
--

LOCK TABLES `estado_vehiculo` WRITE;
/*!40000 ALTER TABLE `estado_vehiculo` DISABLE KEYS */;
/*!40000 ALTER TABLE `estado_vehiculo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ficha_tecnica`
--

DROP TABLE IF EXISTS `ficha_tecnica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ficha_tecnica` (
  `idficha_tecnica` int NOT NULL AUTO_INCREMENT,
  `vencimiento_bateria` date NOT NULL,
  `vencimiento_service` date NOT NULL,
  `vencimiento_RTO` date NOT NULL,
  `form_08` tinyint(1) NOT NULL,
  `form_12` tinyint(1) NOT NULL,
  `titulo_vehiculo` tinyint(1) NOT NULL,
  `seguro` tinyint(1) NOT NULL,
  `municipalidad` tinyint(1) NOT NULL,
  `cedula_vehiculo` tinyint(1) NOT NULL,
  `form_13i` tinyint(1) NOT NULL,
  `informe_dominio` tinyint(1) NOT NULL,
  `prenda` tinyint(1) DEFAULT '0',
  `vehiculos_idvehiculos` int NOT NULL,
  `carroceria_idcarroceria` int NOT NULL,
  `cristales_idcristales` int NOT NULL,
  `neumaticos_idneumaticos` int NOT NULL,
  PRIMARY KEY (`idficha_tecnica`),
  KEY `fk_ficha_tecnica_vehiculos1_idx` (`vehiculos_idvehiculos`),
  KEY `fk_ficha_tecnica_carroceria1_idx` (`carroceria_idcarroceria`),
  KEY `fk_ficha_tecnica_cristales1_idx` (`cristales_idcristales`),
  KEY `fk_ficha_tecnica_neumaticos1_idx` (`neumaticos_idneumaticos`),
  CONSTRAINT `fk_ficha_tecnica_carroceria1` FOREIGN KEY (`carroceria_idcarroceria`) REFERENCES `carroceria` (`idcarroceria`),
  CONSTRAINT `fk_ficha_tecnica_cristales1` FOREIGN KEY (`cristales_idcristales`) REFERENCES `cristales` (`idcristales`),
  CONSTRAINT `fk_ficha_tecnica_neumaticos1` FOREIGN KEY (`neumaticos_idneumaticos`) REFERENCES `neumaticos` (`idneumaticos`),
  CONSTRAINT `fk_ficha_tecnica_vehiculos1` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ficha_tecnica`
--

LOCK TABLES `ficha_tecnica` WRITE;
/*!40000 ALTER TABLE `ficha_tecnica` DISABLE KEYS */;
INSERT INTO `ficha_tecnica` VALUES (1,'2022-01-12','2024-05-12','2022-01-12',1,1,1,0,0,1,0,0,0,1,2,1,2),(2,'2023-02-12','2024-02-01','2024-05-12',1,1,1,0,1,1,1,1,0,2,2,1,3);
/*!40000 ALTER TABLE `ficha_tecnica` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financiamientos`
--

DROP TABLE IF EXISTS `financiamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financiamientos` (
  `idfinanciamientos` int NOT NULL AUTO_INCREMENT,
  `estado` varchar(45) DEFAULT NULL,
  `fecha_financiamiento` varchar(45) DEFAULT NULL,
  `vehiculos_idvehiculos` int NOT NULL,
  `cantidades_cuotas_idcantidades_cuotas` int NOT NULL,
  PRIMARY KEY (`idfinanciamientos`),
  KEY `fk_financiamientos_vehiculos1_idx` (`vehiculos_idvehiculos`),
  KEY `fk_financiamientos_cantidades_cuotas1_idx` (`cantidades_cuotas_idcantidades_cuotas`),
  CONSTRAINT `fk_financiamientos_cantidades_cuotas1` FOREIGN KEY (`cantidades_cuotas_idcantidades_cuotas`) REFERENCES `cantidades_cuotas` (`idcantidades_cuotas`),
  CONSTRAINT `fk_financiamientos_vehiculos1` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financiamientos`
--

LOCK TABLES `financiamientos` WRITE;
/*!40000 ALTER TABLE `financiamientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `financiamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intereses`
--

DROP TABLE IF EXISTS `intereses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intereses` (
  `idintereses` int NOT NULL AUTO_INCREMENT,
  `valor_interes` int DEFAULT NULL,
  `activo_interes` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idintereses`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intereses`
--

LOCK TABLES `intereses` WRITE;
/*!40000 ALTER TABLE `intereses` DISABLE KEYS */;
/*!40000 ALTER TABLE `intereses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `localidades`
--

DROP TABLE IF EXISTS `localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `localidades` (
  `idlocalidades` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_localidad` tinyint(1) NOT NULL DEFAULT '1',
  `provincias_idprovincias` int NOT NULL,
  PRIMARY KEY (`idlocalidades`),
  KEY `fk_localidades_provincias1_idx` (`provincias_idprovincias`),
  CONSTRAINT `fk_localidades_provincias1` FOREIGN KEY (`provincias_idprovincias`) REFERENCES `provincias` (`idprovincias`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localidades`
--

LOCK TABLES `localidades` WRITE;
/*!40000 ALTER TABLE `localidades` DISABLE KEYS */;
INSERT INTO `localidades` VALUES (1,'Formosa',1,1),(2,'Clorinda',1,1),(3,'Pirané',1,1),(4,'Mision Laishi',1,1),(5,'Matacos',1,1),(6,'Ramon Lista',1,1);
/*!40000 ALTER TABLE `localidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `idmarcas` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `activo_marca` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idmarcas`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Renault',1),(2,'Toyota',1),(3,'Hyundai',1),(4,'Volkswagen',1),(5,'Chevrolet',1),(6,'Nissan',1),(7,'Fiat',1),(8,'Ford',1),(9,'Audi',1),(10,'BMW',1),(11,'Honda',1),(12,'Cadillac',1),(13,'Citroen',1),(14,'Ferrari',1),(15,'Alfa Romero',1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modelos`
--

DROP TABLE IF EXISTS `modelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modelos` (
  `idmodelos` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `activo_modelo` tinyint(1) NOT NULL DEFAULT '1',
  `marcas_idmarcas` int NOT NULL,
  PRIMARY KEY (`idmodelos`),
  KEY `fk_modelos_marcas1_idx` (`marcas_idmarcas`),
  CONSTRAINT `fk_modelos_marcas1` FOREIGN KEY (`marcas_idmarcas`) REFERENCES `marcas` (`idmarcas`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelos`
--

LOCK TABLES `modelos` WRITE;
/*!40000 ALTER TABLE `modelos` DISABLE KEYS */;
INSERT INTO `modelos` VALUES (1,'Clio Mio 1.2 CONFORT',1,1),(2,'Clio Mio 1.2 EXPRESSION',1,1),(3,'Gol 1.6 COMFORNTLINE',1,4),(4,'Gol 1.6 TREND',1,4),(5,'Onix 1.2',1,5),(6,'Onix 1.2 LT',1,5),(7,'Alaskan 2.3 TDI 4x4',1,1),(8,'Captur 1.6 BOSE CVT',1,1),(9,'Captur 1.6 LIFE',1,1),(10,'86 GT',1,2),(11,'Corolla 1.6 SE/G',1,2),(12,'CAMRY 3.5 AUT',0,2),(13,'AGILE 1.4 LT',1,5),(14,'CAMARO L/17',1,5),(15,'AVEO 1.6 LT AUT',1,5),(16,'Classic 1.6 LS',1,5),(17,'Classic 1.6 LTZ',1,5),(18,'Kangoo II Zen 1.6 SCe',1,1),(19,'Sandero Life 1.6',1,1),(20,'Stepway Intens 1.6',1,1),(21,'Duster Intens 1.6 MT',1,1),(22,'Koleos PH2 Intens 4WD CVT',1,1),(23,'Kardian Evolution 156 MT',1,1),(24,'Kwid E-TECH',1,1),(25,'Megane E-TECH',1,1),(26,'Kangoo E-TECH',1,1),(27,'Kangoo Express 2A 1.6 SCe',1,1),(28,'Yaris Hatchback',1,2),(29,'Yaris Sedan',1,2),(30,'Corolla Hybrid',1,2),(31,'Corolla GR-Sport',1,2),(32,'Hilux Chasis Cabina',1,2),(33,'Hilux GR-Sport IV',1,2),(34,'Corolla Cross',1,2),(35,'Corolla Cross Hybrid',1,2),(36,'Staria',1,3),(37,'Tucson',1,3),(38,'Creta',1,3),(39,'Veloster N',1,3),(40,'HD-78',1,3),(41,'Polo',1,4),(42,'Virtus',1,4),(43,'T-Cross',1,4),(44,'Taos',1,4),(45,'Nuevo Tiguan Allspace',1,4),(46,'Vento',1,4),(47,'Saveiro',1,4),(48,'Amarok',1,4),(49,'Onix Plus',1,5),(50,'Cruze RS',1,5),(51,'Cruze ',1,5),(52,'Cruze 5',1,5),(53,'Joy',1,5),(54,'Joy Plus',1,5),(55,'Frontier',1,6),(56,'X-Trail E-Power',1,6),(57,'Kicks',1,6),(58,'Sentra',1,6),(59,'Versa',1,6),(60,'Leaf',1,6),(61,'Pulse',1,7),(62,'Cronos',1,7),(63,'Argo',1,7),(64,'Toro',1,7),(65,'Strada',1,7),(66,'Mobi',1,7),(67,'Uno',1,7),(68,'500',1,7),(69,'Maverick',1,8),(70,'Nueva Ranger',1,8),(71,'Ranger Raptor',1,8),(72,'Nueva F-150 Lariat',1,8),(73,'Nueva F-150 Tremor',1,8),(74,'Nueva F-150 Raptor',1,8);
/*!40000 ALTER TABLE `modelos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos` (
  `idmodulos` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_modulo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idmodulos`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,'inicio',1),(2,'bienvenida',1),(3,'login',1),(4,'contacto',1),(5,'nosotros',1),(6,'comprar_vehiculo',1),(7,'form_contacto',1),(8,'registrarse',1),(9,'simulador',1),(10,'listado_usuarios',1),(11,'listado_modulos',1),(12,'listado_vehiculos',1),(13,'cambiar_password',1),(14,'form_tablas_maestras',1),(15,'salida',1),(16,'form_mis_datos',1),(17,'listado_clientes',1),(18,'listado_empleados',1),(19,'registrar_vehiculos',1),(20,'ficha_tecnica',1),(21,'listado_ficha_tecnica',1),(22,'registrar_clientes',1),(23,'registrar_empleados',1),(24,'listado_ventas',1),(25,'listado_compras',1),(26,'registrar_ventas',1),(27,'detalle_ventas',1),(28,'form_financiamiento',1),(29,'listado_precios',1),(30,'listado_stock',1),(31,'gestion_stock',1),(32,'registrar_compras',1),(33,'detalle_compras',1),(34,'olvidar_contraseña',1),(36,'recuperar_password',1),(37,'ver_usuario',1),(38,'estadisticas',1),(39,'reportes',1),(40,'graficos',1),(41,'reportes_cv',1);
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `neumaticos`
--

DROP TABLE IF EXISTS `neumaticos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `neumaticos` (
  `idneumaticos` int NOT NULL AUTO_INCREMENT,
  `descripcion_neumaticos` varchar(45) DEFAULT NULL,
  `activo_neumatico` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idneumaticos`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `neumaticos`
--

LOCK TABLES `neumaticos` WRITE;
/*!40000 ALTER TABLE `neumaticos` DISABLE KEYS */;
INSERT INTO `neumaticos` VALUES (1,'Nuevos',1),(2,'Buen Estado',1),(3,'Desgaste Medio',1),(4,'Desgastados',1);
/*!40000 ALTER TABLE `neumaticos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paises` (
  `idpaises` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_pais` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idpaises`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Argentina',1),(2,'Paraguay',1),(3,'Uruguay',1),(4,'Brasil',1),(5,'Bolivia',1),(6,'Chile',1),(7,'Perú',1);
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfiles` (
  `idperfiles` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_perfil` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idperfiles`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfiles`
--

LOCK TABLES `perfiles` WRITE;
/*!40000 ALTER TABLE `perfiles` DISABLE KEYS */;
INSERT INTO `perfiles` VALUES (1,'Administrador',1),(2,'Empleado',1),(3,'Cliente',1);
/*!40000 ALTER TABLE `perfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfiles_modulos`
--

DROP TABLE IF EXISTS `perfiles_modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perfiles_modulos` (
  `idperfiles_modulos` int NOT NULL AUTO_INCREMENT,
  `perfiles_idperfiles` int NOT NULL,
  `modulos_idmodulos` int NOT NULL,
  PRIMARY KEY (`idperfiles_modulos`),
  KEY `fk_perfiles_modulos_perfiles1_idx` (`perfiles_idperfiles`),
  KEY `fk_perfiles_modulos_modulos1_idx` (`modulos_idmodulos`),
  CONSTRAINT `fk_perfiles_modulos_modulos1` FOREIGN KEY (`modulos_idmodulos`) REFERENCES `modulos` (`idmodulos`),
  CONSTRAINT `fk_perfiles_modulos_perfiles1` FOREIGN KEY (`perfiles_idperfiles`) REFERENCES `perfiles` (`idperfiles`)
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfiles_modulos`
--

LOCK TABLES `perfiles_modulos` WRITE;
/*!40000 ALTER TABLE `perfiles_modulos` DISABLE KEYS */;
INSERT INTO `perfiles_modulos` VALUES (486,2,1),(487,2,2),(488,2,3),(489,2,4),(490,2,5),(491,2,6),(492,2,8),(493,2,9),(494,2,12),(495,2,13),(496,2,15),(497,2,16),(498,2,17),(499,2,19),(500,2,22),(501,2,24),(502,2,25),(503,2,26),(504,2,27),(505,2,28),(506,2,29),(507,2,32),(508,2,33),(509,2,34),(510,2,36),(511,3,1),(512,3,2),(513,3,3),(514,3,4),(515,3,5),(516,3,6),(517,3,7),(518,3,8),(519,3,9),(520,3,13),(521,3,15),(522,3,16),(523,3,34),(524,3,36),(675,1,1),(676,1,2),(677,1,3),(678,1,4),(679,1,5),(680,1,6),(681,1,7),(682,1,8),(683,1,9),(684,1,10),(685,1,11),(686,1,12),(687,1,13),(688,1,14),(689,1,15),(690,1,16),(691,1,17),(692,1,18),(693,1,19),(694,1,20),(695,1,21),(696,1,22),(697,1,23),(698,1,24),(699,1,25),(700,1,26),(701,1,27),(702,1,28),(703,1,29),(704,1,30),(705,1,31),(706,1,32),(707,1,33),(708,1,34),(709,1,36),(710,1,37),(711,1,38),(712,1,39),(713,1,40),(714,1,41);
/*!40000 ALTER TABLE `perfiles_modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personas`
--

DROP TABLE IF EXISTS `personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personas` (
  `idpersonas` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `fecha_nacimiento` varchar(45) NOT NULL,
  `tipo_sexo_idtipo_sexo` int NOT NULL,
  PRIMARY KEY (`idpersonas`),
  KEY `fk_Personas_tipo_sexo1_idx` (`tipo_sexo_idtipo_sexo`),
  CONSTRAINT `fk_Personas_tipo_sexo1` FOREIGN KEY (`tipo_sexo_idtipo_sexo`) REFERENCES `tipo_sexo` (`idtipo_sexo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personas`
--

LOCK TABLES `personas` WRITE;
/*!40000 ALTER TABLE `personas` DISABLE KEYS */;
INSERT INTO `personas` VALUES (1,'Fabio','Martinez','1994-01-26',1),(2,'Juan','Perez','1990-02-01',1),(3,'Leila','Tomas','1996-12-12',2),(4,'Leila Sol','Tomas','1996-12-12',2),(5,'Susana','Gil','1963-02-22',2),(6,'Juan','Zunino','2006-03-20',1),(7,'Juan','Zunino','2006-03-20',1),(8,'Marcelo','Zunino','1965-11-30',1),(9,'Juan','Zunino','2006-03-20',1),(10,'Belen','Portillo','1997-05-29',2),(11,'Alan','Beck','1994-02-02',1),(12,'Marilyn','Acosta','1995-06-02',2),(13,'Marcelo','Zunino','1965-11-30',1),(14,'Juan','Zunino','2006-11-30',1),(15,'Belen','Portillo','1996-05-22',2);
/*!40000 ALTER TABLE `personas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `precios_vehiculos`
--

DROP TABLE IF EXISTS `precios_vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `precios_vehiculos` (
  `idprecios_vehiculos` int NOT NULL AUTO_INCREMENT,
  `precio` varchar(45) DEFAULT NULL,
  `fecha_precio` varchar(45) DEFAULT NULL,
  `vehiculos_idvehiculos` int NOT NULL,
  PRIMARY KEY (`idprecios_vehiculos`),
  KEY `fk_precios_vehiculos_vehiculos1_idx` (`vehiculos_idvehiculos`),
  CONSTRAINT `fk_precios_vehiculos_vehiculos1` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `precios_vehiculos`
--

LOCK TABLES `precios_vehiculos` WRITE;
/*!40000 ALTER TABLE `precios_vehiculos` DISABLE KEYS */;
INSERT INTO `precios_vehiculos` VALUES (1,'12.000.000','2024-11-06 10:02:11',1),(2,'12.000.000','2024-11-07 09:52:01',2),(3,'15.000.000','2024-11-14 20:10:14',1),(4,'13.000.000','2024-11-14 20:12:09',2);
/*!40000 ALTER TABLE `precios_vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provincias`
--

DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provincias` (
  `idprovincias` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_provincia` tinyint(1) NOT NULL DEFAULT '1',
  `paises_idpaises` int NOT NULL,
  PRIMARY KEY (`idprovincias`),
  KEY `fk_provincias_paises1_idx` (`paises_idpaises`),
  CONSTRAINT `fk_provincias_paises1` FOREIGN KEY (`paises_idpaises`) REFERENCES `paises` (`idpaises`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provincias`
--

LOCK TABLES `provincias` WRITE;
/*!40000 ALTER TABLE `provincias` DISABLE KEYS */;
INSERT INTO `provincias` VALUES (1,'Formosa',1,1),(2,'Chaco',1,1),(3,'Corrientes',1,1),(4,'Misiones',1,1),(5,'Santa Fe',1,1),(6,'Salta',1,1);
/*!40000 ALTER TABLE `provincias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_clientes`
--

DROP TABLE IF EXISTS `registro_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_clientes` (
  `idregistro_clientes` int NOT NULL AUTO_INCREMENT,
  `Usuarios_idusuarios` int NOT NULL,
  PRIMARY KEY (`idregistro_clientes`),
  KEY `fk_registro_clientes_Usuarios1_idx` (`Usuarios_idusuarios`),
  CONSTRAINT `fk_registro_clientes_Usuarios1` FOREIGN KEY (`Usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_clientes`
--

LOCK TABLES `registro_clientes` WRITE;
/*!40000 ALTER TABLE `registro_clientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `registro_clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reporte_consulta_vehiculo`
--

DROP TABLE IF EXISTS `reporte_consulta_vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reporte_consulta_vehiculo` (
  `idreporte_consulta_vehiculo` int NOT NULL AUTO_INCREMENT,
  `clicks` json NOT NULL,
  `fecha_consulta` date DEFAULT NULL,
  `vehiculos_idvehiculos` int NOT NULL,
  PRIMARY KEY (`idreporte_consulta_vehiculo`),
  KEY `vehiculos_idvehiculos_idx` (`vehiculos_idvehiculos`),
  CONSTRAINT `vehiculos_idvehiculos` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reporte_consulta_vehiculo`
--

LOCK TABLES `reporte_consulta_vehiculo` WRITE;
/*!40000 ALTER TABLE `reporte_consulta_vehiculo` DISABLE KEYS */;
INSERT INTO `reporte_consulta_vehiculo` VALUES (2,'[1, 1, 1, 1, 1, 1, 1, 1]','2024-11-15',1),(3,'[1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]','2024-11-15',2),(4,'[1, 1, 1]','2024-11-25',1),(5,'[1, 1]','2025-03-29',1),(6,'[1, 1]','2025-03-29',2),(7,'[1, 1]','2025-04-02',1);
/*!40000 ALTER TABLE `reporte_consulta_vehiculo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signos`
--

DROP TABLE IF EXISTS `signos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signos` (
  `idsignos` int NOT NULL,
  `signo` varchar(45) DEFAULT NULL,
  `activo_signo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idsignos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signos`
--

LOCK TABLES `signos` WRITE;
/*!40000 ALTER TABLE `signos` DISABLE KEYS */;
/*!40000 ALTER TABLE `signos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `simulaciones`
--

DROP TABLE IF EXISTS `simulaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `simulaciones` (
  `idsimulaciones` int NOT NULL AUTO_INCREMENT,
  `fecha_simulacion` datetime NOT NULL,
  `resultado` int NOT NULL,
  `cantidades_cuotas_idcantidades_cuotas` int NOT NULL,
  `vehiculos_idvehiculos` int NOT NULL,
  PRIMARY KEY (`idsimulaciones`,`cantidades_cuotas_idcantidades_cuotas`),
  KEY `fk_simulaciones_compra_cantidades_cuotas1_idx` (`cantidades_cuotas_idcantidades_cuotas`),
  KEY `fk_simulaciones_venta_vehiculos1_idx` (`vehiculos_idvehiculos`),
  CONSTRAINT `fk_simulaciones_compra_cantidades_cuotas1` FOREIGN KEY (`cantidades_cuotas_idcantidades_cuotas`) REFERENCES `cantidades_cuotas` (`idcantidades_cuotas`),
  CONSTRAINT `fk_simulaciones_venta_vehiculos1` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `simulaciones`
--

LOCK TABLES `simulaciones` WRITE;
/*!40000 ALTER TABLE `simulaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `simulaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_contacto`
--

DROP TABLE IF EXISTS `tipo_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_contacto` (
  `idtipo_contacto` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_contacto`
--

LOCK TABLES `tipo_contacto` WRITE;
/*!40000 ALTER TABLE `tipo_contacto` DISABLE KEYS */;
INSERT INTO `tipo_contacto` VALUES (1,'Celular',1),(2,'Telefono Fijo',1);
/*!40000 ALTER TABLE `tipo_contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_de_puestos`
--

DROP TABLE IF EXISTS `tipo_de_puestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_de_puestos` (
  `idtipo_de_puestos` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_de_puestos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_de_puestos`
--

LOCK TABLES `tipo_de_puestos` WRITE;
/*!40000 ALTER TABLE `tipo_de_puestos` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipo_de_puestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_documento`
--

DROP TABLE IF EXISTS `tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_documento` (
  `idTipo_documento` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idTipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_documento`
--

LOCK TABLES `tipo_documento` WRITE;
/*!40000 ALTER TABLE `tipo_documento` DISABLE KEYS */;
INSERT INTO `tipo_documento` VALUES (1,'DNI',1);
/*!40000 ALTER TABLE `tipo_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_domicilio`
--

DROP TABLE IF EXISTS `tipo_domicilio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_domicilio` (
  `idtipo_domicilio` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_domicilio`
--

LOCK TABLES `tipo_domicilio` WRITE;
/*!40000 ALTER TABLE `tipo_domicilio` DISABLE KEYS */;
INSERT INTO `tipo_domicilio` VALUES (1,'Real',1),(2,'Legal',1),(3,'Laboral',1);
/*!40000 ALTER TABLE `tipo_domicilio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_pago`
--

DROP TABLE IF EXISTS `tipo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_pago` (
  `idtipo_pago` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_pago`
--

LOCK TABLES `tipo_pago` WRITE;
/*!40000 ALTER TABLE `tipo_pago` DISABLE KEYS */;
INSERT INTO `tipo_pago` VALUES (1,'Efectivo',1),(2,'Transferencia',1),(3,'Crédito Bancario',1);
/*!40000 ALTER TABLE `tipo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_sexo`
--

DROP TABLE IF EXISTS `tipo_sexo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_sexo` (
  `idtipo_sexo` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) NOT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_sexo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_sexo`
--

LOCK TABLES `tipo_sexo` WRITE;
/*!40000 ALTER TABLE `tipo_sexo` DISABLE KEYS */;
INSERT INTO `tipo_sexo` VALUES (1,'Masculino',1),(2,'Femenino',1);
/*!40000 ALTER TABLE `tipo_sexo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_vehiculos`
--

DROP TABLE IF EXISTS `tipo_vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_vehiculos` (
  `idtipo_vehiculos` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idtipo_vehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_vehiculos`
--

LOCK TABLES `tipo_vehiculos` WRITE;
/*!40000 ALTER TABLE `tipo_vehiculos` DISABLE KEYS */;
INSERT INTO `tipo_vehiculos` VALUES (1,'Sedan 3 Puertas',1),(2,'Sedan 4 Puertas',1),(3,'Sedan 5 Puertas',1),(4,'Rural',1),(5,'Pick-up',1),(6,'Camion',1),(7,'Furgon',1),(8,'Pick-Up Cupula',1);
/*!40000 ALTER TABLE `tipo_vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_venta`
--

DROP TABLE IF EXISTS `tipo_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_venta` (
  `idtipo_venta` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `activo_tipo` tinyint(1) NOT NULL DEFAULT '1',
  `comisiones_idcomisiones` int NOT NULL,
  PRIMARY KEY (`idtipo_venta`),
  KEY `fk_tipo_venta_comisiones1_idx` (`comisiones_idcomisiones`),
  CONSTRAINT `fk_tipo_venta_comisiones1` FOREIGN KEY (`comisiones_idcomisiones`) REFERENCES `comisiones` (`idcomisiones`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_venta`
--

LOCK TABLES `tipo_venta` WRITE;
/*!40000 ALTER TABLE `tipo_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipo_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `titular_vehiculo`
--

DROP TABLE IF EXISTS `titular_vehiculo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `titular_vehiculo` (
  `idtitular_vehiculo` int NOT NULL AUTO_INCREMENT,
  `vehiculos_idvehiculos` int NOT NULL,
  `Personas_idpersonas` int NOT NULL,
  PRIMARY KEY (`idtitular_vehiculo`),
  KEY `fk_titular_vehiculo_vehiculos2_idx` (`vehiculos_idvehiculos`),
  KEY `fk_titular_vehiculo_Personas1_idx` (`Personas_idpersonas`),
  CONSTRAINT `fk_titular_vehiculo_Personas1` FOREIGN KEY (`Personas_idpersonas`) REFERENCES `personas` (`idpersonas`),
  CONSTRAINT `fk_titular_vehiculo_vehiculos2` FOREIGN KEY (`vehiculos_idvehiculos`) REFERENCES `vehiculos` (`idvehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `titular_vehiculo`
--

LOCK TABLES `titular_vehiculo` WRITE;
/*!40000 ALTER TABLE `titular_vehiculo` DISABLE KEYS */;
INSERT INTO `titular_vehiculo` VALUES (1,1,2),(2,2,5);
/*!40000 ALTER TABLE `titular_vehiculo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens_recuperacion`
--

DROP TABLE IF EXISTS `tokens_recuperacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens_recuperacion` (
  `idtokens_recuperacion` int NOT NULL AUTO_INCREMENT,
  `token` varchar(255) DEFAULT NULL,
  `fecha_expiracion` datetime DEFAULT NULL,
  `Usuarios_idusuarios` int NOT NULL,
  PRIMARY KEY (`idtokens_recuperacion`),
  KEY `fk_tokens_recuperarcion_Usuarios1_idx` (`Usuarios_idusuarios`),
  CONSTRAINT `fk_tokens_recuperarcion_Usuarios1` FOREIGN KEY (`Usuarios_idusuarios`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens_recuperacion`
--

LOCK TABLES `tokens_recuperacion` WRITE;
/*!40000 ALTER TABLE `tokens_recuperacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens_recuperacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `idusuarios` int NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(150) NOT NULL,
  `email` varchar(60) NOT NULL,
  `fecha_alta` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `activo_usuario` tinyint(1) NOT NULL DEFAULT '1',
  `perfiles_idperfiles` int NOT NULL,
  `personas_idpersonas` int DEFAULT NULL,
  PRIMARY KEY (`idusuarios`),
  KEY `fk_Usuarios_perfiles1_idx` (`perfiles_idperfiles`),
  KEY `fk_Usuarios_Personas1_idx` (`personas_idpersonas`),
  CONSTRAINT `fk_Usuarios_perfiles1` FOREIGN KEY (`perfiles_idperfiles`) REFERENCES `perfiles` (`idperfiles`),
  CONSTRAINT `fk_Usuarios_Personas1` FOREIGN KEY (`personas_idpersonas`) REFERENCES `personas` (`idpersonas`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'famartinez','$2y$10$gVm.8dCEhKPpSnwb6BIf8.u.a1PK93qFivK1DF6hoaQval19fm.QC','famartinez2611994@gmail.com','2024-11-06',NULL,1,1,1),(3,'leilatomas','$2y$10$Vx5ifjkZASKFdOGpcPyDPugMuJ/bpxpikpG0LrT5TGpnoUKEqCHLu','leilatomas@gmail.com','2024-11-06','2024-11-06',1,3,4),(4,'juanzunino','$2y$10$NfKP2Yb0CYZceCCDkTCpze1ESwBeB2I8RMx/Sxh6WrS9QcRhT44wW','juanzunino@gmail.com','2024-11-08',NULL,1,2,7),(5,'marcelozunino','$2y$10$Fs/hQCHAkofKzJYF9PmO0.mpKWIXZjLDQemVQWW.hLw6aooGxlFoW','marcelozunino@gmail.com','2024-11-11',NULL,1,3,8),(6,'juanboca','$2y$10$c7WdxqP8hstKc7aKq9A5QuqJeyiyYTSfRfMZS4TEj82.NLt3W3ypi','juanboca@gmail.com','2024-11-11',NULL,1,2,9),(7,'belportillo','$2y$10$ZDHwd7G/ffQ62ZqqGEvM/OC5Rhv9B10CXy4jSB3A8ytOB4fJQIPCa','belportillo@gmail.com','2024-11-11',NULL,1,3,10),(8,'alanbeck','$2y$10$M3jrT8bfkxKvlRfBs0zWAeOtiDikJg4P/U/S.5BGIXCsWBtLjsVoe','alanbeck@hotmail.com','2024-11-14',NULL,1,3,11),(9,'acostamarilyn','$2y$10$QaZOdLs6fX/3WmN7zp/VbOeBalO0xaWNb/hKJ338f4RBVOkacm3.C','marilynpatricia02@gmail.com','2024-11-28',NULL,1,1,12),(10,'marcelo22','$2y$10$DFGRBlcmlMuOw8cMssvJz.c9wOQRD28OW1PcI1NpmJBTTgJy1g.QG','marcelozunino@gmail.com','2025-03-29',NULL,1,2,13),(11,'marcelo22','$2y$10$QQgNyxa0PCfZPBomwt8DfujntOGTM/oGAkL1NUmyAYeF467OvYjjC','marcelozunino@gmail.com','2025-03-29',NULL,1,2,14),(12,'BelenP','$2y$10$emN0ZoRke4yuC6bQkBbWsuwXOml1ZXaUTZyRsWVRqGF9nPVE/V2Y6','belenportillo@gmail.com','2025-04-24',NULL,1,3,15);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_has_banners`
--

DROP TABLE IF EXISTS `usuarios_has_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_has_banners` (
  `Usuarios_idUsuarios` int NOT NULL,
  `banners_idbanners` int NOT NULL,
  PRIMARY KEY (`Usuarios_idUsuarios`,`banners_idbanners`),
  KEY `fk_Usuarios_has_banners_banners1_idx` (`banners_idbanners`),
  KEY `fk_Usuarios_has_banners_Usuarios1_idx` (`Usuarios_idUsuarios`),
  CONSTRAINT `fk_Usuarios_has_banners_banners1` FOREIGN KEY (`banners_idbanners`) REFERENCES `banners` (`idbanners`),
  CONSTRAINT `fk_Usuarios_has_banners_Usuarios1` FOREIGN KEY (`Usuarios_idUsuarios`) REFERENCES `usuarios` (`idusuarios`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_has_banners`
--

LOCK TABLES `usuarios_has_banners` WRITE;
/*!40000 ALTER TABLE `usuarios_has_banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios_has_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `idvehiculos` int NOT NULL AUTO_INCREMENT,
  `patente` varchar(45) NOT NULL,
  `chasis` varchar(45) NOT NULL,
  `motor` varchar(45) NOT NULL,
  `anio` int NOT NULL,
  `kilometraje` varchar(45) NOT NULL,
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_baja` datetime DEFAULT NULL,
  `activo_vehiculo` tinyint(1) NOT NULL DEFAULT '1',
  `modelos_idmodelos` int NOT NULL,
  `colores_idcolores` int NOT NULL,
  `tipo_vehiculos_idtipo_vehiculos` int NOT NULL,
  `estado_vehiculo_idestado_vehiculo` int DEFAULT NULL,
  PRIMARY KEY (`idvehiculos`),
  KEY `fk_vehiculos_modelos1_idx` (`modelos_idmodelos`),
  KEY `fk_vehiculos_colores1_idx` (`colores_idcolores`),
  KEY `fk_vehiculos_tipo_vehiculos1_idx` (`tipo_vehiculos_idtipo_vehiculos`),
  KEY `fk_vehiculos_estado_vehiculo1_idx` (`estado_vehiculo_idestado_vehiculo`),
  CONSTRAINT `fk_vehiculos_colores1` FOREIGN KEY (`colores_idcolores`) REFERENCES `colores` (`idcolores`),
  CONSTRAINT `fk_vehiculos_estado_vehiculo1` FOREIGN KEY (`estado_vehiculo_idestado_vehiculo`) REFERENCES `estado_vehiculo` (`idestado_vehiculo`),
  CONSTRAINT `fk_vehiculos_modelos1` FOREIGN KEY (`modelos_idmodelos`) REFERENCES `modelos` (`idmodelos`),
  CONSTRAINT `fk_vehiculos_tipo_vehiculos1` FOREIGN KEY (`tipo_vehiculos_idtipo_vehiculos`) REFERENCES `tipo_vehiculos` (`idtipo_vehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,'AA000BB','SDFJHAJF5465','546543SDF321',2022,'12.356 km','2024-11-06 00:00:00',NULL,1,1,4,2,NULL),(2,'AA001BB','SDAF4546FDSA3','64651231SA',2014,'85.635 km','2024-11-07 00:00:00',NULL,1,2,4,1,NULL),(3,'AA002BB','SAFJNSAJFJF','KLLKSAFKKLM',2021,'45.235 km','2025-04-01 00:00:00',NULL,1,11,3,3,NULL);
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `idventas` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(45) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `tipo_pago_idtipo_pago` int NOT NULL,
  `vehiculo_idvehiculo` int NOT NULL,
  `registro_clientes_idregistro_clientes` int NOT NULL,
  PRIMARY KEY (`idventas`),
  KEY `fk_ventas_tipo_pago1_idx` (`tipo_pago_idtipo_pago`) /*!80000 INVISIBLE */,
  KEY `fk_ventas_idvehiculo_idx` (`vehiculo_idvehiculo`),
  KEY `fk_ventas_registro_clientes1_idx` (`registro_clientes_idregistro_clientes`),
  CONSTRAINT `fk_ventas_idvehiculo` FOREIGN KEY (`vehiculo_idvehiculo`) REFERENCES `vehiculos` (`idvehiculos`),
  CONSTRAINT `fk_ventas_registro_clientes1` FOREIGN KEY (`registro_clientes_idregistro_clientes`) REFERENCES `registro_clientes` (`idregistro_clientes`),
  CONSTRAINT `fk_ventas_tipo_pago1` FOREIGN KEY (`tipo_pago_idtipo_pago`) REFERENCES `tipo_pago` (`idtipo_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-25 19:56:39
