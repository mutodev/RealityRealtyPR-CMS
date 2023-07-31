<?php

class AccountTable extends Doctrine_Table_Yammon
{
    public static function cleanSession() {
        Session::delete('company_id');
        Session::delete('agency');
    }

    public static function retrieveById($id){
    	$q = new Doctrine_Query();
    	$q->from('Account a');
    	$q->andWhere('a.id = ?', $id);

    	return $q->fetchOne();
    }

    public static function secoundById($id){
    	return self::retrieveById($id);
    }

    public static function accountsByRoleForSelect($Element, $active = null) {

        static $cache = array();

        //Return cached result
        $cacheKey = md5(var_export([$active], true));

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $q = Doctrine_Query::create();
        $q->select('a.id, a.first_name, a.last_name, a.active, Roles.id, Roles.name');
        $q->from('Account a');
        $q->leftJoin('a.Roles Roles');
        $q->andWhereNotIn('Roles.id', array('system.admin', 'organization.admin'));

        //Filter by active
        if (!is_null($active)) {
            $q->andWhere('a.active = ?', (bool)$active);
        }

        $q->orderBy('a.active DESC, a.first_name, a.last_name');
        $Accounts = $q->fetchArray();

        $return = array();

        foreach($Accounts as $Account){

            $fullName = trim("{$Account['first_name']} {$Account['last_name']}");

            if (!$Account['active']){
                $return[t('Not Active')][$Account['id']] = $fullName;
            }
            else {
                $return[$Account['Roles'][0]['name']][$Account['id']] = $fullName;
            }
        }

        return $cache[$cacheKey] = $return;
    }
}
