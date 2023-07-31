<?php

class PropertyTable {

    public static $defaultSearchFilters = array('per_page'                  => '15',
                                                'order_by'                  => 'price-asc',
                                                'sale_or_rent'              => '',
                                                'property_type'             => array(),
                                                'type'                      => null,
                                                'area'                      => null,
                                                'keywords'                  => null,
                                                'property_number'           => null,
                                                'price_from'                => null,
                                                'price_to'                  => null,
                                                'user_id'                   => null,
                                                'is_foreclosured'           => 0,
                                                'is_commercial'             => 0,
        'luxe' => 0
                                          );

    public static function retrieveHotListings() {
        global $ListMax;

        $query = "SELECT
            p.id, p.nombre, p.id_cat, p.precio, pa.pueblo, pa.area, p.num_interno
            FROM
                Propiedades AS p,
                FPRealtors AS fpr,
                PueblosAreas AS pa,
                Usuarios AS u,
                RBiz AS rb,
                FotosPropiedades AS f
            WHERE 1
                AND p.expira >= '".date('Ymd')."'

                AND fpr.id_usuario IN (90, 8339, 4345)
                AND fpr.num_sv = p.id

                AND p.id_pueblo = pa.id

                AND p.id = f.id_propiedad
                AND f.img1 != '0'

                AND p.id_usuario = u.id

                AND u.rbiz_id = rb.id
                AND rb.blic = '53'

            GROUP BY p.id
            ORDER BY fpr.id
            LIMIT 0,3";

        return $ListMax->query( $query );
    }

    public static function retrieveByIdPreview($id) {
        global $ListMax;

        $query = "SELECT
            p.id, p.nombre, p.id_cat, p.precio, pa.pueblo, pa.area, p.num_interno, f.img1, c.cat, p.sealquila, p.precio_renta, p.sevende
            FROM
                Propiedades AS p,
                PueblosAreas AS pa,
                Usuarios AS u,
                RBiz AS rb,
                FotosPropiedades AS f,
                Cats as c
            WHERE

                p.id_pueblo = pa.id

                AND p.id = f.id_propiedad
                AND f.img1 != '0'

                AND p.id_usuario = u.id
                AND c.id = p.id_cat

                AND u.rbiz_id = rb.id
                AND rb.blic = '53'
                AND p.id = '$id'

            GROUP BY p.id
            LIMIT 0,1";

        return $ListMax->query( $query );
    }

    public static function priceLog($id) {
        global $ListMax;

        $query = "SELECT
            precio, renta, fecha
            FROM
                rr_price_log
            WHERE
                id_propiedad = '$id'

            ORDER BY fecha DESC
            ";

        return $ListMax->query( $query );
    }

    public static function getDocumentsById($id){
        global $ListMax;

        $query = "SELECT
            *
            FROM
                PropertyDocs
            WHERE
                pid = '$id'
                AND deleted = 0

            ORDER BY id DESC
            ";

        return $ListMax->query( $query );
    }

    public static function retrieveById($id, $includeExpired = false) {
        $q = new Doctrine_Query();
        $q->from('Property p');
        $q->leftJoin('p.Category');
        $q->leftJoin('p.Area');
        $q->leftJoin('p.Contract c');
        $q->leftJoin('p.Photos Photo WITH Photo.is_approved = 1');

        if (!$includeExpired) {
//            $q->andWhere('p.status = ?', 'PUBLISHED');
            $q->andWhere('c.end_at >= ?', date('Y-m-d 00:00:00'));
        }

        $q->andWhere('p.id = ?', $id);

        return $q->fetchOne();
    }

