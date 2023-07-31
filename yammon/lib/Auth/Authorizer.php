<?php

    class Auth_Authorizer{

        private static $permissions = array();
        private static $rules       = array();

        public function addRule( $requester , $permission ,  $resources = null , $value = true ){

            //Get ther permission id
            $permission = $this->findPermission( $permission );
            if( !$permission )
                throw new Exception("Invalid Permission");
            $permission_id = $permission['id'];

            //Get the requester
            if( $requester instanceof Account ){
                $account_id = $requester->id;
                $role_id    = null;
            }elseif( $requester instanceof Role ){
                $account_id = null;
                $role_id    = $requester->id;
            }else{
                $account_id = $requester;
                $role_id    = null;
            }

            //Get the resources
            if( $resources === null ){
                $resources = array( null );
            }elseif( !is_array( $resources ) ){
                $resources = array( $resources );
            }

            foreach( $resources as $resource ){

                //Get the resource
                if( $resource instanceof Doctrine_Record ){
                    $resource_class = get_class( $resource );
                    $resource_id    = array_shift( $resource->identifier() );
                }else{
                    $resource_class = $permission['resource'];
                    $resource_id    = $resource;
                }

                //Validate the resource
                if( $permission['resource'] ){

                    if( $permission['resource'] == 'Account' && $resource_class == 'Role' )
                        ;
                    elseif( $permission['resource'] != $resource_class )
                        throw new Exception('Invalid Resource');

                    if( empty( $resource_id ) )
                        throw new Exception('Resource is required');

                }else{
                    $resource_class = null;
                    $resource_id    = null;
                }

                //Find the rule
                $q = new Doctrine_Query();
                $q->from('PermissionRule');
                $q->andWhere('permission_id = ?' , $permission_id );

                if( is_null($account_id) )
                    $q->andWhere('account_id IS NULL');
                else
                    $q->andWhere('account_id = ?' , $account_id );

                if( is_null($role_id) )
                    $q->andWhere('role_id IS NULL' );
                else
                    $q->andWhere('role_id = ?' , $role_id );

                if( is_null($resource_id) )
                    $q->andWhere('resource_id IS NULL' );
                else
                    $q->andWhere('resource_id = ?' , $resource_id );

                if( is_null($resource_class) )
                    $q->andWhere('resource_class IS NULL' );
                else
                    $q->andWhere('resource_class = ?' , $resource_class );

                $Rule = $q->fetchOne();

                //Save the rule
                if( !$Rule ) $Rule = new PermissionRule();
                $Rule->permission_id  = $permission_id;
                $Rule->account_id     = $account_id;
                $Rule->role_id        = $role_id;
                $Rule->resource_id    = $resource_id;
                $Rule->resource_class = $resource_class;
                $Rule->value          = $value;

                $Rule->save();
            }

            //Clear Cache

            return true;
        }

        private static function findPermission( $id ){

            //Find permissions in the cache
            if( isset( self::$permissions[ $id ] ) )
                return self::$permissions[$id];

            //Load Permissions
            $q = new Doctrine_Query();
            $q->from('Permission');
            $permissions = $q->fetchArray();

            //Reindex permissions
            foreach( $permissions as $k => $v ){
                self::$permissions[ $v['id'] ] = $v;
            }

            if( isset( self::$permissions[ $id ] ) )
                return self::$permissions[ $id ];
            else
                return null;
        }

        public function findRules( $requester = null , $exact = true ){

            if( self::$rules )
                return self::$rules;

            $query = $this->findRulesQuery( $requester , $exact );
            self::$rules = $query->fetchArray();

            return self::$rules;
        }

        public function findRulesQuery( $requester = null , $exact = true ){

            if( $requester === null ){
                $requester = Auth::get();
            }

            //Get the account/roles from the requester
            if( $requester instanceof Role ){
                $account  = null;
                $Roles    = array( $requester );
            }else{

                if( $requester instanceof Account ){
                    $account  = $requester;
                }else{
                    $account  = Doctrine::getTable('Account')->find( $requester );
                }

                if( empty( $account ) ){
                    throw new Exception('Invalid Requester');
                }

                $Roles = array();

                //Get Account Roles
                if( !$exact ){
                    $q = new Doctrine_Query();
                    $q->from('Role');
                    $q->innerJoin('Role.Accounts');
                    $q->where('Role.Accounts.id = ?' , $account->id );
                    $AccountRoles = $q->execute();
                    foreach( $AccountRoles as $AccountRole ){
                        $Roles[] = $AccountRole;
                    }
                }
            }

            //Get the parent roles
            if( !$exact && $Roles ){
                $RelativeRoles = $this->getRelativeRoles($Roles);

                foreach( $RelativeRoles as $RelativeRole ){
                    $Roles[] = $RelativeRole;
                }
            }

            //Get Rules
            $q = new Doctrine_Query();
            $q->select('PermissionRule.* , PermissionRule.Permission.group as group , PermissionRule.Permission.resource as resource . PermissionRule.Permission.resource_name as resource_name , IF( PermissionRule.account_id , 99999 , PermissionRule.Role.rule_weight ) as weight');
            $q->from('PermissionRule');
            $q->leftJoin('PermissionRule.Role');
            $q->leftJoin('PermissionRule.Permission');
            $q->orWhere('0');
            if( $account ){
                $q->orWhere('PermissionRule.account_id = ?' , $account->id );
            }

            foreach( $Roles as $Role ){
                $q->orWhere('PermissionRule.role_id = ?' , $Role->id );
            }

            return $q;
        }

        public function hasPermission( $permission , $resource = null , $requester = null ){

            $allowed = array();
            $denied  = array();

            //Get the permission
            $permission = $this->findPermission( $permission );
            if( !$permission )
                return false;

            //Convert Resource to id
            if( $resource instanceof Doctrine_Record )
                $resource = array_shift( $resource->identifier() );

            //Check the requester
            if( $requester === null && !Auth::isLoggedIn() ){
                if( $resource === null )
                    return array();
                else
                    return false;
            }

            //Get rules
            $rules = $this->findRules( $requester , false );

            //Filter Rules / Sort By Weight
            $rules_by_weight = array();
            foreach( $rules as $i => $rule ){

                //Filter by permission
                if( $rule['permission_id'] != $permission['id'] )
                    continue;

                //Add the rule
                $weight = $rule['weight'];
                $rules_by_weight[ $weight ][] = $rule;

            }
            ksort( $rules_by_weight );

            //Check if its allowed
            foreach( $rules_by_weight as $weight => $rules ){

                $allowed_by_weight = array();
                $denied_by_weight  = array();

                foreach( $rules as $rule ){

                    $value       = $rule['value'];

                    if( $rule['resource_class'] == 'Role' ){
                        $account_ids = Auth::getAccountRolesIds( $rule['resource_id'] );
                        if( $value )
                            $allowed_by_weight = array_merge( $allowed_by_weight , $account_ids );
                        else
                            $denied_by_weight  = array_merge( $denied_by_weight , $account_ids );

                    }else{
                        $resource_id = $rule['resource_id'] ? $rule['resource_id'] : '*';

                        if( $value )
                            $allowed_by_weight[] = $resource_id;
                        else
                            $denied_by_weight[]  = $resource_id;

                    }




                }

                $allowed_by_weight = array_substract( $allowed_by_weight , $denied_by_weight );
                $allowed           = array_merge( $allowed     , $allowed_by_weight );
                $allowed           = array_substract( $allowed , $denied_by_weight );

            }

            //Return
            if( $permission['resource'] ){
                if( $resource === null )
                    $return = $allowed;
                else
                    $return = in_array( $resource , $allowed );
            }else{
                $return = in_array( '*' , $allowed );
            }

            return $return;

        }

        public function requirePermission( $permission , $resource = null , $requester = null ){

            //Make sure we are logged in
            Auth::requireLogin();

            //Check if we have permission
            $has_permission = $this->hasPermission( $permission , $resource , $requester );

            //If we dont have permission
            if( !$has_permission )
              Router::fowardForbidden();

            return true;

        }

        public function removeRule( $Rule , $all_resources = false ){

            //Find the Rule
            if( !($Rule instanceof PermissionRule ) )
                $Rule = Doctrine::getTable('PermissionRule')->find( $rule );

            //Delete Rules
            $q = new Doctrine_Query();
            $q->delete('PermissionRule');
            $q->andWhere('permission_id = ?' , $Rule->permission_id );

            if( is_null($Rule->account_id) )
                $q->andWhere('account_id IS NULL');
            else
                $q->andWhere('account_id = ?' , $Rule->account_id );

            if( is_null($Rule->role_id) )
                $q->andWhere('role_id IS NULL' );
            else
                $q->andWhere('role_id = ?' , $Rule->role_id );

            if( is_null($Rule->resource_class) )
                $q->andWhere('resource_class IS NULL' );
            else
                $q->andWhere('resource_class = ?' , $Rule->resource_class );

            if( $all_resources ){

                if( is_null($Rule->resource_id) )
                    $q->andWhere('resource_id IS NULL' );
                else
                    $q->andWhere('resource_id = ?' , $Rule->resource_id );

            }

            $q->execute();

        }

        public function addAccountToRole( $Account , $Role ){

            //Get Account/Role
            if( !($Account instanceof Account ))
                $Account = Doctrine::getTable('Account')->find( $Account );

            if( !($Role instanceof Role ))
                $Role = Doctrine::getTable('Role')->find( $Role );

            //Get Parent Roles
            $ids   = $this->getParentRolesIds( $Role );
            $ids[] = $Role->id;

            //Delete Account From Roles
            $q = new Doctrine_Query();
            $q->delete('AccountRole');
            $q->andWhere('account_id = ?' , $Account->id );
            $q->andWhereIn('role_id'  , $ids );
            $q->execute();

            //Add Account to Role
            $AccountRole = new AccountRole();
            $AccountRole->account_id = $Account->id;
            $AccountRole->role_id    = $Role->id;
            $AccountRole->save();

        }

        public function removeAccountFromRole( $Account , $Role ){

            //Get Account/Role
            if( !($Account instanceof Account ))
                $Account = Doctrine::getTable('Account')->find( $Account );

            if( !($Role instanceof Role ))
                $Role = Doctrine::getTable('Role')->find( $Role );

            //Get Children Roles
            $ids   = $this->getChildRolesIds( $Role );
            $ids[] = $Role->id;

            //Delete Account From Roles
            $q = new Doctrine_Query();
            $q->delete('AccountRole');
            $q->andWhere('account_id = ?' , $Account->id );
            $q->andWhereIn('role_id'  , $ids );
            $q->execute();
        }

        public function hasRole( $Role , $Account = null ){

            //Get Account
            if( !($Role instanceof Role ))
                $Role = Doctrine::getTable('Role')->find( $Role );

            if( $Account == null )
                $Account = Auth::get();

            if( empty( $Role ) || empty( $Account ) )
                return false;

            $ids = $this->getAccountRolesIds( $Account );
            return in_array( $Role->id , $ids );

        }

        public function getAccountRoles( $Account = null ){

            if( $Account == null )
                $Account = Auth::get();

            //Get Account
            if( !($Account instanceof Account ))
                $Account = Doctrine::getTable('Account')->find( $Account );

            $q = new Doctrine_Query();
            $q->from('Role');
            $q->innerJoin( 'Role.Accounts Acc WITH Acc.id = ?', $Account->id );
            $DirectRoles = $q->execute();

            return $DirectRoles->merge( $this->getRelativeRoles($DirectRoles) );
        }

        public function getAccountRolesIds( $Account ){
            $Roles = $this->getAccountRoles( $Account );
            $ids   = array();
            foreach( $Roles as $Role ){
                $ids[] = $Role->id;
            }
            return $ids;
        }

        public function getChildRoles( $Roles )
        {
            return $this->getRelativeRoles( $Roles, 'child' );
        }

        public function getChildRolesIds( $Role ){
            $Roles = $this->getChildRoles( $Role );
            $ids   = array();
            foreach( $Roles as $Role ){
                $ids[] = $Role->id;
            }
            return $ids;
        }

        public function getParentRoles( $Roles )
        {
            return $this->getRelativeRoles( $Roles, 'parent' );
        }

        public function getParentRolesIds( $Role ){
            $Roles = $this->getParentRoles( $Role );
            $ids   = array();
            foreach( $Roles as $Role ){
                $ids[] = $Role->id;
            }
            return $ids;
        }

        protected function getRelativeRoles( $Roles, $type = 'parent' )
        {
            //Convert to Array
            if ( !is_array( $Roles ) && !($Roles instanceof Doctrine_Collection) )
                $Roles = array( $Roles );

            //Fetch if it's numeric
            if ( is_numeric($Roles[0]) ) {
                $q = new Doctrine_Query();
                $q->from('Role');
                $q->whereIn('Role.id', $Roles );
                $q->orderBy('Role.lft ASC');
                $Roles = $q->execute();
            }

            if ( $type == 'parent' )
                $dql = 'Role.lft < ? AND Role.rgt > ?';
            else if ( $type == 'child' )
                $dql = 'Role.lft > ? AND Role.rgt < ?';

            $q = new Doctrine_Query();
            $q->from('Role');
            foreach( $Roles as $Role ){
                $q->orWhere($dql, array( $Role->lft , $Role->rgt ) );
            }

            return $q->execute();
        }

        public function getRoleAccounts( $Role ){

            //Get Role
            if( !($Role instanceof Role ))
                $Role = Doctrine::getTable('Role')->find( $Role );

            //Get Children Roles
            $ids   = $this->getChildRolesIds( $Role );
            $ids[] = $Role->id;

            $q = new Doctrine_Query();
            $q->from('Account');
            $q->innerJoin( 'Account.Roles' );
            $q->andWhereIn( 'Account.Roles.id' , $ids );
            return $q->execute();

        }

        public function getRoleAccountsIds( $Role ){
            $Accounts = $this->getRoleAccounts( $Role );
            $ids      = array();
            foreach( $Accounts as $Account ){
                $ids[] = $Account->id;
            }
            return $ids;
        }

    }
