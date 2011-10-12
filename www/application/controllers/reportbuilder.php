<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Reportbuilder extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('reportbuilder_model','report');
			//----- This page requires login-----
			$this->load->library('UserAccess');
			$this->useraccess->LoginRequired();
			//-----------------------------------
		}
		
		function index()
		{
			$this->load->view('customreports/reportbuilder_view');
		}
	}
?>