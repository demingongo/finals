<?php

namespace Novice;

class Password
{
	public static function hash($password, $options = array("cost" => PASSWORD_BCRYPT_DEFAULT_COST ), $algorithm = PASSWORD_BCRYPT)
    {
        return password_hash($password, $algorithm, $options);
    }

	public static function needsRehash($hash, $options = array("cost" => PASSWORD_BCRYPT_DEFAULT_COST ), $algorithm = PASSWORD_BCRYPT)
    {
        return password_needs_rehash($hash, $algorithm, $options);
    }
    
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

	public static function getInfo($hash)
    {
        return password_get_info($hash);
    }
}
