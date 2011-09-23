<?php
class Blog extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->helper(array('url','html'));
	}

	public function index()
	{
		$data['todo_list'] = array('Clean House', 'Call Mom', 'Run Errands');
		$data['title'] = "My Real Title";
		$data['heading'] = "My Real Heading";

		$this->load->view('blog_view', $data);
	}
}
?>