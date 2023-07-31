<?php

/**
 * BaseBranch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $company_id
 * @property integer $area_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $phone_ext
 * @property string $phone2
 * @property string $phone2_ext
 * @property string $address1
 * @property string $address2
 * @property string $postal_code
 * @property Doctrine_Collection $Accounts
 * @property Company $Company
 * @property Area $Area
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseBranch extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('branch');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'primary' => true,
             'unsigned' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('company_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('area_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'notnull' => true,
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '255',
             ));
        $this->hasColumn('phone', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '255',
             ));
        $this->hasColumn('phone_ext', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '255',
             ));
        $this->hasColumn('phone2', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '255',
             ));
        $this->hasColumn('phone2_ext', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '255',
             ));
        $this->hasColumn('address1', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('address2', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('postal_code', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Account as Accounts', array(
             'local' => 'id',
             'foreign' => 'branch_id'));

        $this->hasOne('Company', array(
             'local' => 'company_id',
             'foreign' => 'id'));

        $this->hasOne('Area', array(
             'local' => 'area_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}