<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Myclasses {
	/**
	* includes the directory application/my_classes/Classes in your includes directory
	* 
	*/
	function index() {
		//CodeIgniter 2.0.2
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . BASEPATH . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'my_classes' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR);
	}
}
?>