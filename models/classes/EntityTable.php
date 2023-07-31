<?php

class EntityTable extends Doctrine_Table_Yammon
{
    public static function entitiesForSelect($Element, $type, $active = null) {

        static $cache = array();

        //Return cached result
        $cacheKey = md5(var_export([$active, $type], true));

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $q = Doctrine_Query::create();
        $q->from('Entity e');
        $q->andWhere('e.type = ?', $type);

        //Filter by active
        if (!is_null($active)) {
            $q->andWhere('a.is_active = ?', (bool)$active);
        }

        $q->orderBy('e.is_active DESC, e.name');
        $Entities = $q->fetchArray();

        $return = array();

        foreach($Entities as $Entity){

            if (!$Entity['is_active']){
                $return[t('Not Active')][$Entity['id']] = $Entity['name'];
            }
            else {
                $return[t('Active')][$Entity['id']] = $Entity['name'];
            }
        }

        return $cache[$cacheKey] = $return;
    }
}
