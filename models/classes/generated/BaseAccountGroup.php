<?php

/**
 * BaseAccountGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $company_id
 * @property string $name
 * @property Company $Company
 * @property Doctrine_Collection $Accounts
 * @property Doctrine_Collection $AccountGroupRelation
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAccountGroup extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('account_group');
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
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Company', array(
             'local' => 'company_id',
             'foreign' => 'id'));

        $this->hasMany('Account as Accounts', array(
             'refClass' => 'AccountGroupRelation',
             'local' => 'group_id',
             'foreign' => 'account_id'));

        $this->hasMany('AccountGroupRelation', array(
             'local' => 'id',
             'foreign' => 'group_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}