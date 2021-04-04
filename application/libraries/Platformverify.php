<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Platformverify
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /*İOS Platform Doğrulama Dış Servis Varsayalım*/
	public function ios_verify(string $receipt)
	{
		$username = $this->ci->output->get_header('username');
		$password = $this->ci->output->get_header('password');
		// Dökümüanda header dan gelen username ve pass ile ne yapılacağı yazılmadığından dolayı işlem yapmadım. Auth tarzı bir durum olacağını düşünüyorum.

		$platform_verify = (substr($receipt, -1 , 1) % 2 != 0) ? true : false;
		if ($platform_verify == true) {
			$return = array(
				"status" => "true",
				"expired_at" => date("Y-m-d H:i:s",strtotime("+1 year")),
			);
		} else {
			$return = array(
				"status" => "false",
				"expired_at" => null,
			);
		}
		return $return;
	}

	/*Google Platform Doğrulama Dış Servis Varsayalım*/
	public function google_verify(string $receipt)
	{
		$username = $this->ci->output->get_header('username');
		$password = $this->ci->output->get_header('password');
		// Dökümüanda header dan gelen username ve pass ile ne yapılacağı yazılmadığından dolayı işlem yapmadım. Auth tarzı bir durum olacağını düşünüyorum.

		$platform_verify = (substr($receipt, -1 , 1) % 2 != 0) ? true : false;		
		if ($platform_verify == true) {
			$return = array(
				"status" => "true",
				"expired_at" => date("Y-m-d H:i:s",strtotime("+1 year")),
			);
		} else {
			$return = array(
				"status" => "false",
				"expired_at" => null,
			);
		}
		return $return;
	}

}