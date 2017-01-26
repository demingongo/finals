<?php

namespace Novice;

class PasswordChecker
{
	private $password;

	public function __construct($password)
    {
		$this->password = $password;
    }

	public function getPassword()
    {
        return $this->password;
    }

	public function hash($options = array("cost" => PASSWORD_BCRYPT_DEFAULT_COST ), $algorithm = PASSWORD_BCRYPT)
    {
        return password_hash($this->password, $algorithm, $options);
    }

	public function verify($hash)
    {
        return password_verify($this->password, $hash);
    }

	public function needsRehash($hash, $options = array("cost" => PASSWORD_BCRYPT_DEFAULT_COST ), $algorithm = PASSWORD_BCRYPT)
    {
        return password_needs_rehash($hash, $algorithm, $options);
    }

	public function getInfo($hash)
    {
        return password_get_info($hash);
    }
}
