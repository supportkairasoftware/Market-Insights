<?php

use \stdClass;

class Authentication
{
	var $itoa64;
    var $iteration_count_log2;
    var $portable_hashes;
    var $random_state;

    function __construct($iteration_count_log2, $portable_hashes)
    {
        $this->itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
            $iteration_count_log2 = 8;
        $this->iteration_count_log2 = $iteration_count_log2;

        $this->portable_hashes = $portable_hashes;

        $this->random_state = microtime();
        if (function_exists('getmypid'))
            $this->random_state .= getmypid();
    }

    function CheckPassword($password, $stored_hash)
    {

        $hash = $this->crypt_private($password, $stored_hash);

        if ($hash[0] == '*') {
            $hash = crypt($password, $stored_hash);
        }
        return $hash == $stored_hash;
    }

    function crypt_private($password, $setting)
    {
        $output = '*0';
        //print_r($password);
        if (self::getSubString($setting, 0, 2) == $output)
            $output = '*1';

        $id = self::getSubString($setting, 0, 3);

        # We use "$P$", phpBB3 uses "$H$" for the same thing
        if ($id != '$P$' && $id != '$H$')
            return $output;
    }

    public static function getSubString($string='', $start=0, $length=null) {

        if (function_exists('mb_substr')) {
            if ($length !== null) {
                $substring = mb_substr($string, $start, $length, 'UTF-8');
            } else {
                $substring = mb_substr($string, $start, self::getLength($string), 'UTF-8');
            }
        } else {
            if ($length !== null) {
                $substring = substr($string, $start, $length);
            } else {
                $substring = substr($string, $start);
            }
        }
        //print_r($substring);
        return $substring;
    }
}