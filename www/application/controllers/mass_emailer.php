<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mass_emailer extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		//----- This page requires login-----
		$this->load->library('UserAccess');
		$this->useraccess->LoginRequired();
		if(!$this->useraccess->HasRole(array('system admin','mailer',))) redirect('/', 'refresh');
		//-----------------------------------
	}

	public function index()
	{
		log_message('debug', __METHOD__.' Index called');
		// show the upload page
		$this->load->view('notifications_mailer');
	}

	public function send()
	{
		//////////////////
		// validate non-upload input
		//////////////////
		$this->load->library(array('form_validation','email'));
		$this->load->helper('email');
		$this->form_validation->set_rules('message_from_address', 'Message sender address', 'required|valid_email');
		$this->form_validation->set_rules('message_from_name', 'Message sender name', 'required|min_length[2]');
		$this->form_validation->set_rules('message_subject', 'Subject', 'required|min_length[2]');
		$this->form_validation->set_rules('message_body', 'Body', 'required|min_length[2]');
		$this->form_validation->set_rules('message_encoding', 'Encoding', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('upload_form');
			return TRUE;
		}

		$from_address = $this->input->post('message_from_address');
		$from_name = $this->input->post('message_from_name');
		$subject = $this->input->post('message_subject');
		$body = $this->input->post('message_body');
		$encoding = $this->input->post('message_encoding');
		$to_list_file_data = null;

		// fix the body line ending
		$temp_body = '';
		foreach(preg_split("/(\r?\n)/", $body) as $line){
		    $temp_body .= $line.chr(13).chr(10);
		}
		$body = $temp_body;

		$email_config['mailtype'] = 'text';
		if($encoding == 'html') $email_config['mailtype'] = 'html';
		$email_config['charset'] = 'utf-8';
		$email_config['crlf'] = '\r\n';
		$email_config['newline'] = '\r\n';
		$this->email->initialize($email_config);

		//////////////////
		//upload the file
		//////////////////
		$upload_config['upload_path'] = './uploads/';
		$upload_config['allowed_types'] = 'txt|csv';
		$upload_config['max_size']	= '1000000';

		$this->load->library('upload', $upload_config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $error);
			echo json_encode($error);//debug
		}
		else
		{
			$to_list_file_data = $this->upload->data();
			//echo $to_list_file_data['full_path']."<br/>\n";//debug
		}

		//////////////////
		//loop through the file
		//////////////////
		$results = "\n\n".'//// RESULTS /////'."\n";
		$handle = @fopen($to_list_file_data['full_path'], "r");
		if ($handle) {
		    while (($to_address = fgets($handle, 4096)) !== false) {
		        
		        //////////////////
				//send the email
				//////////////////
		    	if(!valid_email($to_address))
				{
					$results .= '"'.$to_address.'",Invalid Email address'."\n";
					continue;
				}

				$this->email->clear();
				$this->email->from($from_address, $from_name);
				$this->email->to($to_address); 

				$this->email->subject($subject);
				$this->email->message($body);
				
				if(isset($attachments))
				{
					if(!is_null($attachments))
					{
						foreach($attachments as $attachment)
						{
							if(file_exists($attachment)) $this->email->attach($attachment);
						}
					}
				}
				
				$this->email->send();

				$results .= '"'.$to_address.'",Sent'."\n";

				if(isset($print_debug))
				{
					if($print_debug)
					{
						echo $this->email->print_debugger();
					}
				}

				$this->email->clear(TRUE);

		    }
		    if (!feof($handle)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($handle);
		}

		//////////////////
		// let sender know the result
		//////////////////
		$user = $this->useraccess->CurrentUser();
		$results_file = './uploads/mass_mailing_results_'.$user->username.'_'.date("Ymd").'_'.rand().'.csv';
		$this->load->helper('file');
		write_file($results_file, $results);
		$this->email->clear();
		$this->email->from($from_address, $from_name);
		$this->email->to($user->email_address);
		$this->email->attach($results_file);
		$this->email->subject($subject);
		$this->email->message($body);
		$this->email->send();

		//////////////////
		// Return Output
		//////////////////

		$view_data = array(
			'message_title' => 'Mass Email Sent',
			'message_body' => 'Your email has been sent.  A copy has been sent to the email address we have on file for your account with the results attached.',
			'message_return' => 'Return to the <a href="'.site_url('').'">main page</a> or <a href="'.site_url('mass_emailer').'">send another email</a>.',
		);
		$this->load->view('generic_message',$view_data);

		
	}
}