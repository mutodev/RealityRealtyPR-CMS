# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: cmsoldrr
# Generation Time: 2017-05-29 18:39:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table llamadas_sources
# ------------------------------------------------------------

LOCK TABLES `lead_source` WRITE;
/*!40000 ALTER TABLE `llamadas_sources` DISABLE KEYS */;

INSERT INTO `lead_source` (`id`, `name`)
VALUES
	(1,'Website RR'),
	(2,'Rotulo'),
	(3,'Clasificadosonline.com'),
	(4,'Clasificados.pr'),
	(5,'Esfera de Influencia'),
	(6,'El Nuevo Dia'),
	(7,'Compraoalquila'),
	(8,'Dubina'),
	(9,'Guia telefonica'),
	(10,'TV - Anuncio Subasta'),
	(11,'Revista Subasta'),
	(12,'Zillow'),
	(13,'Referido'),
	(14,'Walk In'),
	(15,'RealEstateBook'),
	(16,'La Semana'),
	(17,'Realtor.com'),
	(19,'Facebook'),
	(20,'Sin identificar'),
	(21,'Clasificados Popular'),
	(22,'MLS');

/*!40000 ALTER TABLE `llamadas_sources` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
