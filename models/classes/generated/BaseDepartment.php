<?php

/**
 * BaseDepartment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $company_id
 * @property integer $account_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property Company $Company
 * @property Account $Account
 * @property Doctrine_Collection $Property
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDepartment extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('department');
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
        $this->hasColumn('account_id', 'integer', 11, array(
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Company', array(
             'local' => 'company_id',
             'foreign' => 'id'));

        $this->hasOne('Account', array(
             'local' => 'account_id',
             'foreign' => 'id'));

        $this->hasMany('Property', array(
             'local' => 'id',
             'foreign' => 'department_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}