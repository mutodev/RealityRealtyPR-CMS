//Categories
INSERT INTO `property_category` (`id`, `name`, `type`, `parent_id`, `created_at`, `updated_at`, `name_en`, `name_es`)
VALUES
	(1, 'House', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'House', 'Casa'),
	(4, 'Multi-family (Up to 4 units)', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Multi-family (Up to 4 units)', 'Multi-familiar (Hasta 4 unidades)'),
	(5, 'Apartment\\Estudio', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Apartment\\Studio', 'Apartamento\\Estudio'),
	(6, 'Apartment\\Walk-up', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Apartment\\Walk-up', 'Apartamento\\Walk-up'),
	(7, 'Apartment\\Condo', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Apartment\\Condo', 'Apartamento\\Condominio'),
	(8, 'Other', 'Residential', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Other', 'Otro'),
	(9, 'Office', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Office', 'Office'),
	(10, 'Mixed Use', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Mixed Use', 'Mixed Use'),
	(11, 'Shopping Center', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Shopping Center', 'Shopping Center'),
	(12, 'Hotel', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Hotel / Guest House', 'Hotel / Guest House'),
	(13, 'Gas Station', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Gas Station', 'Gas Station'),
	(14, 'Industrial', 'Commercial', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Industrial', 'Industrial'),
	(15, 'Other', 'Commercial', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Other', 'Other'),
	(16, 'Residential Lot', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Residential Lot', 'Residential Lot'),
	(17, 'Commercial Lot', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Commercial Lot', 'Commercial Lot'),
	(18, 'Agricultural Land', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Agricultural Land', 'Finca Agricula'),
	(19, 'Industrial', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Industrial Farm', 'Industrial Farm'),
	(20, 'Remnant Lot', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Remnant Lot', 'Remanente para desarrollo'),
	(21, 'Other', 'Land', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Other', 'Other'),
	(22, 'Retail', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Retail', 'Local Comercial'),
	(23, 'Multi-family (More than 5 units)', 'Commercial', 30, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Multi-family (More than 5 units)', 'Multi-family (More than 5 units)');


//Branches
select id, office_loc as name, dir as address1, tel as phone, email_adm as email from RBiz;

//Usuarios
select administrator, clave as password, 1 as company_id, subasta as auction, comercial as commercial, reventa as resell, short_sales, proyectos_nuevos as new_developments, relocation, reposeidas as foreclosure, id, email, email as username, tel1 as phone, tel2 as phone2, nombre as first_name, apellido as last_name, position, rbiz_id as branch_id, show_in_list, RealtorsInfo.lic as license, realtor as is_realtor, IF(status = 'ACTIVE', 1, 0) as active from Usuarios
LEFT JOIN RealtorsInfo on RealtorsInfo.id_usuario = Usuarios.id;

//Propiedades
select
Propiedades.nombre as title_es,
Propiedades.nombre as title_en,
Propiedades.id,
category_id,
1 as company_id,
id_pueblo as area_id,
id_usuario as account_id,
sevende as for_sale,
expira as end_at,
precio as sale_price,
sealquila as for_rent,
precio_renta as rent_price,
TiposAlquilerPropiedades.long_term as is_rent_long_term,
TiposAlquilerPropiedades.short_term as is_rent_short_term,
TiposAlquilerPropiedades.time_share as is_rent_time_share,
short_sale as is_short_sale,
(Repo.id IS NOT NULL) as is_repossessed,
PropiedadesDir.dir as address1,
PropiedadesDir.zip as postal_code,
year_built,
mantenimiento as maintenance,
mantenimiento_monthly as maintenance_price,
crim_exencion as exemption,
crim_exencion_monthly as exemption_price,
plati as latitude,
plong as longitude,
catastro,
fin_rural as financing_rural,
fin_fha as financing_fha,
fin_convencional as financing_conventional,
fin_vivienda as financing_vivienda,
fin_cash as financing_cash,
fin_repair_mortgage as financing_loan_with_repairs,
fin_financiamiento_privado as financing_private,
fin_asumir_hipoteca as financing_assume_mortgage,
'PUBLISHED' as status,
Info.banos as bathrooms,
Info.cuartos as rooms,
Info.piesc as sqf,
Info.metrosc as sqm,
num_interno as internal_number,
num_mls as mls_number,
num_co as co_number,
num_cpr as cpr_number,
Info.clasific as zonification,
PropiedadesDir.notas_internas as internal_notes,
PropiedadesDir.showing_instructions as showing_instructions,
key_num as key_number,
key_box,
Info.nota as description_es,
Info.nota as description_en,
Info.prkg as parkings,
Info.pisos as floors,
Info.tipo as extra,
Categories.new_id as category_id


from Propiedades
left join TiposAlquilerPropiedades ON Propiedades.id = TiposAlquilerPropiedades.id_propiedad
left join PropiedadesDir ON Propiedades.id = PropiedadesDir.id_propiedad
left join Repo ON Propiedades.id = Repo.id
inner join Categories ON Propiedades.category_id = Categories.id
left join Info ON Propiedades.id = Info.id_propiedad
where
Propiedades.expira >= 20160101
order by Propiedades.id DESC;

select * from FotosPropiedades
left join Propiedades on Propiedades.id = FotosPropiedades.id_propiedad
where Propiedades.expira >= 20160101;

update property
inner join (select start_at, end_at, property_id from contract order by contract.end_at) contract ON contract.property_id = property.id AND contract.end_at IS NOT NULL
set
property.status = 'PUBLISHED',
property.start_at = contract.start_at,
property.end_at = contract.end_at;

//Clients
select id, first_name, concat(last_name, ' ') as last_name, address as address1, zip as postal_code, telephone as phone, city_id as area_id, email, cellular as phone2, notes, IFNULL(date_entered, NOW()) as created_at, IFNULL(date_entered, NOW()) as updated_at from lm_clients where date_creaed >= '2015-01-01 00:00:00' order by id desc;

//Contracts
select property_id,
client_id,
IFNULL(date_created, NOW()) as created_at,
IFNULL(date_changed, NOW()) as updated_at,
contract_option as approved_at,
sellerType as type, sellerAskingPrice as sale_price, sales_price_agreed as sale_agreed, status, sellerPercentBrokerSell as sale_value, sellerPercentOwnerSell as by_owner_value, second_broker_id as secondary_account_id, deposit as option_deposit, date_expires as end_at, date_signed as start_at, sellerAskingRent as rent_price, rent_agreed, rent_start as rent_start_at, rent_end as rent_end_at, sellerRentalDeposit as rent_deposit,
 CASE
 	WHEN sellerPercentBrokerSell THEN 'Percentage'
 	ELSE 'Fixed'
 END as sale_commission
 from bl_cases
 inner join Propiedades on Propiedades.id = property_id
 WHERE property_id IS NOT NULL
 and Propiedades.expira >= 20160101 order by bl_cases.id ASC;

INSERT INTO `lead_source` (`id`, `name`, created_at, updated_at)
VALUES
	(1, 'Website RR', NOW(), NOW()),
	(2, 'Rotulo', NOW(), NOW()),
	(3, 'Clasificadosonline.com', NOW(), NOW()),
	(4, 'Clasificados.pr', NOW(), NOW()),
	(5, 'Esfera de Influencia', NOW(), NOW()),
	(6, 'El Nuevo Dia', NOW(), NOW()),
	(7, 'Compraoalquila', NOW(), NOW()),
	(8, 'Dubina', NOW(), NOW()),
	(9, 'Guia telefonica', NOW(), NOW()),
	(10, 'TV - Anuncio Subasta', NOW(), NOW()),
	(11, 'Revista Subasta', NOW(), NOW()),
	(12, 'Zillow', NOW(), NOW()),
	(13, 'Referido', NOW(), NOW()),
	(14, 'Walk In', NOW(), NOW()),
	(15, 'RealEstateBook', NOW(), NOW()),
	(16, 'La Semana', NOW(), NOW()),
	(17, 'Realtor.com', NOW(), NOW()),
	(19, 'Facebook', NOW(), NOW()),
	(20, 'Sin identificar', NOW(), NOW()),
	(21, 'Clasificados Popular', NOW(), NOW()),
	(22, 'MLS', NOW(), NOW());

//Leads
select llamadas.nombre as first_name, apellido as last_name, tel as phone, email, lead as type, source as source_id, re_propiedad_id as property_id, asignada_a as account_id, fecha_hora as created_at, fecha_hora as updated_at, notas as notes,
CASE
	WHEN lead = 'Email' THEN 'EMAIL'
	WHEN lead = 'Llamada' THEN 'CALL'
	WHEN lead = 'Mensaje' THEN 'SMS'
	WHEN lead = 'Mensaje de Texto' THEN 'SMS'
	WHEN lead = 'Mensaje de Voz' THEN 'CALL'
	WHEN lead = 'Redes Sociales' THEN 'FACEBOOK'
	WHEN lead = 'Walk-in' THEN 'WALK-IN'
	WHEN lead = 'Otro' THEN 'WALK-IN'
	ELSE 'WALK-IN'
END as type
from llamadas
left join Propiedades on Propiedades.id = re_propiedad_id
where (re_propiedad_id IS NULL OR Propiedades.expira >= 20160101);

update lead set type = 'CALL' where type = 'Llamada';
update lead set type = 'EMAIL' where type = 'Email';

//Logs
select id_llamada as lead_id, log_by as account_id, log_note as log, log_tstamp as created_at, log_tstamp as updated_at from llamadas_log;

//Offers
select broker_id as account_id,
CASE
	WHEN forma_compraventa = 'Cash' THEN 'Cash'
	WHEN forma_compraventa = 'Financiado' THEN 'Financed'
	ELSE NULL
END as method,
id_propiedad as property_id, oferta as price, cliente_nombre as first_name, cliente_tel as phone, cliente_email as email, fecha as ocurred_at, comentarios as notes, contra_oferta as counter, fecha as created_at, fecha as updated_at from rr_ofertas;

//Searches
select id as lead_id, interes_pueblo1 as area_id, pricemin as price_min, pricemax as price_max,
CASE WHEN interes_tipo = 'Casa' THEN 1
	 WHEN interes_tipo = 'Apt' THEN 7
	 WHEN interes_tipo = 'Terreno' THEN 8
	 WHEN interes_tipo = 'Comercial' THEN 46
	 ELSE NULL
END as category_id,
re_propiedad_id as property_id
from llamadas;

//Documents
select nombre as name, id_group as category_id, file as path from contratos_docs;

select id, 1 as company_id,
IF(id_padre, hijo, padre) as name,
id_padre as parent_id
FROM contratos_docs_groups;

INSERT INTO `document_category` (`id`, `company_id`, `name`, `parent_id`)
VALUES
	(1, 1, 'Reventa', NULL),
	(2, 1, 'Short Sales', NULL),
	(3, 1, 'Departamento Comercial- Reventa', NULL),
	(4, 1, 'Departamento REPO? Residencial', NULL),
	(5, 1, 'Departamento REPO-Comercial', NULL),
	(6, 1, 'Otras', NULL),
	(7, 1, 'Paquete de Listado', 1),
	(8, 1, 'Paquete de Opción', 1),
	(9, 1, 'Paquete de Arrendamiento', 1),
	(10, 1, 'Formas Misceláneas', 1),
	(11, 1, 'Formas Misceláneas', 1),
	(12, 1, 'Paquete de Listado', 2),
	(13, 1, 'Paquete de Opción', 2),
	(14, 1, 'Formas Misceláneas', 2),
	(15, 1, 'Paquete de Listado', 3),
	(16, 1, 'Paquete de Opción', 3),
	(17, 1, 'Paquete de Arrendamiento', 3),
	(18, 1, 'Formas Misceláneas', 3),
	(19, 1, 'Hoja de Inspeccion', 4),
	(20, 1, 'Perfil de Oferta', 4),
	(21, 1, 'Formas Miscelaneas', 4),
	(22, 1, 'Hoja de Inspección', 5),
	(23, 1, 'Perfil de Oferta', 5),
	(24, 1, 'Formas Miscelaneas', 5),
	(25, 1, 'Formas Administrativas', 6),
	(26, 1, 'Otras Instituciones', NULL),
	(27, 1, 'Propiedades Reposeidas', 26),
	(28, 1, 'Documentos Administrativos', 26),
	(29, 1, 'Paquete de Opción Ingles', 1);

INSERT INTO `department` (`id`, `name`, `account_id`, `email`)
VALUES
	(1, 'Reposeidas', 25497, 'santaellag@realityrealtypr.com'),
	(2, 'Reventa', NULL, NULL),
	(3, 'Short Sale', 32152, 'diazje@realityrealtypr.com'),
	(4, 'Comercial', 21393, 'lopezj@realityrealtypr.com'),
	(5, 'Proyectos Nuevos', 25497, 'santaellag@realityrealtypr.com'),
	(6, 'Relocation', NULL, 'molinarye@realityrealtypr.com'),
	(7, 'Reposubasta', NULL, 'perezg@realityrealtypr.com');

