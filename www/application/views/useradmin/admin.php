<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'User Administration',
)); ?>
						
						<?php echo $this->table->generate($user_list); ?>
						<br />
						<h2>Add User</h2>
						<p><?=validation_errors()?></p>
						<?=form_open('useradmin/saveaccount')?>
						<div id="table-container">
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('First Name','fname')?></div>
								<div id="table-column-right"><?=form_input('fname',set_value('fname'))?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Last Name','lname')?></div>
								<div id="table-column-right"><?=form_input('lname',set_value('lname'))?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Username','username')?></div>
								<div id="table-column-right"><?=form_input('username',set_value('username'))?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Email Address','email_address')?></div>
								<div id="table-column-right"><?=form_input('email_address',set_value('email_address'))?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Password','password')?></div>
								<div id="table-column-right"><?=form_password('password','')?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Confirm Password','confirm_password')?></div>
								<div id="table-column-right"><?=form_password('confirm_password','')?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_submit('submit', 'Create')?></div>
								<div id="table-column-right"></div>
							 </div>
						</div>
						<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>