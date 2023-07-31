<?php

/**
 * BasePropertyCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property enum $type
 * @property integer $parent_id
 * @property Doctrine_Collection $Property
 * @property Doctrine_Collection $LeadSearch
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePropertyCategory extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('property_category');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'primary' => true,
             'unsigned' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('type', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'Residential',
              1 => 'Commercial',
              2 => 'Land',
             ),
             ));
        $this->hasColumn('parent_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'primary' => true,
             'unsigned' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Property', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $this->hasMany('LeadSearch', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $translatable0 = new Doctrine_Template_Translatable(array(
             'languages' => 
             array(
              0 => 'en',
              1 => 'es',
             ),
             'fields' => 
             array(
              'name' => 
              array(
              'type' => 'string',
              'length' => 255,
              ),
             ),
             ));
        $this->actAs($timestampable0);
        $this->actAs($translatable0);
    }
}