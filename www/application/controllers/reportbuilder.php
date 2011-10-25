<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Reportbuilder extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('reportbuilder_model','report');
			//----- This page requires login-----
			$this->load->library('UserAccess');
			$this->useraccess->LoginRequired();
			if(!$this->useraccess->HasRole(array('system admin','report admin',))) redirect('/', 'refresh');
			//-----------------------------------
		}
		
		function index()
		{
			$this->load->view('customreports/reportbuilder_view');
		}
	}
?>