    public static function similarProperties($Property, $limit = 3, $ignoreId) {
        $filters = array();

        if ($Property->area_id) {
            $filters['area_id'] = $Property->area_id;
        }

        $filters['property_type'] = $Property->Category->type;

        //Sale Price
        if ($Property->for_sale && $Property->sale_price) {
            $filters['price_from'] = $Property->sale_price - ($Property->sale_price * .15);
            $filters['price_to'] = $Property->sale_price + ($Property->sale_price * .15);
        }

        //Rent Price
        if ($Property->for_rent && $Property->rent_price) {
            $filters['rent_price_from'] = $Property->rent_price - ($Property->rent_price * .15);
            $filters['rent_price_to'] = $Property->rent_price + ($Property->rent_price * .15);
        }

        $Properties = [];
        $count = 0;
        $ignoreIds = [$ignoreId];
        $max = count($filters);
        do {

            $PossibleProperties = self::retrieveBySearch(array_merge($filters, ['ignore_id' => $ignoreIds, 'with_photos' => 1]))->limit($limit - $count)->execute();
            $count += count($PossibleProperties);

            if ($PossibleProperties) {
                foreach ($PossibleProperties as $PossibleProperty) {
                    $Properties[] = $PossibleProperty;
                    $ignoreIds[] = $PossibleProperty->id;
                }
            }

            $filters = array_slice($filters, 0, -1);
        } while ($count < $limit);

        return $Properties;
    }

    public static function retrieveByIds($ids) {
        $q = new Doctrine_Query();
        $q->from('Property p');
        $q->leftJoin('p.Contract c');
        $q->andWhere('p.status = ?', 'PUBLISHED');
        $q->andWhere('c.end_at >= ?', date('Y-m-d 00:00:00'));
        $q->andWhereIn('p.id', array_merge($ids, [-1]));

        return $q;
    }

    public static function getFeaturedProperties() {
        $Properties = PropertyTable::retrieveBySearch(['featured' => true])->limit(3)->orderBy('RAND()')->execute();
        return PropertyTable::retrieveBySearch(['featured' => true])->andWhereIn('p.id', $Properties->getPrimaryKeys())->orderBy('Photo.id')->execute();
    }

    public static function retrieveBySearch($filters, $Account = false) {

        $q = new Doctrine_Query();
        $q->from('Property p');
        $q->addSelect('p.*, IF(Photo.id, "1", "0") as photoIndex');
        $q->leftJoin('p.Category');
        $q->leftJoin('p.Area');
        $q->leftJoin('p.Tags Tags');
        $q->innerJoin('p.Contract c');
        $q->leftJoin('p.Agent agent');
        $q->leftJoin('p.SecondaryAgent sagent');
        $q->leftJoin('p.Photos Photo WITH Photo.is_approved = 1');
        //$q->andWhere('p.start_at <= ?', date('Y-m-d 00:00:00'));
//        $q->andWhere('p.status = ?', 'PUBLISHED');
        $q->andWhereNotIn('c.status', ['Rented', 'Closed', 'Out of Market']);
        $q->andWhere('c.end_at >= ?', date('Y-m-d 00:00:00'));
        $q->orderBy('photoIndex DESC, Photo.id ASC');

        if(@$filters['property_number']){
            $q->andWhere('p.id = ?', $filters['property_number']);
        }

        if (@$filters['sale_or_rent'] == 'sale' )
            $q->andWhere('p.for_sale = 1');
        else if (@$filters['sale_or_rent'] == 'rent' )
            $q->andWhere('p.for_rent = 1');

        if (@$filters['luxe']) {
            $q->andWhere('p.sale_price >= ?', 450000);
            $q->andWhere('p.Category.type = ?', 'Residential');
        }

        // Price
        if (@$filters['price_from'] )
            $q->andWhere('p.sale_price >= ?', $filters['price_from']);

        if (@$filters['price_to'] )
            $q->andWhere('p.sale_price <= ?', $filters['price_to']);

        if (@$filters['rent_price_from'] )
            $q->andWhere('p.rent_price >= ?', $filters['rent_price_from']);

        if (@$filters['rent_price_to'] )
            $q->andWhere('p.rent_price <= ?', $filters['rent_price_to']);

        // City
        if (@$filters['area'] )
            $q->andWhere('p.Area.name_es = ?', $filters['area']);

        if (@$filters['area_id'] )
            $q->andWhere('p.area_id = ?', $filters['area_id']);

        // Category
        if (@$filters['property_type'] ){
            $category = explode(':', $filters['property_type']);

            if (isset($category[0]) && $category[0]) {
                $q->andWhere('p.Category.type = ?', $category[0]);
            }

            if (isset($category[1]) && $category[1]) {
                $q->andWhere('p.category_id = ?', $category[1]);
            }
        }

        if (!empty($filters['featured']) ) {
            $q->andWhere('p.is_featured = ?', true);
        }

        if (@$filters['ignore_id'] ) {
            $q->andWhereNotIn('p.id', $filters['ignore_id']);
        }

        // Keywords
        if (@$filters['keywords'] ) {
            $keywords = '%'.$filters['keywords'].'%';
            $q->andWhere('p.title_en LIKE ? OR p.title_es LIKE ? OR p.id LIKE ? OR p.internal_number LIKE ? OR p.Area.name_es LIKE ?', array($keywords, $keywords, $keywords, $keywords, $keywords));
        }

        // Property Number
        if (@$filters['property_number'] )
            $q->andWhere('p.id = ?', $filters['property_number']);

        // User
        if (@$filters['user_id'] )
            $q->andWhere('p.Agent.id = ?', $filters['user_id']);

        // Reposessed
        if (@$filters['repo'] == '1' ){
            $q->andWhere('p.is_repossessed = 1');
        }
        else if (@$filters['repo'] == '0' ){
            $q->andWhere('p.is_repossessed != 1');
        }

        // Commercial
        if (@$filters['is_commercial'] == 1 )
            $q->andWhere('p.Category.type = ?', 'Commercial');

        if (@$filters['short_sale'] == '1' ){
            $q->andWhere('p.is_short_sale = 1');
        }
        else if (@$filters['repo'] == '0' ){
            $q->andWhere('p.is_short_sale != 1');
        }

        if ($Account && !$Account->show_all_properties) {
            $q->andWhere('PrimaryAgent.id = ? OR SecondaryAgent.id = ?', [$Account->id, $Account->id]);
        }

        if (@$filters['tags'] ) {
            $q->andWhereIn('Tags.slug', $filters['tags']);
        }

        if (@$filters['with_photos'] == 1 )
            $q->andWhere('Photo.id IS NOT NULL');

        return $q;
    }

