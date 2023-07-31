<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//
//mysql_query("set names ISO-8859-1;");
//mysql_query("set character set ISO-8859-1;");

$idsBayamon = array(6,9,11,24,26,28,47,52,54,69,70,73,74);
$idsCaguas = array(4,5,7,10,13,18,19,21,23,27,30,33,36,40,44,49,53,56,63,66,77,245);

$offices = array();
$offices[23] = array(
	'office_loc' => 'Caguas',
	'office_phone' => '787-745-8792',
);
$offices[24] = array(
	'office_loc' => 'Bayamon',
	'office_phone' => '787-780-1223',
);
$offices[31] = array(
	'office_loc' => 'Humacao',
	'office_phone' => '787-285-8595',
);
$offices[49] = array(
	'office_loc' => 'San Juan',
	'office_phone' => '787-745-8777',
);
$offices[50] = array(
	'office_loc' => 'Metro',
	'office_phone' => '787-758-1933',
);

header("Content-type:application/xml; charset=UTF-8");//ISO-8859-1
header("Content-type:application/xml");

function clean_name($nombre){
	$var = str_replace(" ","-",$nombre);
	$var = str_replace("!","",$var);
	$var = str_replace(".","_",$var);
	$var = str_replace(",","",$var);
	$var = str_replace("\"","",$var);
	$var = str_replace("/","-",$var);
	$var = str_replace("(","",$var);
	$var = str_replace(")","",$var);
	return $var;
}

function e($s){
/*
$ary[] = "ASCII";
$ary[] = "JIS";
$ary[] = "EUC-JP";
return mb_detect_encoding($s, $ary);
 */
 	//$s = htmlentities($s, ENT_QUOTES, "ISO-8859-1");

	$s = str_replace("&","&amp;",$s);
	$s = str_replace("<","&lt;",$s);
	$s = str_replace(">","&gt;",$s);
	$s = str_replace("\"","&quot;",$s);
	$s = str_replace("\'","&apos;",$s);
	$s = str_replace("baño'","ba&ntilde;o",$s);
	$s = str_replace("á'","&aacute;",$s);

	return $s;
}

$q = PropertyTable::retrieveBySearch([]);

$properties = $q->execute();



//die($sql);


	//4. Start xml
	//ISO-8859-1
	echo '<listings xmlns="listings-schema">';


	foreach( $properties as $Property )
	{

		switch($Property->Category->id){
			case 5:	// Apartamentos
						$property_type  = 'Condo';
						break;
			case 1:	// Casas
						$property_type  = 'Single Family';
						break;
			case 4:	//
						$property_type = 'Multi Family';
						break;
			default:
						$property_type = 'Land';
						break;
		}

		if($Property->for_rent){
			$price = $Property->rent_price;
			$property_type  = 'Rental';
		}else{
			$price = $Property->sale_price;
		}

		if($Property->Category->type === 'Commercial'){
			continue;
		}

		echo "\t<property>\n";
			echo "\t\t<mls_id>" . e($Property->id) . "</mls_id>\n";
			echo "\t\t<title>" . e($Property->title) . "</title>\n";
			echo "\t\t<property_type>" . e($property_type) . "</property_type>\n";
			echo "\t\t<price>" . e($price) . "</price>\n";
			echo "\t\t\t<country_code>PRI</country_code>\n";
			echo "\t\t\t<region>" . e($Property->Area->region_es) . "</region>\n";
			echo "\t\t\t<city>" . e($Property->Area->city_es) . "</city>\n";
			echo "\t\t\t<state_code>" . e($$Property->Area->sate_es) . "</state_code>\n";
			echo "\t\t\t<street_address>" . e($Property->address1) . ' ' . e($Property->address2) . "</street_address>\n";
			echo "\t\t\t<zip_code>" . e($Property->postal_code) . "</zip_code>\n";
			echo "\t\t\t<latitude>" . e($Property->latitude) . "</latitude>\n";
			echo "\t\t\t<longitude>" . e($Property->longitude) . "</longitude>\n";
			echo "\t\t\t<status>Available</status>\n";
			echo "\t\t<qty_bedrooms>" . e($Property->rooms) . "</qty_bedrooms>\n";
			echo "\t\t<qty_full_bathrooms>" . e($Property->bathrooms) . "</qty_full_bathrooms>\n";
			echo "\t\t<qty_half_bathrooms>0</qty_half_bathrooms>\n";
			echo "\t\t<square_feet>" . e($Property->sqf) . "</square_feet>\n";

			$agenteInfo = ' - Agente: '.($Property->account_id ? $Property->Agent->getFullName() : '').' Cel. '.$Property->account_id ? $Property->Agent->phone : '';

			echo "\t\t<description><![CDATA[" . e('') . $agenteInfo . " ]]></description>\n";

			echo "\t\t<pictures>\n";

				$picCount = 0;
				for($i=0;$i<10;$i++){
					if( !empty($Property->getPhoto($i)) ){
						$picCount++;
					}
				}

				for($i=0;$i<$picCount;$i++){
					echo "\t\t<picture>\n";
						echo "\t\t<url>" . e($Property->getPhoto($i)->getThumborPhoto('large')) . "</url>\n";
						echo "\t\t<picture_caption></picture_caption>\n";
						echo "\t\t<display_order>" . e($i) . "</display_order>\n";
					echo "\t\t</picture>\n";
				}

			echo "\t\t</pictures>\n";


			echo "\t\t\t<agent_id>" . e($Property->account_id) . "</agent_id>\n";
			echo "\t\t\t<agent_name>" . e(($Property->account_id ? $Property->Agent->getFullName() : '')) . "</agent_name>\n";
			echo "\t\t\t<agent_email>" . e($Property->account_id ? $Property->Agent->email : '') . "</agent_email>\n";

            $r['RBizLocation'] = 'Santurce';
            $r['RBizName'] = 'Santurce';
            $r['BrokerPhone'] = $offices[49]['office_phone'];

	        echo "\t\t\t<agent_phone>" . e($r['BrokerPhone']) . "</agent_phone>\n";

			echo "\t\t\t\t<office_name>" . e($r['RBizName']) . "</office_name>\n";

			echo "\t\t\t\t<virtual_tour>" . e('http://realityrealtypr.com/p/'.$Property->id) . "</virtual_tour>\n";
			echo "\t\t\t\t<member_page_url>" . e('http://realityrealtypr.com/p/'.$Property->id) . "</member_page_url>\n";


		echo "\t</property>\n";

	}

echo "\t</listings>";
exit();
