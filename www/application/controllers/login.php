<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * index method is the default method for the controller
	 * access by calling /login , /login/ , /login/index , or /login/index/
	 *
	 *
	 * @result bool $successful
	 */
	public function index()
	{
		// we want the default action to be to send the user to the login form
		$this->form();
	}
	
	/**
	 * form method displays login form 
	 * access by calling /login/form or /login/form/
	 *
	 * @result bool $successful
	 */
	public function form()
	{	
		// we are going to be using session info
		$this->load->library(array('session','UserAccess'));

		$user_id = $this->session->userdata('user_id');
		if(isset($user_id) && $user_id != '') redirect('/', 'refresh');
		
		// holds data used in the view
		$view_data = array();
		
		// holds hidden fields in the form
		$view_data['hidden'] = array();
		
		// pull the attempted page data if available
		$last_page = $this->session->flashdata('last_page');
		if(!is_null($last_page)) $view_data['hidden']['last_page'] = $last_page;
		
		// pull username if available
		$username = $this->session->flashdata('username');
		if(!is_null($username)) $view_data['username'] = $username;
		
		// pull error message if available
		$error = $this->session->flashdata('error');
		if(!is_null($error)) $view_data['error'] = $error;
		
		//load the page
		$this->load->helper(array('form','url'));
		$this->load->view('login/form',$view_data);
	}
	
	public function submit()
	{
		log_message('debug', __METHOD__.' called with username "'.$_REQUEST['username'].'", password "'.$_REQUEST['password'].'".');
		
		$destination = '/';
		if(isset($_REQUEST['last_page']) && $_REQUEST['last_page'] != '') $destination = $_REQUEST['last_page'];
		
		$this->load->library(array('form_validation'));
		
		// login form validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[1]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[1]|xss_clean');
		$this->form_validation->set_rules('last_page', 'last page', 'trim|xss_clean');
		
		if(!$this->form_validation->run())
		{
			log_message('error', __METHOD__.' form data failed validation');
			$this->load->helper(array('url'));
			$this->load->library(array('session'));
			$this->session->set_flashdata('error', 'Both username and password are required.');
			$this->session->set_flashdata('last_page', $destination);
			redirect('/login/form/','refresh');
		}
		
		$this->load->library('UserAccess');
		if(!$this->useraccess->login($_REQUEST['username'],$_REQUEST['password']))
		{
			log_message('error', __METHOD__.' credentials failed account validation');
			$this->load->helper(array('url'));
			$this->load->library(array('session'));
			$this->session->set_flashdata('error', 'Username or password were incorrect.');
			$this->session->set_flashdata('last_page', $destination);
			redirect('/login/form/','refresh');
		}
		
		$destination = '/';
		if(isset($_REQUEST['last_page']) && $_REQUEST['last_page'] != '') $destination = $_REQUEST['last_page'];
		log_message('debug', __METHOD__.' all logged in! Forwarding user to: '.$destination);
		redirect($destination,'refresh');
	}
	
	public function token()
	{
		$token = $this->uri->segment(3);
		log_message('debug', __METHOD__.' called with token "'.$token.'".');
		
		$this->load->library('UserAccess');
		if(!$this->useraccess->TokenLogin($token))
		{
			log_message('error', __METHOD__.' credentials failed account validation');
			$this->load->helper(array('url'));
			$this->load->library(array('session'));
			$this->session->set_flashdata('error', 'Token Invalid.');
			redirect('/login/form/','refresh');
		}
		
		$destination = '/';
		log_message('debug', __METHOD__.' all logged in! Forwarding user to: '.$destination);
		redirect($destination,'refresh');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */