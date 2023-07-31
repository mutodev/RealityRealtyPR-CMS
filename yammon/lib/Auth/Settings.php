<?php
/*
 *  $Id:$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.yammon.com>.
 */
 
/**
* Auth_Settings
*
* Contains the base class for managing setting thru the 
* authentication sub system
*
* LICENSE: Some license information
*
* @category   Yammon
* @package    Yammon
* @subpackage Auth
* @author     Mon Villalon <mon@listmax.com>
* @copyright  2010 MA WEB GROUP
* @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
* @version    $Revision:$
* @link       www.yammon.com
* @since      1.0
*/
class Auth_Settings{

    private static $accounts = array();
    private static $roles = array();
    private static $accountDirectRoles = array();
    private static $accountAllRoles = array();
    private static $settings      = array();
    private static $settingRules  = array();
    private static $settingValues = array();
    
    
    protected function findAccount( $id )
    {
        if( isset( self::$accounts[ $id ] ) )
            return self::$accounts[ $id ];
            
        $account = Doctrine::getTable('Account')->find( $id );
        return self::$accounts[ $id ] = $account;
    }
    
    /**
     * findAccountId
     *
     * Transforms a requester into the an account id.
     * A requester is a representation of an account or role
     *
     * Possible formats for a requester are:
     *   + null    if no requester is passed and auto is set to true
     *             it will asume the currently logged in account
     *
     *   + int     if an integer is passed and auto is set to true
     *             it will assume it is an account_id and verify
     *             that the account exists 
     *
     *   + Account if an account object is passed it will extract
     *             the account_id from the object, it will assume
     *             the account exists
     *
     *   + Array   if an array of the form array( 'Account' , $id )
     *             is passed it will use the account_id from the 
     *             array and verify that the account exists
     *
     * @param  mixed  $requester see description for details
     * @param  int    $auto      see description for details ( default true )
     * @return int               the account_id for the requester or null if not found
    */
    public function findAccountId( $requester = null , $auto = true )
    {
            
        $account_id = null;
        
        if( $auto && $requester === null ){

            //If no requester was given
            //we assume the currently logged in
            //user
            $requester  = Auth::get();
            $account_id = $requester ? $requester->id : null;
            
        }elseif( $auto && is_numeric( $requester ) ){
        
            $requester = $this->findAccount( $requester );                
            $account_id = $requester ? $requester->id : null;
            
        }elseif( $requester instanceof Account ){
        
            //If an account object was given
            //we extract the id from it            
            $account_id = $requester->id;
            
        }elseif( is_array( $requester ) 
                 && count( $requester ) == 2 
                 && $requester[0] == 'Account'
                 && is_numeric( $requester[1] ) )
        {
            
            //If an array was given we check
            //that its of the format array( 'Account' , $id )
            //and get the id from the account object
            $requester = $this->findAccount( $requester[1] );                
            $account_id = $requester ? $requester->id : null;
            
        }
        
        return $account_id;
        
    }    
    
    protected function findAccountRoleIds( $account_id , $direct = false )
    {
                
        //Check the cache
        if( $direct && isset( self::$accountDirectRoles[ $account_id ] ) ){
            return self::$accountDirectRoles[ $account_id ];
        }elseif( !$direct && isset( self::$accountAllRoles[ $account_id ] ) ){
            return self::$accountAllRoles[ $account_id ];
        }
                
        //Get the roles for the account
        $q = new Doctrine_Query();
        $q->from('AccountRole');
        $q->innerJoin('AccountRole.Role');        
        $q->andWhere('account_id = ?' , $account_id );
        $q->orderBy('lft');
        $direct_roles = $q->execute();

        //Pluck the ids for the direct roles
        $direct_role_ids = array();
        foreach( $direct_roles as $direct_role ){
            $id = $direct_role['role_id'];
            self::$roles[ $id ] = $direct_role['Role'];
            $direct_role_ids[]  = $direct_role['role_id'];
        }
                
        //If we only the direct roles return them
        self::$accountDirectRoles[ $account_id ] = $direct_role_ids;
        if( $direct ){
            return $direct_role_ids;
        }
                
        //Get the parent roles
        $q = new Doctrine_Query();
        $q->from('Role');
        $q->orWhere('0');
        foreach( $direct_roles as $direct_role ){
            $q->orWhere('Role.lft <= ? AND Role.rgt >= ?' , array( $direct_role['Role']['lft'] , $direct_role['Role']['rgt'] ) );
        }
        $q->orderBy('lft');
        $all_roles = $q->execute();

        //Pluck the ids for the roles
        $role_ids = array();
        foreach( $all_roles as $role )
        {
            $id = $role['id'];
            self::$roles[ $id ] = $role;
            $role_ids[] = $role['id'];
        }

        //Return them
        self::$accountAllRoles[ $account_id ] = $role_ids;        
        return $role_ids;

    }    
    
    protected function findRole( $id )
    {    
        if( isset( self::$roles[ $id ] ) )
            return self::$roles[ $id ];    
    
        $role = Doctrine::getTable('Role')->find( $id );
        return self::$roles[ $id ] = $role;
    }
    
    protected function findRoleParentIds( $id )
    {

        $base = $this->findRole( $id );
        if( empty( $base ) ) return array();
                
        $q = new Doctrine_Query();
        $q->from('Role');
        $q->andWhere('Role.lft < ? AND Role.rgt > ?' , array( $base->lft , $base->rgt ) );
        $roles = $q->execute();
        
        $return = array();
        $first  = false;
        foreach( $roles as $role ){
            $id = $role->id;
            self::$roles[ $id ] = $role;
            $return[] = $id;
        }
        
        return $return;

    }
    
    protected function findSetting( $id )
    {
        if( isset( self::$settings[ $id ] ) )
            return self::$settings[ $id ];    
    
        $setting = Doctrine::getTable('Setting')->find( $id );
        return self::$settings[ $id ] = $setting;       
    }
        

    
    /**
     * findRoleId
     *
     * Transforms a requester into the a role_id.
     * A requester is a representation of an account or role
     *
     * Possible formats for a requester are:
     *   + null    if no requester is passed and auto is set to true
     *             it will asume the root role
     *
     *   + int     if an integer is passed and auto is set to true
     *             it will assume it is an account_id and verify
     *             that the role exists 
     *
     *   + Role    if an account object is passed it will extract
     *             the account_id from the object, it will assume
     *             the role exists
     *
     *   + Array   if an array of the form array( 'Account' , $id )
     *             is passed it will use the account_id from the 
     *             array and verify that the account exists
     *
     * @param  mixed  $requester see description for details
     * @param  int    $auto      see description for details ( default false )
     * @return int               the account_id for the requester or null if not found
    */    
    public function findRoleId( $requester = null , $auto = false ){
        
        $role_id = null;
    
        if( $auto && $requester === null ){

            //@todo find the root role
            $role_id = null;
            
        }elseif( $auto && is_numeric( $requester ) ){
        
            //If the requester is a number
            //we assume is an account id
            $requester = $this->findRole( $requester );
            $role_id = $requester ? $requester->id : null;
                        
        }elseif( $requester instanceof Role ){
        
            //If an account object was given
            //we extract the id from it            
            $role_id = $requester->id;
            
        }elseif( is_array( $requester ) 
                 && count( $requester ) == 2 
                 && $requester[0] == 'Role'
                 && is_numeric( $requester[1] ) )
        {
            
            //If an array was given we check
            //that its of the format array( 'Role' , $id )
            //and get the id from the account object
            $requester = $this->findRole( $requester[1] );
            $role_id = $requester ? $requester->id : null;
            
        }
        
        return $role_id;
    
    }
    
    public function findRuleWeight( $rule ){
    
        static $cache = array();
                
        $role_id    = $rule['role_id'];
        $account_id = $rule['account_id'];
        
        if( $account_id ){
            return PHP_INT_MAX;
        }
        
        if( isset( $cache[ $role_id ] ) ){
            return $cache[ $role_id ];
        }
        
        $parent_ids = $this->findRoleParentIds( $role_id );
        $role       = $this->findRole( $role_id );
        
        $parent_weight = 0;
        foreach( $parent_ids as $parent_id ){
            $parent_role   = $this->findRole( $parent_id );
            $parent_weight += (int)$parent_role['rule_weight'];
        }
        $weight = $parent_weight + (int)$role['rule_weight'] + $role['level'];
        return $cache[ $role_id ] = $weight;
        
    }
    
    /**
     * findSettingRules
     *
     * Find the rules specified for a specific setting
     *
     * @param  string $setting   the name of the setting
     * @return array             an array of rules  
    */  
    public function findSettingRules( $setting , $requester = null , $exact = false )
    {
                                          
        //Check cache
        $setting_key   = serialize( $setting );        
        $requester_key = serialize( $requester );
        if( isset( self::$settingRules[ $setting_key ][ $requester_key ][ $exact ] ) ){
            return self::$settingRules[ $setting_key ][ $requester_key ][ $exact ];
        }                     
                     
        //Get the Setting
        $setting    = $this->findSetting( $setting );
        
        //Make sure the setting exists
        if( !$setting ) 
            return array();
        
        //Get the requester Information
        $account_id = $this->findAccountId( $requester );
        $role_id    = $this->findRoleId( $requester );

        //Make sure we have a valid requester
        if( !$account_id && !$role_id ){
            return array();
        }

        //Get the role_ids
        $role_ids = array();
        if( !$exact ){
            if( $account_id ){
                $role_ids = $this->findAccountRoleIds( $account_id );
            }else{
                $role_ids = $this->findRoleParentIds( $role_id );
            }
        }
                                                    
        //Fetch the rules
        $condition   = array();
        $args        = array();
                                
        $q = new Doctrine_Query();
        $q->from('SettingRule');
        $q->andWhere('setting_id = ?'  , $setting->id );
                
        if( $account_id ){
            $condition[] = 'account_id = ?';
            $args[]      = $account_id;
        }            

        if( $role_id ){        
            $condition[] = 'role_id = ?';        
            $args[]      = $role_id;
        }            
        
        if( $role_ids ){
            $condition[]  = 'role_id IN ('.implode( ',' , $role_ids ).')';
        }
        
        if( $condition ){
            $condition = '('.implode( ' OR ' , $condition ).')';
            $q->andWhere( $condition , $args );
        }
                
        $raw_rules = $q->fetchArray();
        $rules     = array();                

        foreach( $raw_rules as $rule ){
            $rule_weight = $this->findRuleWeight( $rule );
            $rules[ $rule_weight ][] = $rule;
        }
               
        //Sort Rules by Weight
        krsort( $rules );
               
        return self::$settingRules[ $setting_key ][ $requester_key ][ $exact ] = $rules;
        
    }

    public function getSetting( $setting_id , $requester = null , $exact = false )
    {
        
        //Check the cache
        $requester_key = serialize( $requester );
        if( isset( self::$settingValues[ $setting_id ][ $requester_key ][ $exact ] ) ){
            return self::$settingValues[ $setting_id ][ $requester_key ][ $exact ];
        }
    
        //Find the setting
        $setting = $this->findSetting( $setting_id );
        if( $setting === null )
            return null;

        //Get setting information
        $resolution = $setting['resolution'];
        $default    = $setting['default'];
                
        //Get the setting rules
        $rules = $this->findSettingRules( $setting_id , $requester , $exact );
        
        //Get the exact value
        if( $exact ){
            $value = null;
            $rule  = array_shift( $rules );
            if( $rule ) $rule  = array_shift( $rule );
            if( $rule ) $value = $rule['value'];
            return self::$settingValues[ $setting_id ][ $requester_key ][ $exact ] = $value;
        }

        //Evaluate the rule
        switch( $resolution ){
            case 'max':     //Find the max value                            
                            $max_value = null;
                            foreach( $rules as $weight => $rulesw ){
                                foreach( $rulesw as $rule ){
                                    if( $max_value === null || $rule['value'] > $max_value ){
                                        $value     = $rule['value'];
                                        $max_value = $value;
                                    }                                        
                                }
                            }

                            if( $max_value === null )
                                $value = $default;

                            break;
                            
            case 'min':     //Find the min value                            
                            $min_value = null;
                            foreach( $rules as $weight => $rulesw ){
                                foreach( $rulesw as $rule ){
                                    if( $min_value === null || $rule['value'] < $min_value ){
                                        $value     = $rule['value'];
                                        $min_value = $value;
                                    }                                        
                                }
                            }
                            
                            if( $min_value === null )
                                $value = $default;

                            break; 
                            
            case 'sum':     //Sum the values                            
                            $sum_value = null;
                            foreach( $rules as $weight => $rulesw ){
                                foreach( $rulesw as $rule ){
                                    $sum_value += $rule['value'];
                                }
                            }

                            if( $sum_value === null )
                                $value = $default;
                            else
                                $value = $sum_value;

                            break;

            case 'all':     //Get all values                            
                            $all_values = array();
                            foreach( $rules as $weight => $rulesw ){
                                foreach( $rulesw as $rule ){
                                    $all_values[] = $rule['value'];
                                }
                            }

                            if( !$all_values )
                                $value = array( $default );
                            else
                                $value = $all_values;
                            
                            break;

            case 'weight':  //Find the value with greater weight
            default:        $value = $default;
                            $rule  = array_shift( $rules );
                            if( $rule ) $rule  = array_shift( $rule );
                            if( $rule ) $value = $rule['value'];
                            
                            break;
    
            
        }

    
        //Return the value
        return self::$settingValues[ $setting_id ][ $requester_key ][ $exact ] = $value;
        
    }

    public function setSetting( $setting_id , $value = null , $requester = null ){
    
        //Find the setting
        $setting = $this->findSetting( $setting_id );
        if( $setting === null )
            return null;
    
        //Get the requester Information
        $account_id = $this->findAccountId( $requester );
        $role_id    = $this->findRoleId( $requester );
        
        //Make sure the requester exists
        if( !$account_id && !$role_id ){
            return null;
        }        
        
        //Delete previous rule
        $q = new Doctrine_Query();
        $q->delete('SettingRule');
        $q->andWhere('setting_id = ?' , $setting_id );
        if( $account_id )
            $q->andWhere( 'account_id = ?' , $account_id );
        if( $role_id )
            $q->andWhere( 'role_id = ?'    , $role_id );
        $q->execute();
        
        if( $value ){
            $SettingRule = new SettingRule();
            $SettingRule->account_id = $account_id;
            $SettingRule->role_id    = $role_id;
            $SettingRule->setting_id = $setting_id;
            $SettingRule->value      = $value;
            $SettingRule->save();
        }            
        
        //Clear the settings cache
        self::$settingValues = array();
        self::$settingRules  = array();        
    
    }

}