    // only for RR
    public static function retrieveRRContactInfo($pid) {
        global $ListMax;
			/*
			SELECT rb.office_loc, rb.tel, rb.fax, rb.dir, rbp.id_pueblo, pa.pueblo, pa.area, rb.email_adm, p.id_cat, it.terr_comercial
                    FROM RBiz AS rb
                    INNER JOIN RBizPueblos AS rbp ON rb.id = rbp.id_rbiz
                    INNER JOIN PueblosAreas AS pa ON (rbp.id_pueblo = pa.id OR rbp.id_pueblo = pa.id_p)
                    INNER JOIN Propiedades AS p ON (p.id_pueblo = pa.id OR p.id_pueblo = pa.id_p) AND p.id = '$pid'
                    LEFT JOIN InfoTerrenos AS it ON it.id_propiedad = p.id AND p.id_cat = '6'
                    WHERE rb.blic = 53 LIMIT 1
			*/
        $query = "select id_pueblo from RBizPueblos where id_rbiz = 23";

        $rsetArray = $ListMax->query( $query );
        $idsCaguas = array();
        foreach($rsetArray AS $row){
            $idsCaguas[] = $row['id_pueblo'];
        }

        $query = "select id_pueblo from RBizPueblos where id_rbiz = 24";

        $rsetArray = $ListMax->query( $query );
        $idsBayamon = array();
        foreach($rsetArray AS $row){
            $idsBayamon[] = $row['id_pueblo'];
        }

        $query = "SELECT
            rb.office_loc,
            rb.tel AS rbtel,
            rb.fax,
            rb.dir,
            u.tel1,
            u.tel2,
            u.email AS email_adm,
            rb.email_adm AS rbemail,
            rb.id as rbid,
            p.id_cat,
            p.merchant_id,
            u.s3foto as user_foto,
            p.rr_id_departamento,
            p.id_pueblo,
            c.type,
            CONCAT(u.nombre,' ',u.apellido) AS agente,
            ri.lic
                FROM RBiz AS rb
                INNER JOIN Propiedades AS p ON p.id = '$pid'
                LEFT JOIN Categories AS c ON p.category_id = c.id
                INNER JOIN Usuarios AS u ON u.id = p.id_usuario
                LEFT JOIN RealtorsInfo AS ri ON ri.id_usuario = u.id
                WHERE rb.blic = 53 and u.rbiz_id = rb.id LIMIT 1
                ";

        $row = array();

        $rsetArray = $ListMax->query( $query );
        foreach($rsetArray AS $row){
            break;
        }

        if(($row['type'] == 'commercial' || $row['rr_id_departamento'] == 4) && $row['rr_id_departamento'] != 5){
            $row['rbtel']      = '787-745-8777';
            $row['fax']        = null;
            $row['office_loc'] = 'Comercial';
            $row['dir']        = 'Ave Fernandez Juncos 1253 Pda. 18 Santurce, PR';
            $row['rbemail']    = 'lopezj@realityrealtypr.com';
        }else{

            //Subasta
            if($row['rr_id_departamento'] == 7){
                $row['rbtel']      = '(787) 745-8777';
                $row['fax']        = null;
                $row['office_loc'] = 'Subasta';
                $row['dir']        = 'Ave Fernandez Juncos 1253 Pda. 18 Santurce, PR';
                $row['rbemail']    = 'perezg@realityrealtypr.com';
            //Relocation
            }elseif($row['rr_id_departamento'] == 6){
                $row['rbtel']      = '(787) 720-2461';
                $row['fax']        = null;
                $row['office_loc'] = 'Relocation';
                $row['dir']        = 'Ave Fernandez Juncos 1253 Pda. 18 Santurce, PR';
                $row['rbemail']    = 'molinarye@realityrealtypr.com';
            }else{

                //Reposeidas
                if($row['rr_id_departamento'] == 1){
                    $row['rbtel']      = '(787) 745-8777';
                    $row['fax']        = null;
                    $row['office_loc'] = 'Reposeídas';
                    $row['dir']        = 'Ave Fernandez Juncos 1253 Pda. 18 Santurce, PR';
                    $row['rbemail']    = 'santurce@realityrealtypr.com';
                }

                if(in_array($row['id_pueblo'], $idsCaguas)){

                    //$row['email_adm']  = 'caguas@realityrealtypr.com';
                    $row['rbtel']      = '(787) 745-8792';
                    $row['fax']        = null;
                    $row['office_loc'] = 'Caguas';
                    $row['dir']        = 'Ave Troche V12 Caguas, PR 00725';
                    $row['rbemail']    = 'caguas@realityrealtypr.com';

                }elseif( !$repo && $row['rbid'] == 23 && in_array($row['id_pueblo'], $idsBayamon)){

                    //$row['email_adm']  = 'bayamon@realityrealtypr.com';
                    $row['rbtel']      = '(787) 780-1223';
                    $row['fax']        = null;
                    $row['office_loc'] = 'Bayamón';
                    $row['dir']        = 'Edificio Eva, Calle 17 5-5 Urb. Flamboyan Gardens, Bayamon, PR 00959';
                    $row['rbemail']    = 'bayamon@realityrealtypr.com';

                //Listador Que no es Caguas
                }
            }

            if($row['rbid'] == 23 && $row['merchant_id']){
                $offices = array();
                $offices[23] = array(
                    'rbtel'      => '(787) 745-8792',
                    'fax'        => null,
                    'office_loc' => 'Caguas',
                    'dir'        => 'Ave Troche V12 Caguas, PR 00725',
                    'rbemail'    => 'caguas@realityrealtypr.com',
                );
                $offices[24] = array(
                    'rbtel'      => '(787) 780-1223',
                    'fax'        => null,
                    'office_loc' => 'Bayamón',
                    'dir'        => 'Edificio Eva, Calle 17 5-5 Urb. Flamboyan Gardens, Bayamon, PR 00959',
                    'rbemail'    => 'bayamon@realityrealtypr.com',
                );
                $offices[49] = array(
                    'rbtel'      => '(787) 522-8777',
                    'fax'        => null,
                    'office_loc' => 'San Juan',
                    'dir'        => 'Ave Fernandez Juncos 1253 Pda. 18 Santurce, PR',
                    'rbemail'    => 'santurce@realityrealtypr.com',
                );
                $offices[50] = array(
                    'rbtel'      => '787-758-1933',
                    'fax'        => null,
                    'office_loc' => 'Metro',
                    'dir'        => 'Carr. 176 Km 1.1, Ave. Ana G. Mendez, Rio Piedras, PR 00926',
                    'rbemail'    => 'metro@realityrealtypr.com',
                );

                $row = array_merge($row, $offices[$row['merchant_id']]);
            }
        }

        if($row['rr_id_departamento'] == 3){
            $row['office_loc'] = 'Short Sale';
        }elseif($row['rr_id_departamento'] == 5){
            $row['office_loc'] = 'Proyectos Nuevos';
        }elseif($row['rr_id_departamento'] == 4){
            $row['office_loc'] = 'Comercial';
        }

        return $row;
    }

