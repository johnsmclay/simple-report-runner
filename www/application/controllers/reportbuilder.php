<?php
	class Reportbuilder extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			$this->load->model('reportbuilder_model','report');
		}
		
		function index()
		{
			$this->load->view('reportbuilder_view');
		}
	}
?>