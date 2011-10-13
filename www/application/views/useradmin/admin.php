<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'User Administration',
)); ?>
						
						<?php echo $this->table->generate($user_list); ?>
						<br />
						<h2>Add User</h2>
						<?=form_open('useradmin/saveaccount')?><br />
						<?=form_label('First Name','fname')?><?=form_input('fname','')?><br />
						<?=form_label('Last Name','lname')?><?=form_input('lname','')?><br />
						<?=form_label('Username','username')?><?=form_input('username','')?><br />
						<?=form_label('Email Address','email_address')?><?=form_input('email_address','')?><br />
						<?=form_label('Password','new_password')?><?=form_password('new_password','')?><br />
						<?=form_label('Confirm Password','confirm_password')?><?=form_password('confirm_password','')?><br />
						<?=form_submit('submit', 'Create')?>
						<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>