    public static function getCategories($type = null)
    {
        $q = new Doctrine_Query();
        $q->from('PropertyCategory');
        if ($type) {
            $q->andWhere('type = ?', ucfirst($type));
        }
        $Categories = $q->execute();

        $return = [];

        foreach($Categories as $Category){
            $item = array(
                'label' => $Category->name,
                'id'    => $Category->id,
            );

            $return[$Category['type']][$Category['id']] = $item;
        }

        return $return;
    }

    public static function url_slug($string,$space="-") {
        $string = trim($string);
        $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
        $string = preg_replace("/\s+/", $space , $string);
        $string = strtolower($string);
        $string = urlencode( $string );
        return $string;
    }

    public static function url_property( $Property ){

        //return '/properties/view?id='.$Property->id.'&'.http_build_query($_GET);

        $url = array();

        if( $Property->for_sale ){
            $url[] = t("compra-venta");
        }elseif ($Property->for_rent){
            $url[] = t("alquiler-renta");
        }

        $url[] = urlencode(strtolower($Property->Category->name));
        $url[] = "puerto-rico";
        $url[] = urlencode(strtolower(self::url_slug($Property->Area->name)));


        $url[] = self::url_slug($Property->title);
        $url[] = $Property->id;

        unset($_GET['id']);

        return "/".implode( "/" , $url  )."/".'?'.http_build_query($_GET);

    }

