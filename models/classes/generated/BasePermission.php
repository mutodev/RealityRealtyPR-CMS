<?php

/**
 * BasePermission
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $id
 * @property string $group
 * @property string $name
 * @property string $description
 * @property string $resource
 * @property string $resource_name
 * @property Doctrine_Collection $Account
 * @property Doctrine_Collection $Role
 * @property Doctrine_Collection $PermissionRule
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasePermission extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('permission');
        $this->hasColumn('id', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'primary' => true,
             ));
        $this->hasColumn('group', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'notnull' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('resource', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('resource_name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Account', array(
             'refClass' => 'PermissionRule',
             'local' => 'permission_id',
             'foreign' => 'account_id'));

        $this->hasMany('Role', array(
             'refClass' => 'PermissionRule',
             'local' => 'permission_id',
             'foreign' => 'role_id'));

        $this->hasMany('PermissionRule', array(
             'local' => 'id',
             'foreign' => 'permission_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}