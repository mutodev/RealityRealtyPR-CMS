<?php

/**
 * BaseOrganization
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $timezone
 * @property integer $agency_id
 * @property boolean $is_active
 * @property Doctrine_Collection $Accounts
 * @property Doctrine_Collection $Companies
 * @property Agency $Agency
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseOrganization extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('organization');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'primary' => true,
             'unsigned' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('timezone', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'default' => 'America/Puerto_Rico',
             'length' => '255',
             ));
        $this->hasColumn('agency_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('is_active', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Account as Accounts', array(
             'local' => 'id',
             'foreign' => 'organization_id'));

        $this->hasMany('Company as Companies', array(
             'local' => 'id',
             'foreign' => 'organization_id'));

        $this->hasOne('Agency', array(
             'local' => 'agency_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}