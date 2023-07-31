<?php

/**
 * BaseProperty
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $company_id
 * @property integer $contract_id
 * @property integer $department_id
 * @property integer $area_id
 * @property integer $account_id
 * @property integer $secondary_account_id
 * @property integer $short_sale_bank_id
 * @property integer $relocation_company_id
 * @property integer $repossessed_bank_id
 * @property integer $repossessed_investor_id
 * @property integer $production_unit_id
 * @property string $source
 * @property string $source_id
 * @property boolean $for_sale
 * @property decimal $sale_price
 * @property enum $sale_price_type
 * @property boolean $for_rent
 * @property decimal $rent_price
 * @property enum $rent_price_type
 * @property boolean $is_rent_long_term
 * @property boolean $is_rent_short_term
 * @property boolean $is_rent_time_share
 * @property boolean $is_short_sale
 * @property boolean $public_coordinates
 * @property string $short_sale_loan_number
 * @property boolean $is_relocation
 * @property boolean $is_resale
 * @property boolean $is_repossessed
 * @property timestamp $repossessed_at
 * @property string $address1
 * @property string $address2
 * @property string $postal_code
 * @property integer $year_built
 * @property boolean $maintenance
 * @property decimal $maintenance_price
 * @property boolean $maintenance_complex
 * @property decimal $maintenance_complex_price
 * @property string $exemption
 * @property string $exemption_comment
 * @property string $latitude
 * @property string $catastro
 * @property string $longitude
 * @property enum $financing_rural
 * @property enum $financing_fha
 * @property enum $financing_conventional
 * @property enum $financing_vivienda
 * @property enum $financing_cash
 * @property enum $financing_loan_with_repairs
 * @property enum $financing_private
 * @property enum $financing_assume_mortgage
 * @property enum $status
 * @property timestamp $start_at
 * @property timestamp $end_at
 * @property integer $bathrooms
 * @property integer $rooms
 * @property decimal $sqf
 * @property decimal $sqm
 * @property decimal $cuerdas
 * @property string $internal_number
 * @property string $cpr_number
 * @property string $co_number
 * @property string $mls_number
 * @property string $zonification
 * @property Text $internal_notes
 * @property Text $showing_instructions
 * @property string $key_number
 * @property string $key_box
 * @property boolean $have_parkings
 * @property integer $parkings
 * @property integer $floors
 * @property string $flood_zone
 * @property string $mixed_use_commercial
 * @property string $mixed_use_residential
 * @property Text $extra
 * @property boolean $is_optioned
 * @property boolean $lead_required
 * @property integer $lead_year
 * @property boolean $is_featured
 * @property PropertyCategory $Category
 * @property Company $Company
 * @property Contract $Contract
 * @property Department $Department
 * @property Area $Area
 * @property Account $Agent
 * @property Account $SecondaryAgent
 * @property Entity $ShortSaleBank
 * @property Entity $RelocationCompany
 * @property Entity $RepossessedBank
 * @property Entity $RepossessedInvestor
 * @property ProductionUnit $ProductionUnit
 * @property Doctrine_Collection $Conditions
 * @property Doctrine_Collection $Tags
 * @property Doctrine_Collection $PropertyConditionRelation
 * @property Doctrine_Collection $PropertyTagRelation
 * @property Doctrine_Collection $Photos
 * @property Doctrine_Collection $Units
 * @property Doctrine_Collection $Offers
 * @property Doctrine_Collection $PropertyStat
 * @property Doctrine_Collection $PriceLogs
 * @property Doctrine_Collection $Lead
 * @property Doctrine_Collection $PropertyLeadRelation
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseProperty extends Doctrine_Record_Yammon
{
    public function setTableDefinition()
    {
        $this->setTableName('property');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'primary' => true,
             'unsigned' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('category_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => 1,
             ));
        $this->hasColumn('company_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => 1,
             ));
        $this->hasColumn('contract_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => 1,
             ));
        $this->hasColumn('department_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('area_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('account_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('secondary_account_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('short_sale_bank_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('relocation_company_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('repossessed_bank_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('repossessed_investor_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('production_unit_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => 11,
             'unsigned' => true,
             ));
        $this->hasColumn('source', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('source_id', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('for_sale', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('sale_price', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 2,
             ));
        $this->hasColumn('sale_price_type', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'SQF',
              1 => 'SQM',
              2 => 'CUERDAS',
             ),
             ));
        $this->hasColumn('for_rent', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('rent_price', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 2,
             ));
        $this->hasColumn('rent_price_type', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'SQF',
              1 => 'SQM',
              2 => 'CUERDAS',
             ),
             ));
        $this->hasColumn('is_rent_long_term', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('is_rent_short_term', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('is_rent_time_share', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('is_short_sale', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('public_coordinates', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('short_sale_loan_number', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('is_relocation', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('is_resale', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('is_repossessed', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('repossessed_at', 'timestamp', null, array(
             'type' => 'timestamp',
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
        $this->hasColumn('year_built', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             ));
        $this->hasColumn('maintenance', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('maintenance_price', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 2,
             ));
        $this->hasColumn('maintenance_complex', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('maintenance_complex_price', 'decimal', 10, array(
             'type' => 'decimal',
             'length' => 10,
             'scale' => 2,
             ));
        $this->hasColumn('exemption', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('exemption_comment', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('latitude', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('catastro', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('longitude', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('financing_rural', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_fha', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_conventional', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_vivienda', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_cash', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_loan_with_repairs', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_private', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('financing_assume_mortgage', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'YES',
              1 => 'NO',
             ),
             ));
        $this->hasColumn('status', 'enum', null, array(
             'type' => 'enum',
             'values' =>
             array(
              0 => 'PUBLISHED',
              1 => 'UNPUBLISHED',
             ),
             'default' => 'UNPUBLISHED',
             ));
        $this->hasColumn('start_at', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('end_at', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('bathrooms', 'decimal', 12, array(
            'type' => 'decimal',
            'length' => '12',
            'scale' => '1',
             ));
        $this->hasColumn('rooms', 'integer', 10, array(
             'type' => 'integer',
             'length' => 10,
             ));
        $this->hasColumn('sqf', 'decimal', 12, array(
             'type' => 'decimal',
             'length' => '12',
             'scale' => '2',
             ));
        $this->hasColumn('sqm', 'decimal', 12, array(
             'type' => 'decimal',
             'length' => '12',
             'scale' => '2',
             ));
        $this->hasColumn('cuerdas', 'decimal', 12, array(
             'type' => 'decimal',
             'length' => '12',
             'scale' => '2',
             ));
        $this->hasColumn('internal_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('cpr_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('co_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('mls_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('zonification', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('internal_notes', 'Text', null, array(
             'type' => 'Text',
             ));
        $this->hasColumn('showing_instructions', 'Text', null, array(
             'type' => 'Text',
             ));
        $this->hasColumn('key_number', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('key_box', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('have_parkings', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('parkings', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('floors', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('flood_zone', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('mixed_use_commercial', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('mixed_use_residential', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('extra', 'Text', null, array(
             'type' => 'Text',
             ));
        $this->hasColumn('is_optioned', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('lead_required', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('lead_year', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('is_featured', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('PropertyCategory as Category', array(
             'local' => 'category_id',
             'foreign' => 'id'));

        $this->hasOne('Company', array(
             'local' => 'company_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Contract', array(
             'local' => 'contract_id',
             'foreign' => 'id',
             'onDelete' => 'SET NULL',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Department', array(
             'local' => 'department_id',
             'foreign' => 'id'));

        $this->hasOne('Area', array(
             'local' => 'area_id',
             'foreign' => 'id'));

        $this->hasOne('Account as Agent', array(
             'local' => 'account_id',
             'foreign' => 'id'));

        $this->hasOne('Account as SecondaryAgent', array(
             'local' => 'secondary_account_id',
             'foreign' => 'id'));

        $this->hasOne('Entity as ShortSaleBank', array(
             'local' => 'short_sale_bank_id',
             'foreign' => 'id'));

        $this->hasOne('Entity as RelocationCompany', array(
             'local' => 'relocation_company_id',
             'foreign' => 'id'));

        $this->hasOne('Entity as RepossessedBank', array(
             'local' => 'repossessed_bank_id',
             'foreign' => 'id'));

        $this->hasOne('Entity as RepossessedInvestor', array(
             'local' => 'repossessed_investor_id',
             'foreign' => 'id'));

        $this->hasOne('ProductionUnit', array(
             'local' => 'production_unit_id',
             'foreign' => 'id'));

        $this->hasMany('PropertyCondition as Conditions', array(
             'refClass' => 'PropertyConditionRelation',
             'local' => 'property_id',
             'foreign' => 'condition_id'));

        $this->hasMany('PropertyTag as Tags', array(
             'refClass' => 'PropertyTagRelation',
             'local' => 'property_id',
             'foreign' => 'tag_id'));

        $this->hasMany('PropertyConditionRelation', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyTagRelation', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyPhoto as Photos', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyUnit as Units', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyOffer as Offers', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyStat', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyPriceLog as PriceLogs', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('Lead', array(
             'local' => 'id',
             'foreign' => 'property_id'));

        $this->hasMany('PropertyLeadRelation', array(
             'local' => 'id',
             'foreign' => 'property_id'));

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
              'title' =>
              array(
              'type' => 'string',
              'length' => 255,
              'notnull' => true,
              ),
              'description' =>
              array(
              'type' => 'text',
              ),
              'flyer_description' =>
              array(
              'type' => 'text',
              ),
             ),
             ));
        $this->actAs($timestampable0);
        $this->actAs($translatable0);
    }
}