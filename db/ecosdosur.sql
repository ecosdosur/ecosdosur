-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ecosdosur
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
-- Table structure for table `agenda`
--

DROP TABLE IF EXISTS `agenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agenda` (
  `CODTERC` int(11) NOT NULL,
  `CODDEPT` int(11) NOT NULL,
  `DESCDEPT` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOMBRE` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NIF` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TLFFIJOS` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FAX` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TLFMOVIL` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CARGO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOTAS` mediumtext COLLATE latin1_spanish_ci,
  PRIMARY KEY (`CODTERC`,`CODDEPT`),
  CONSTRAINT `FK_agenda_1` FOREIGN KEY (`CODTERC`) REFERENCES `terceros` (`CODTERC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos` (
  `CODALUMNO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOIDENT` int(11) DEFAULT NULL,
  `NUMDOCUM` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NUMTARJSS` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODPOSTAL` int(11) DEFAULT NULL,
  `LOCALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODMUNICIPIO` int(11) DEFAULT NULL,
  `CODPROV` int(11) DEFAULT NULL,
  `FECHANAC` date DEFAULT NULL,
  `LUGARNAC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TLFMOVIL` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TLFFIJO` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMAIL` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CURSOS` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TITULACION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ESPECIALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `UNIVERSIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NUMHORASPRAC` int(11) DEFAULT NULL,
  `SERVICIOPRAC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ACTIVIDADES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODIDIOMA1` int(11) DEFAULT NULL,
  `CTIPONIVIDI1` int(11) DEFAULT NULL,
  `CODIDIOMA2` int(11) DEFAULT NULL,
  `CTIPONIVIDI2` int(11) DEFAULT NULL,
  `VALORACION` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODALUMNO`),
  KEY `FK_alumnos_1` (`CTIPOIDENT`),
  KEY `FK_alumnos_2` (`CODMUNICIPIO`),
  KEY `FK_alumnos_3` (`CODPROV`),
  KEY `FK_alumnos_4` (`CTIPONIVIDI1`),
  KEY `FK_alumnos_5` (`CTIPONIVIDI2`),
  KEY `FK_alumnos_6` (`CODIDIOMA1`),
  KEY `FK_alumnos_7` (`CODIDIOMA2`),
  CONSTRAINT `FK_alumnos_1` FOREIGN KEY (`CTIPOIDENT`) REFERENCES `tipo_identificacion` (`CTIPOIDENT`),
  CONSTRAINT `FK_alumnos_2` FOREIGN KEY (`CODMUNICIPIO`) REFERENCES `municipios` (`CODMUNICIPIO`),
  CONSTRAINT `FK_alumnos_3` FOREIGN KEY (`CODPROV`) REFERENCES `provincias` (`CODPROV`),
  CONSTRAINT `FK_alumnos_4` FOREIGN KEY (`CTIPONIVIDI1`) REFERENCES `tipo_nividioma` (`CTIPONIVIDI`),
  CONSTRAINT `FK_alumnos_5` FOREIGN KEY (`CTIPONIVIDI2`) REFERENCES `tipo_nividioma` (`CTIPONIVIDI`),
  CONSTRAINT `FK_alumnos_6` FOREIGN KEY (`CODIDIOMA1`) REFERENCES `idiomas` (`CODIDIOMA`),
  CONSTRAINT `FK_alumnos_7` FOREIGN KEY (`CODIDIOMA2`) REFERENCES `idiomas` (`CODIDIOMA`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `areasgeo`
--

DROP TABLE IF EXISTS `areasgeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areasgeo` (
  `CODAREA` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODAREA`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avisos`
--

DROP TABLE IF EXISTS `avisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avisos` (
  `NAVISO` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CODOFERTA` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `HISTORIAL` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `AVISADO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `MODOAVISO` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAAVISO` date DEFAULT NULL,
  `PTECONFIRM` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `ACEPTA` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHACONF` date DEFAULT NULL,
  `CONTRATADO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`NAVISO`),
  KEY `FK_avisos_1` (`CODOFERTA`),
  KEY `FK_avisos_2` (`HISTORIAL`),
  CONSTRAINT `FK_avisos_1` FOREIGN KEY (`CODOFERTA`) REFERENCES `ofertas` (`CODOFERTA`),
  CONSTRAINT `FK_avisos_2` FOREIGN KEY (`HISTORIAL`) REFERENCES `demandantes` (`HISTORIAL`)
) ENGINE=InnoDB AUTO_INCREMENT=9550 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datos_sanitarios`
--

DROP TABLE IF EXISTS `datos_sanitarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datos_sanitarios` (
  `HISTORIAL` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `EDAD` int(11) DEFAULT NULL,
  `CTIPOGREDAD` int(11) DEFAULT NULL,
  `RESIDENCIA` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NACIONALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODAREA` int(11) DEFAULT NULL,
  `MEDMANTOUX` int(11) DEFAULT NULL,
  `CTIPOMANTOUX` int(11) DEFAULT NULL,
  `CTIPOTRAT` int(11) DEFAULT NULL,
  `CODVACBCG` int(11) DEFAULT NULL,
  `CTIPOFACT` int(11) DEFAULT NULL,
  `CODMES` int(11) DEFAULT NULL,
  `INFOVIH` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DERIVVIH` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`HISTORIAL`),
  KEY `FK_datos_sanitarios_1` (`CTIPOGREDAD`),
  KEY `FK_datos_sanitarios_2` (`CODAREA`),
  KEY `FK_datos_sanitarios_3` (`CTIPOMANTOUX`),
  KEY `FK_datos_sanitarios_4` (`CTIPOTRAT`),
  KEY `FK_datos_sanitarios_5` (`CODVACBCG`),
  KEY `FK_datos_sanitarios_6` (`CTIPOFACT`),
  KEY `FK_datos_sanitarios_7` (`CODMES`),
  CONSTRAINT `FK_datos_sanitarios_1` FOREIGN KEY (`CTIPOGREDAD`) REFERENCES `tipo_grupoedad` (`CTIPOGREDAD`),
  CONSTRAINT `FK_datos_sanitarios_2` FOREIGN KEY (`CODAREA`) REFERENCES `areasgeo` (`CODAREA`),
  CONSTRAINT `FK_datos_sanitarios_3` FOREIGN KEY (`CTIPOMANTOUX`) REFERENCES `tipo_mantoux` (`CTIPOMANTOUX`),
  CONSTRAINT `FK_datos_sanitarios_4` FOREIGN KEY (`CTIPOTRAT`) REFERENCES `tipo_tratamiento` (`CTIPOTRAT`),
  CONSTRAINT `FK_datos_sanitarios_5` FOREIGN KEY (`CODVACBCG`) REFERENCES `tipo_snnsnc` (`CTIPOSNNSNC`),
  CONSTRAINT `FK_datos_sanitarios_6` FOREIGN KEY (`CTIPOFACT`) REFERENCES `tipo_factriesgo` (`CTIPOFACT`),
  CONSTRAINT `FK_datos_sanitarios_7` FOREIGN KEY (`CODMES`) REFERENCES `meses` (`CODMES`),
  CONSTRAINT `FK_datos_sanitarios_8` FOREIGN KEY (`HISTORIAL`) REFERENCES `usuarios` (`HISTORIAL`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandantes`
--

DROP TABLE IF EXISTS `demandantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demandantes` (
  `HISTORIAL` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `NODISPONIBLE` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `MODOCONECTAR` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EXPPROFES` mediumtext COLLATE latin1_spanish_ci,
  `FORMACION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPODEMLAB` int(11) DEFAULT NULL,
  `MEJORAEMPLEO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `MEJORAOTROS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ANOTESPEC` mediumtext COLLATE latin1_spanish_ci,
  `HORASNOTRAB` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIASNOTRAB` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPODISPV` int(11) DEFAULT NULL,
  `TRABINT` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP1` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP2` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP3` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP4` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CARNETESP` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TIPOCARNET` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISPVEHICULO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRABAJOSNO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRABAJOSSI` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `AVISOSOFERTAS` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`HISTORIAL`),
  KEY `FK_inclusion_1` (`CTIPODEMLAB`),
  KEY `FK_demandantes_2` (`CTIPODISPV`),
  CONSTRAINT `FK_demandantes_2` FOREIGN KEY (`CTIPODISPV`) REFERENCES `tipo_dispviajar` (`CTIPODISPV`),
  CONSTRAINT `FK_demandantes_3` FOREIGN KEY (`HISTORIAL`) REFERENCES `usuarios` (`HISTORIAL`),
  CONSTRAINT `FK_inclusion_1` FOREIGN KEY (`CTIPODEMLAB`) REFERENCES `tipo_demlab` (`CTIPODEMLAB`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demandantes_3`
--

DROP TABLE IF EXISTS `demandantes_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demandantes_3` (
  `HISTORIAL` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `NODISPONIBLE` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `MODOCONECTAR` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EXPPROFES` mediumtext COLLATE latin1_spanish_ci,
  `FORMACION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPODEMLAB` int(11) DEFAULT NULL,
  `MEJORAEMPLEO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `MEJORAOTROS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ANOTESPEC` mediumtext COLLATE latin1_spanish_ci,
  `HORASNOTRAB` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIASNOTRAB` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPODISPV` int(11) DEFAULT NULL,
  `TRABINT` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP1` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP2` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP3` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISP4` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CARNETESP` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TIPOCARNET` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISPVEHICULO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRABAJOSNO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRABAJOSSI` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `AVISOSOFERTAS` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`HISTORIAL`),
  KEY `FK_inclusion_1` (`CTIPODEMLAB`),
  KEY `FK_demandantes_2` (`CTIPODISPV`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `donativos`
--

DROP TABLE IF EXISTS `donativos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donativos` (
  `NSOCIO` int(11) NOT NULL,
  `AÑO` int(11) NOT NULL,
  `IMPCUOTA` decimal(13,2) DEFAULT NULL,
  `PERIODICIDAD` int(11) DEFAULT NULL,
  `DONATIVOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OBSERVACIONES` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`NSOCIO`,`AÑO`),
  CONSTRAINT `FK_donativos_1` FOREIGN KEY (`NSOCIO`) REFERENCES `socios` (`NSOCIO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empleadores`
--

DROP TABLE IF EXISTS `empleadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleadores` (
  `CODEMP` int(11) NOT NULL AUTO_INCREMENT,
  `CTIPOEMP` int(11) DEFAULT NULL,
  `NOMBRE` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODMUNICIPIO` int(11) DEFAULT NULL,
  `CODPROV` int(11) DEFAULT NULL,
  `LOCALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TELEFONO1` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TELEFONO2` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PROGESPEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `SOCIO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `BONO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `APORTBONO` decimal(13,2) DEFAULT NULL,
  `FECHAAPORT` date DEFAULT NULL,
  `APORTBONO2` decimal(13,2) DEFAULT NULL,
  `FECHAAPORT2` date DEFAULT NULL,
  `NOOFERTAS` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODEMP`),
  KEY `FK_empleadores_1` (`CTIPOEMP`),
  KEY `FK_empleadores_2` (`CODPROV`),
  KEY `FK_empleadores_3` (`CODMUNICIPIO`),
  CONSTRAINT `FK_empleadores_1` FOREIGN KEY (`CTIPOEMP`) REFERENCES `tipo_empleador` (`CTIPOEMP`),
  CONSTRAINT `FK_empleadores_2` FOREIGN KEY (`CODPROV`) REFERENCES `provincias` (`CODPROV`),
  CONSTRAINT `FK_empleadores_3` FOREIGN KEY (`CODMUNICIPIO`) REFERENCES `municipios` (`CODMUNICIPIO`)
) ENGINE=InnoDB AUTO_INCREMENT=510 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idiomas`
--

DROP TABLE IF EXISTS `idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idiomas` (
  `CODIDIOMA` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODIDIOMA`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `meses`
--

DROP TABLE IF EXISTS `meses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meses` (
  `CODMES` int(11) NOT NULL,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODMES`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modo_aviso`
--

DROP TABLE IF EXISTS `modo_aviso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modo_aviso` (
  `CMODOAVISO` int(11) NOT NULL,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CMODOAVISO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `modo_conectar`
--

DROP TABLE IF EXISTS `modo_conectar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modo_conectar` (
  `CMODOCONECTAR` int(11) NOT NULL,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CMODOCONECTAR`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `municipios`
--

DROP TABLE IF EXISTS `municipios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `municipios` (
  `CODMUNICIPIO` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODMUNICIPIO`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nacionalidades`
--

DROP TABLE IF EXISTS `nacionalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nacionalidades` (
  `CODNACIONAL` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODNACIONAL`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ofertas`
--

DROP TABLE IF EXISTS `ofertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ofertas` (
  `CODOFERTA` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `FECHA` date DEFAULT NULL,
  `CODEMP` int(11) DEFAULT NULL,
  `CTIPOSS` int(11) DEFAULT NULL,
  `CLASETRABAJO` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO1` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO2` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO3` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO4` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO5` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO6` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIATRABAJO7` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE1` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE2` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE3` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE4` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE5` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE6` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIALIBRE7` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `HORARIO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `SALARIO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DURACION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONDICIONES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PROGESPEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONF1` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONF2` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONF3` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONF4` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOCONF` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `OBSERVACIONES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODOFERTA`),
  KEY `FK_ofertas_1` (`CODEMP`),
  KEY `FK_ofertas_2` (`CTIPOSS`),
  CONSTRAINT `FK_ofertas_1` FOREIGN KEY (`CODEMP`) REFERENCES `empleadores` (`CODEMP`),
  CONSTRAINT `FK_ofertas_2` FOREIGN KEY (`CTIPOSS`) REFERENCES `tipo_segsocial` (`CTIPOSS`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paises` (
  `CODPAIS` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODPAIS`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provincias`
--

DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provincias` (
  `CODPROV` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CODPROV`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socios`
--

DROP TABLE IF EXISTS `socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socios` (
  `NSOCIO` int(11) NOT NULL,
  `REFERENCIA` int(11) DEFAULT NULL,
  `NORDEN` int(11) DEFAULT NULL,
  `TRATAMIENTO` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOMBRE` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DNI` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODPOSTAL` int(11) DEFAULT NULL,
  `CODMUNICIPIO` int(11) DEFAULT NULL,
  `CODPROV` int(11) DEFAULT NULL,
  `LOCALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NCUENTA` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAALTA` date DEFAULT NULL,
  `TELEFONO1` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TELEFONO2` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMAIL` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OBSERVACIONES` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`NSOCIO`),
  KEY `FK_socios_1` (`CODPROV`),
  KEY `FK_socios_2` (`CODMUNICIPIO`),
  CONSTRAINT `FK_socios_1` FOREIGN KEY (`CODPROV`) REFERENCES `provincias` (`CODPROV`),
  CONSTRAINT `FK_socios_2` FOREIGN KEY (`CODMUNICIPIO`) REFERENCES `municipios` (`CODMUNICIPIO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `terceros`
--

DROP TABLE IF EXISTS `terceros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terceros` (
  `CODTERC` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `CIF` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODTERC`)
) ENGINE=InnoDB AUTO_INCREMENT=582 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_conocimiento`
--

DROP TABLE IF EXISTS `tipo_conocimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_conocimiento` (
  `CTIPOCONOC` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOCONOC`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_demlab`
--

DROP TABLE IF EXISTS `tipo_demlab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_demlab` (
  `CTIPODEMLAB` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPODEMLAB`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_dispviajar`
--

DROP TABLE IF EXISTS `tipo_dispviajar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_dispviajar` (
  `CTIPODISPV` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPODISPV`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_ecivil`
--

DROP TABLE IF EXISTS `tipo_ecivil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_ecivil` (
  `CTIPOECIVIL` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOECIVIL`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_empleador`
--

DROP TABLE IF EXISTS `tipo_empleador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_empleador` (
  `CTIPOEMP` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOEMP`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_estudios`
--

DROP TABLE IF EXISTS `tipo_estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_estudios` (
  `CTIPOESTUDIOS` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOESTUDIOS`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_factriesgo`
--

DROP TABLE IF EXISTS `tipo_factriesgo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_factriesgo` (
  `CTIPOFACT` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOFACT`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_grupoedad`
--

DROP TABLE IF EXISTS `tipo_grupoedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_grupoedad` (
  `CTIPOGREDAD` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOGREDAD`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_identificacion`
--

DROP TABLE IF EXISTS `tipo_identificacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_identificacion` (
  `CTIPOIDENT` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOIDENT`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_mantoux`
--

DROP TABLE IF EXISTS `tipo_mantoux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_mantoux` (
  `CTIPOMANTOUX` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOMANTOUX`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_migrante`
--

DROP TABLE IF EXISTS `tipo_migrante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_migrante` (
  `CTIPOMIGRANTE` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOMIGRANTE`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_nividioma`
--

DROP TABLE IF EXISTS `tipo_nividioma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_nividioma` (
  `CTIPONIVIDI` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPONIVIDI`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_segsocial`
--

DROP TABLE IF EXISTS `tipo_segsocial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_segsocial` (
  `CTIPOSS` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOSS`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_sitlab`
--

DROP TABLE IF EXISTS `tipo_sitlab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_sitlab` (
  `CTIPOSITLAB` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOSITLAB`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_snnsnc`
--

DROP TABLE IF EXISTS `tipo_snnsnc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_snnsnc` (
  `CTIPOSNNSNC` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOSNNSNC`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_tarjetass`
--

DROP TABLE IF EXISTS `tipo_tarjetass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_tarjetass` (
  `CTIPOTARJSS` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOTARJSS`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_tenencia`
--

DROP TABLE IF EXISTS `tipo_tenencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_tenencia` (
  `CTIPOTENENCIA` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOTENENCIA`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_tratamiento`
--

DROP TABLE IF EXISTS `tipo_tratamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_tratamiento` (
  `CTIPOTRAT` int(11) NOT NULL AUTO_INCREMENT,
  `DESCRIPCION` varchar(100) COLLATE latin1_spanish_ci NOT NULL,
  PRIMARY KEY (`CTIPOTRAT`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `HISTORIAL` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `PROGMIRGA` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `ENTREVISTADOR` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAACOGIDA` date DEFAULT NULL,
  `EXPAFINES1` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EXPAFINES2` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EXPAFINES3` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOMIGRANTE` int(11) DEFAULT NULL,
  `PRIMERAVEZ` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `ANTIGUEDAD` int(11) DEFAULT NULL,
  `CTIPOCONOC` int(11) DEFAULT NULL,
  `OTROSCONOC` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DERIVADO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DERIVADOCUAL` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PROGESPEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOIDENT` int(11) DEFAULT NULL,
  `NUMDOCUM` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOMBRE` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PERMRES` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `PERMTRAB` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TIPOPERM` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAVALPERM` date DEFAULT NULL,
  `SEXO` enum('H','M') COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHANAC` date DEFAULT NULL,
  `CODPAIS` int(11) DEFAULT NULL,
  `CODAREA` int(11) DEFAULT NULL,
  `CODNACIONAL` int(11) DEFAULT NULL,
  `TELEFONO1` int(10) DEFAULT NULL,
  `TELEFONO2` int(10) DEFAULT NULL,
  `DOMICILIO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `LOCALIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODMUNICIPIO` int(11) DEFAULT NULL,
  `CODPROV` int(11) DEFAULT NULL,
  `CODPOST` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMAIL` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CONVIVE` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `NUMPERSCONV` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `RELACION` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `VIVIENDAOK` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMBIODOM` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CAMBIORES` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOTENENCIA` int(11) DEFAULT NULL,
  `CTIPOECIVIL` int(11) DEFAULT NULL,
  `RESIDPAREJA` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PAREJAECOS` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `NUMHIJOS` int(10) DEFAULT NULL,
  `EDADHIJO1` int(10) DEFAULT NULL,
  `EDADHIJO2` int(10) DEFAULT NULL,
  `EDADHIJO3` int(10) DEFAULT NULL,
  `EDADHIJO4` int(10) DEFAULT NULL,
  `RESIDHIJOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ESCOLARHIJOS` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `FAMILIAESP` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRAERFAMILIAR` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DESTCOR` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `APOYO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `RAZONELEC` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `AYUDAPUBLICA` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `TIPOAYUDA` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOESTUDIOS` int(11) DEFAULT NULL,
  `OTROSESTUDIOS` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODIDIOMA1` int(11) DEFAULT NULL,
  `CTIPONIVIDI1` int(11) DEFAULT NULL,
  `CODIDIOMA2` int(11) DEFAULT NULL,
  `CTIPONIVIDI2` int(11) DEFAULT NULL,
  `ULTIMAOCUP` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OTRASOCUP` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TRABESP` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `ASEGURADO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `DEMANDATRAB` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `SECTORESP` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OTROSSECT` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOSITLAB` int(11) DEFAULT NULL,
  `OTRASSITLAB` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CTIPOTARJSS` int(11) DEFAULT NULL,
  `NUMTARJSS` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ACUDIRURG` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `PRUEBATB` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `PRUEBAMANTOUX` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAMANTOUX` date DEFAULT NULL,
  `PQNOMANTOUX` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMPADRONADO` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `LUGAREMP` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHAEMP` date DEFAULT NULL,
  `ARRAIGOESP` enum('S','N') COLLATE latin1_spanish_ci DEFAULT NULL,
  `FECHASCHG` date DEFAULT NULL,
  `MODOENTSCHG` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OTROSSCHG` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CURSOSDEM` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `OTROSCURSOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DEMANDASOL` mediumtext COLLATE latin1_spanish_ci,
  `OTRASDEM` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DERIVAROTRAS` varchar(155) COLLATE latin1_spanish_ci DEFAULT NULL,
  `MOTIVOSDERIV` mediumtext COLLATE latin1_spanish_ci,
  `CURSOSSUP` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `AVISOS` mediumtext COLLATE latin1_spanish_ci,
  PRIMARY KEY (`HISTORIAL`),
  KEY `FK_usuarios_1` (`CTIPOMIGRANTE`),
  KEY `FK_usuarios_2` (`CTIPOCONOC`),
  KEY `FK_usuarios_3` (`CTIPOIDENT`),
  KEY `FK_usuarios_4` (`CODAREA`),
  KEY `FK_usuarios_5` (`CTIPOECIVIL`),
  KEY `FK_usuarios_6` (`CTIPOESTUDIOS`),
  KEY `FK_usuarios_7` (`CTIPONIVIDI1`),
  KEY `FK_usuarios_8` (`CTIPONIVIDI2`),
  KEY `FK_usuarios_9` (`CTIPOSITLAB`),
  KEY `FK_usuarios_10` (`CTIPOTARJSS`),
  KEY `FK_usuarios_11` (`CTIPOTENENCIA`),
  KEY `FK_usuarios_12` (`CODPAIS`) USING BTREE,
  KEY `FK_usuarios_13` (`CODNACIONAL`),
  KEY `FK_usuarios_14` (`CODMUNICIPIO`),
  KEY `FK_usuarios_15` (`CODPROV`),
  KEY `FK_usuarios_16` (`CODIDIOMA1`),
  KEY `FK_usuarios_17` (`CODIDIOMA2`),
  CONSTRAINT `FK_usuarios_1` FOREIGN KEY (`CTIPOMIGRANTE`) REFERENCES `tipo_migrante` (`CTIPOMIGRANTE`),
  CONSTRAINT `FK_usuarios_10` FOREIGN KEY (`CTIPOTARJSS`) REFERENCES `tipo_tarjetass` (`CTIPOTARJSS`),
  CONSTRAINT `FK_usuarios_11` FOREIGN KEY (`CTIPOTENENCIA`) REFERENCES `tipo_tenencia` (`CTIPOTENENCIA`),
  CONSTRAINT `FK_usuarios_12` FOREIGN KEY (`CODPAIS`) REFERENCES `paises` (`CODPAIS`),
  CONSTRAINT `FK_usuarios_13` FOREIGN KEY (`CODNACIONAL`) REFERENCES `nacionalidades` (`CODNACIONAL`),
  CONSTRAINT `FK_usuarios_14` FOREIGN KEY (`CODMUNICIPIO`) REFERENCES `municipios` (`CODMUNICIPIO`),
  CONSTRAINT `FK_usuarios_15` FOREIGN KEY (`CODPROV`) REFERENCES `provincias` (`CODPROV`),
  CONSTRAINT `FK_usuarios_16` FOREIGN KEY (`CODIDIOMA1`) REFERENCES `idiomas` (`CODIDIOMA`),
  CONSTRAINT `FK_usuarios_17` FOREIGN KEY (`CODIDIOMA2`) REFERENCES `idiomas` (`CODIDIOMA`),
  CONSTRAINT `FK_usuarios_2` FOREIGN KEY (`CTIPOCONOC`) REFERENCES `tipo_conocimiento` (`CTIPOCONOC`),
  CONSTRAINT `FK_usuarios_3` FOREIGN KEY (`CTIPOIDENT`) REFERENCES `tipo_identificacion` (`CTIPOIDENT`),
  CONSTRAINT `FK_usuarios_4` FOREIGN KEY (`CODAREA`) REFERENCES `areasgeo` (`CODAREA`),
  CONSTRAINT `FK_usuarios_5` FOREIGN KEY (`CTIPOECIVIL`) REFERENCES `tipo_ecivil` (`CTIPOECIVIL`),
  CONSTRAINT `FK_usuarios_6` FOREIGN KEY (`CTIPOESTUDIOS`) REFERENCES `tipo_estudios` (`CTIPOESTUDIOS`),
  CONSTRAINT `FK_usuarios_7` FOREIGN KEY (`CTIPONIVIDI1`) REFERENCES `tipo_nividioma` (`CTIPONIVIDI`),
  CONSTRAINT `FK_usuarios_8` FOREIGN KEY (`CTIPONIVIDI2`) REFERENCES `tipo_nividioma` (`CTIPONIVIDI`),
  CONSTRAINT `FK_usuarios_9` FOREIGN KEY (`CTIPOSITLAB`) REFERENCES `tipo_sitlab` (`CTIPOSITLAB`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voluntarios`
--

DROP TABLE IF EXISTS `voluntarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voluntarios` (
  `CODVOLUNTARIO` int(11) NOT NULL AUTO_INCREMENT,
  `APELLIDOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `NOMBRE` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DNI` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DIRECCION` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODPOSTAL` int(11) DEFAULT NULL,
  `LOCALIDAD` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `CODMUNICIPIO` int(11) DEFAULT NULL,
  `CODPROV` int(11) DEFAULT NULL,
  `FECHANAC` date DEFAULT NULL,
  `LUGARNAC` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TELEFONO1` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `TELEFONO2` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `EMAIL` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `ESTUDIOS` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `PROFESACT` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `LABORECOS` mediumtext COLLATE latin1_spanish_ci,
  `PROGCOLAB` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `DISPONIBILIDAD` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `HORARIO` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `HORASDEDIC` int(11) DEFAULT NULL,
  `FECHAINIECOS` date DEFAULT NULL,
  `FECHAFINECOS` date DEFAULT NULL,
  `FECHAFICHA` date DEFAULT NULL,
  `OBSERVACIONES` varchar(1000) COLLATE latin1_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`CODVOLUNTARIO`),
  KEY `FK_voluntarios_1` (`CODMUNICIPIO`),
  KEY `FK_voluntarios_2` (`CODPROV`),
  CONSTRAINT `FK_voluntarios_1` FOREIGN KEY (`CODMUNICIPIO`) REFERENCES `municipios` (`CODMUNICIPIO`),
  CONSTRAINT `FK_voluntarios_2` FOREIGN KEY (`CODPROV`) REFERENCES `provincias` (`CODPROV`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'ecosdosur'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-19 15:47:18
