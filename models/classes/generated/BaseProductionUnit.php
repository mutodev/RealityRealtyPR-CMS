<?php

/**
 * BaseProductionUnit
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property boolean $is_active
 * @property Doctrine_Collection $Property
 * @property Doctrine_Collection $Goals
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseProductionUnit extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('production_unit');
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
        $this->hasColumn('is_active', 'boolean', null, array(
             'type' => 'boolean',
             'default' => true,
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Property', array(
             'local' => 'id',
             'foreign' => 'production_unit_id'));

        $this->hasMany('Goal as Goals', array(
             'local' => 'id',
             'foreign' => 'production_unit_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}