    public static function title_property( $Property ){

        $title = array();

        $title[] = $Property->title;

        if( $Property->for_sale && !$Property->for_rent){
            $title[] = "Compra y Venta de";
        }elseif( !$Property->for_sale && $Property->for_rent){
            $title[] = "Alquiler y Renta de";
        }else{
            $title[] = "Compra y Venta o Alquiler y Renta de";
        }

        $title[] = $Property->Category->name;
        $title[] = "en " . $Property->Area->name .", Puerto Rico";

        $title[] = "| Bienes Raices Puerto Rico";

        return implode( " " , $title );

    }

    public static function title_search( $filters ){

        $title = array();

        if(@$filters['sale_or_rent'] == 'sale' ){
            $title[] = "Compra y Venta";
        }elseif(@$filters['sale_or_rent'] == 'rent' ){
            $title[] = "Alquiler y Renta";
        }else{
            $title[] = "Compra y Venta o Alquiler y Renta";
        }

        if(@$filters['is_commercial'] == 1 ){
            $title[] = "Comercial";
        }

        if(@$filters['repo'] != '' ){
            $title[] = "Reposeidas";
        }

        if(@$filters['property_type'] ){
            $q = new Doctrine_Query();
            $q->from('PropertyCategory');
            $q->andWhere('id = ?', $filters['property_type']);
            $Category = $q->fetchOne();

            if ($Category) {
                $title[] = "de " . $Category->name;
            }
        }

        if(@$filters['area'] ){
            $title[] = "en ".$filters['area']." Puerto Rico";
        }

        $title[] = "| Bienes Raices en Puerto Rico";

        return implode( " " , $title );

    }

}
?>
