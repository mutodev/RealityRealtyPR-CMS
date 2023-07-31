SET FOREIGN_KEY_CHECKS=0;
INSERT INTO `property_category` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`, `name_en`, `name_es`)
VALUES
	(1, ''House'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''House'', ''Casa''),
	(4, ''Multi-family (Up to 4 units)'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Multi-family (Up to 4 units)'', ''Multi-familiar (Hasta 4 unidades)''),
	(5, ''Apartment\\Estudio'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Apartment\\Studio'', ''Apartamento\\Estudio''),
	(6, ''Apartment\\Walk-up'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Apartment\\Walk-up'', ''Apartamento\\Walk-up''),
	(7, ''Apartment\\Condo'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Apartment\\Condo'', ''Apartamento\\Condominio''),
	(29, ''Other'', ''Residential'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Other'', ''Otro''),
	(32, ''Office'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Office'', ''Office''),
	(33, ''Mixed Use'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Mixed Use'', ''Mixed Use''),
	(34, ''Shopping Center'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Shopping Center'', ''Shopping Center''),
	(35, ''Hotel'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Hotel / Guest House'', ''Hotel / Guest House''),
	(36, ''Gas Station'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Gas Station'', ''Gas Station''),
	(45, ''Industrial'', ''Commercial'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Industrial'', ''Industrial''),
	(46, ''Other'', ''Commercial'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Other'', ''Other''),
	(48, ''Residential Lot'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Residential Lot'', ''Residential Lot''),
	(49, ''Commercial Lot'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Commercial Lot'', ''Commercial Lot''),
	(50, ''Agricultural Land'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Agricultural Land'', ''Finca Agricula''),
	(51, ''Industrial'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Industrial Farm'', ''Industrial Farm''),
	(52, ''Remnant Lot'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Remnant Lot'', ''Remanente para desarrollo''),
	(53, ''Other'', ''Land'', 0, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Other'', ''Other''),
	(54, ''Retail'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Retail'', ''Local Comercial''),
	(55, ''Multi-family (More than 5 units)'', ''Commercial'', 30, ''0000-00-00 00:00:00'', ''0000-00-00 00:00:00'', ''Multi-family (More than 5 units)'', ''Multi-family (More than 5 units)'');

	SET FOREIGN_KEY_CHECKS=1;
