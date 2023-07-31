ALTER TABLE `property` ADD `flyer_description_es` TEXT  NULL;
ALTER TABLE `property` ADD `flyer_description_en` TEXT  NULL;
update property set flyer_description_es = description_es, flyer_description_en = description_en;

CREATE TABLE `property_price_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `property_id` bigint(20) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `old_sale_price` decimal(10,2) DEFAULT NULL,
  `rent_price` decimal(10,2) DEFAULT NULL,
  `old_rent_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `property_id_idx` (`property_id`),
  KEY `account_id_idx` (`account_id`),
  CONSTRAINT `property_price_log_account_id_account_id` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`),
  CONSTRAINT `property_price_log_property_id_property_id` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
