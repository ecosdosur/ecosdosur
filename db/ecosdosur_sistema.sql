-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ecosdosur_sistema
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `funperfil`
--

DROP TABLE IF EXISTS `funperfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `funperfil` (
  `PERFIL` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `PANTALLA` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `CONSULTA` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `MODIFICACION` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`PERFIL`,`PANTALLA`),
  KEY `FK_funperfil_2` (`PANTALLA`),
  CONSTRAINT `FK_funperfil_1` FOREIGN KEY (`PERFIL`) REFERENCES `perfiles` (`PERFIL`),
  CONSTRAINT `FK_funperfil_2` FOREIGN KEY (`PANTALLA`) REFERENCES `pantallas` (`PANTALLA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `informes`
--

DROP TABLE IF EXISTS `informes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informes` (
  `CODINFORME` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMPOSFIL` varchar(5000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `SELECTRES` varchar(5000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMPOSRES` varchar(5000) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODINFORME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `CODMENU` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ORDEN` int(11) DEFAULT NULL,
  PRIMARY KEY (`CODMENU`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pantallas`
--

DROP TABLE IF EXISTS `pantallas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pantallas` (
  `PANTALLA` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `LIBTABLA` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMPOSGES` varchar(5000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `SELECTRES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMPOSRES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `RELACIONADAS` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EDICIONESPEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `JSESPEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`PANTALLA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles` (
  `PERFIL` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`PERFIL`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submenus`
--

DROP TABLE IF EXISTS `submenus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submenus` (
  `CODMENU` int(10) unsigned NOT NULL,
  `CODSUBMENU` int(11) NOT NULL,
  `PANTALLA` varchar(10) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ORDEN` int(11) DEFAULT NULL,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODMENU`,`CODSUBMENU`),
  KEY `FK_submenus_2` (`PANTALLA`),
  CONSTRAINT `FK_submenus_1` FOREIGN KEY (`CODMENU`) REFERENCES `menus` (`CODMENU`),
  CONSTRAINT `FK_submenus_2` FOREIGN KEY (`PANTALLA`) REFERENCES `pantallas` (`PANTALLA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usrperfil`
--

DROP TABLE IF EXISTS `usrperfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usrperfil` (
  `USUARIO` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `PERFIL` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`USUARIO`,`PERFIL`),
  KEY `FK_usrperfil_2` (`PERFIL`),
  CONSTRAINT `FK_usrperfil_1` FOREIGN KEY (`USUARIO`) REFERENCES `usuarios` (`USUARIO`),
  CONSTRAINT `FK_usrperfil_2` FOREIGN KEY (`PERFIL`) REFERENCES `perfiles` (`PERFIL`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `USUARIO` varchar(10) COLLATE latin1_spanish_ci NOT NULL,
  `CUSUARIO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PASSWORD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`USUARIO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'ecosdosur_sistema'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-19 15:46:48
