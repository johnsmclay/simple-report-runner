<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'User Administration',
)); ?>
						
						<?php echo $this->table->generate($user_list); ?>
						<br />
						<h2>Add User</h2>
						<p><?=validation_errors()?></p>
						<?=form_open('useradmin/saveaccount')?><br />
						<?=form_label('First Name','fname')?><?=form_input('fname',set_value('fname'))?><br />
						<?=form_label('Last Name','lname')?><?=form_input('lname',set_value('lname'))?><br />
						<?=form_label('Username','username')?><?=form_input('username',set_value('username'))?><br />
						<?=form_label('Email Address','email_address')?><?=form_input('email_address',set_value('email_address'))?><br />
						<?=form_label('Password','password')?><?=form_password('password','')?><br />
						<?=form_label('Confirm Password','confirm_password')?><?=form_password('confirm_password','')?><br />
						<?=form_submit('submit', 'Create')?>
						<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>