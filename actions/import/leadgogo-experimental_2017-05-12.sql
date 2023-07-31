# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: leadgogo-experimental
# Generation Time: 2017-05-12 18:24:40 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table lead_review
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review`;

CREATE TABLE `lead_review` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_history_id` bigint(20) unsigned NOT NULL,
  `form_id` bigint(20) unsigned NOT NULL,
  `reviewer_id` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `invalid_reason` varchar(255) DEFAULT NULL,
  `last_activity_at` datetime DEFAULT NULL,
  `duration` smallint(6) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_history_id_idx` (`lead_history_id`),
  KEY `form_id_idx` (`form_id`),
  KEY `reviewer_id_idx` (`reviewer_id`),
  CONSTRAINT `lead_review_form_id_lead_review_form_id` FOREIGN KEY (`form_id`) REFERENCES `lead_review_form` (`id`),
  CONSTRAINT `lead_review_lead_history_id_lead_history_id` FOREIGN KEY (`lead_history_id`) REFERENCES `lead_history` (`id`),
  CONSTRAINT `lead_review_reviewer_id_account_id` FOREIGN KEY (`reviewer_id`) REFERENCES `account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_answer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_answer`;

CREATE TABLE `lead_review_answer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint(20) unsigned NOT NULL,
  `field_id` bigint(20) unsigned NOT NULL,
  `option_id` bigint(20) unsigned DEFAULT NULL,
  `open_answer` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id_idx` (`review_id`),
  KEY `field_id_idx` (`field_id`),
  KEY `option_id_idx` (`option_id`),
  CONSTRAINT `lead_review_answer_field_id_lead_review_form_field_id` FOREIGN KEY (`field_id`) REFERENCES `lead_review_form_field` (`id`),
  CONSTRAINT `lead_review_answer_option_id_lead_review_form_field_option_id` FOREIGN KEY (`option_id`) REFERENCES `lead_review_form_field_option` (`id`),
  CONSTRAINT `lead_review_answer_review_id_lead_review_id` FOREIGN KEY (`review_id`) REFERENCES `lead_review` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form`;

CREATE TABLE `lead_review_form` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_es` varchar(255) NOT NULL,
  `description_en` text,
  `description_es` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_company`;

CREATE TABLE `lead_review_form_company` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id_idx` (`form_id`),
  KEY `company_id_idx` (`company_id`),
  KEY `department_id_idx` (`department_id`),
  CONSTRAINT `lead_review_form_company_company_id_company_id` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  CONSTRAINT `lead_review_form_company_department_id_company_id` FOREIGN KEY (`department_id`) REFERENCES `company` (`id`),
  CONSTRAINT `lead_review_form_company_form_id_lead_review_form_id` FOREIGN KEY (`form_id`) REFERENCES `lead_review_form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_field
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_field`;

CREATE TABLE `lead_review_form_field` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fieldset_id` bigint(20) unsigned NOT NULL,
  `type` text NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `depend_condition` varchar(255) DEFAULT NULL,
  `position` bigint(20) DEFAULT '0',
  `slug` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `question_en` varchar(255) NOT NULL,
  `question_es` varchar(255) NOT NULL,
  `description_en` text,
  `description_es` text,
  `example_en` text,
  `example_es` text,
  PRIMARY KEY (`id`),
  KEY `fieldset_id_idx` (`fieldset_id`),
  CONSTRAINT `lead_review_form_field_fieldset_id_lead_review_form_fieldset_id` FOREIGN KEY (`fieldset_id`) REFERENCES `lead_review_form_fieldset` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_field_dependency
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_field_dependency`;

CREATE TABLE `lead_review_form_field_dependency` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `depend_field_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_id_idx` (`field_id`),
  KEY `depend_field_id_idx` (`depend_field_id`),
  CONSTRAINT `ldli` FOREIGN KEY (`depend_field_id`) REFERENCES `lead_review_form_field` (`id`),
  CONSTRAINT `lfli` FOREIGN KEY (`field_id`) REFERENCES `lead_review_form_field` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_field_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_field_option`;

CREATE TABLE `lead_review_form_field_option` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT '0',
  `position` bigint(20) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `label_en` varchar(255) NOT NULL,
  `label_es` varchar(255) NOT NULL,
  `description_en` text,
  `description_es` text,
  PRIMARY KEY (`id`),
  KEY `field_id_idx` (`field_id`),
  CONSTRAINT `lead_review_form_field_option_field_id_lead_review_form_field_id` FOREIGN KEY (`field_id`) REFERENCES `lead_review_form_field` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_fieldset
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_fieldset`;

CREATE TABLE `lead_review_form_fieldset` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `position` bigint(20) DEFAULT '0',
  `slug` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_es` varchar(255) NOT NULL,
  `description_en` text,
  `description_es` text,
  PRIMARY KEY (`id`),
  KEY `form_id_idx` (`form_id`),
  CONSTRAINT `lead_review_form_fieldset_form_id_lead_review_form_id` FOREIGN KEY (`form_id`) REFERENCES `lead_review_form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_notification
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_notification`;

CREATE TABLE `lead_review_form_notification` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `condition` text NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_es` varchar(255) NOT NULL,
  `description_en` text NOT NULL,
  `description_es` text NOT NULL,
  `sms_content_en` text,
  `sms_content_es` text,
  `email_content_en` text,
  `email_content_es` text,
  `email_subject_en` varchar(255) DEFAULT NULL,
  `email_subject_es` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id_idx` (`form_id`),
  CONSTRAINT `lead_review_form_notification_form_id_lead_review_form_id` FOREIGN KEY (`form_id`) REFERENCES `lead_review_form` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lead_review_form_notification_company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lead_review_form_notification_company`;

CREATE TABLE `lead_review_form_notification_company` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_form_id` bigint(20) unsigned NOT NULL,
  `notification_id` bigint(20) unsigned DEFAULT NULL,
  `recipient_group_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_id_idx` (`notification_id`),
  KEY `recipient_group_id_idx` (`recipient_group_id`),
  KEY `company_form_id_idx` (`company_form_id`),
  CONSTRAINT `lcli` FOREIGN KEY (`company_form_id`) REFERENCES `lead_review_form_company` (`id`),
  CONSTRAINT `lnli` FOREIGN KEY (`notification_id`) REFERENCES `lead_review_form_notification` (`id`),
  CONSTRAINT `lrni` FOREIGN KEY (`recipient_group_id`) REFERENCES `notification_recipient_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
