<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'Edit Account',
)); ?>
						<p><?=validation_errors()?></p>
						<?=form_open('useradmin/saveaccount','',$hidden_fields)?><br />
						<?=form_label('First Name','fname')?><?=form_input('fname',$user->fname)?><br />
						<?=form_label('Last Name','lname')?><?=form_input('lname',$user->lname)?><br />
						<?=form_label('Username','username')?><?=form_input('username',$user->username)?><br />
						<?=form_label('Email Address','email_address')?><?=form_input('email_address',$user->email_address)?><br />
						<?=form_label('New Password','password')?><?=form_password('password','')?><br />
						<?=form_label('Confirm New Password','confirm_password')?><?=form_password('confirm_password','')?><br />
						<?=form_submit('submit', 'Save')?>
						<?=form_close()?>
						<?=form_open('useradmin/deleteaccount','',$hidden_fields)?>
						<?=form_submit('submit', 'Delete')?>
						<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>