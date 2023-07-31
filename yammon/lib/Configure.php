<?php
/* SVN FILE: $Id$ */
/**
 * Defines Configure Class
 *
 *
 *
 * @filesource
 * @copyright     Copyright 2009, Mon Villalon
 * @package       framework
 * @subpackage    framework.core
 * @since         v1.1
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $LastChangedDate$
 */

class Configure{

    static private $values = null;

    /**
     * Used to initialized the collection in case it hasn't been
     *
     * @access public     
     */
    static private function init(){        
        if( self::$values === null ){
            self::$values = new Collection();
        }
    }

    /**
     * Get configuration as an array
     *
     * @access public     
     */
    static public function toArray(){        
		self::init();
        return self::$values->toArray( );		
    }


    /**
     * Used to check if a configuration has been set.
     *
     * @access public     
     * @param string $key the name of the configuration to check
     * @return false if the configuration is set true otherwise
     */
    static public function exists( $key ){
        self::init();
        $key = strtolower( $key );
        return self::$values->exists( $key );
    }

    /**
     * Used to create or overwrite a configuration
     *
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function write( $key , $value = null ){
        self::init();
        $key = strtolower( $key );
        return self::$values->set( $key , $value );
    }

    /**
     * Used to create or overwrite a configuration
     * but the values like on , off , TRUE , FALSE will be converted
     * to boolean
     *
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function writeBool( $key , $value = false ){
        $value = filter_var( $default , FILTER_VALIDATE_BOOLEAN );
        return self::write( $key , $value );
    }

    /**
     * Used to create or overwrite a configuration
     * but the values are cast to an int
     *
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function writeInt( $key , $value = 0 ){
        $value = (int)$value;
        return self::write( $key , $value );
    }

    /**
     * Used to a read a configuration
     * if the configuration has not been set we return the default
     * 
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function read( $key , $default = null ){
        self::init();
        $key = strtolower( $key );
        return self::$values->get( $key , $default );
    }

    /**
     * Used to read a configuration
     * and cast it to a boolean
     * values like on , off , TRUE , FALSE will be converted
     * if the configuration has not been set we return false
     *
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function readBool( $key , $default = false ){
        $default = filter_var( $default , FILTER_VALIDATE_BOOLEAN );
        $value   = parent::readBool( $key , $default );
        return filter_var( $value , FILTER_VALIDATE_BOOLEAN );       
    }

    /**
     * Used to read a configuration
     * and cast it to a int
     * if the configuration has not been set we return 0
     *
     * @access public     
     * @param string $key the name of the configuration to write
     * @param string $value the value of the configuration
     * @return the value that has just been set
     */
    static public function readInt( $key , $default = 0 ){
        $default = (int)$default;
        $value   = parent::read( $key , $default );
        return (int)$value;
    }

    /**
     * Used to write a delete a configuration
     * Remove a configuration any further isset will return false
     * 
     * @access public     
     * @param string $key the name of the configuration to delete
     * @return the value of the key before deleted
     */
    static public function delete(  $key  ){
        self::init();
        $key = strtolower( $key );
        return self::$values->delete( $key );
    }

    /**
     * Used to write a reset all configuration
     * it will remove all keys and start anew
     * 
     */
    static public function reset( ){
        self::init();
        self::$values->clear();
    }

}

