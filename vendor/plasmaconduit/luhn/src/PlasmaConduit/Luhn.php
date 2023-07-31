<?php
namespace PlasmaConduit;
use PlasmaConduit\Map;

class Luhn {

    static private $_map = [0,2,4,6,8,1,3,5,7,9];

    /**
     * Takes a number and calculates the Luhn checksum of it
     *
     * @param {Int} $number - The number to calculate the checksum for
     * @return {Int}        - The computed checksum
     */
    static public function checksum($number) {
        $numbers = new Map(str_split(strrev($number)));
        $sum     = $numbers->reduce(0, function($sum, $value, $key) {
            return $sum + (($key % 2) ? self::$_map[$value] : $value);
        });
        return $sum % 10;
    }

    /**
     * Given an incomplete Luhn this calculates the check digit
     *
     * @param {Int} $number - The incomplete number to derive the check digit
     * @return {Int}        - The derived check digit
     */
    static public function getCheckDigit($number) {
        $check = self::checksum($number * 10);
        return ($check == 0) ? 0 : 10 - $check;
    }

    /**
     * Given a complete Luhn this function returns true if it's valid
     *
     * @param {Int} $number - The Luhn to validate
     * @return {Boolean}    - True on valid false otherwise
     */
    static public function validate($number) {
        if (is_numeric($number)) {
            return self::checksum($number) === 0;
        } else {
            return false;
        }
    }

}