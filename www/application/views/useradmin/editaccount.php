<?php $this->load->view('dependencies/header',array(
	'title' => 'Account Profile',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'Edit Account',
)); ?>
						<p><?=validation_errors()?></p>
						<?=form_open('useradmin/saveaccount',array('autocomplete' => 'off'),$hidden_fields)?><br />
						<div id="table-container">
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('First Name','fname')?></div>
								<div id="table-column-right"><?=form_input('fname',$user->fname)?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Last Name','lname')?></div>
								<div id="table-column-right"><?=form_input('lname',$user->lname)?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Username','username')?></div>
								<div id="table-column-right"><?=form_input('username',$user->username)?></div>
							 </div>
							 <div id="table-row">
								<div id="table-column-left"><?=form_label('Email Address','email_address')?></div>
								<div id="table-column-right"><?=form_input('email_address',$user->email_address)?></div>
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
								<div id="table-column-left">
									<?=form_submit('submit', 'Save')?>
									<?=form_close()?>
								</div>
								<div id="table-column-right">
									<?php if ($this->useraccess->HasRole(array('user admin','system admin'))): ?>
										<?=form_open('useradmin/deleteaccount','',$hidden_fields)?>
										<?=form_submit('submit', 'Delete')?>
										<?=form_close()?>
									<?php endif; ?>
								</div>
							 </div>
						</div>
						<?=form_close()?>

						<br />
						<h2>Roles</h2>
						<p><?=validation_errors()?></p>
						<?=form_open('useradmin/saveroles','',$hidden_fields)?><br />
						<?php foreach ($available_roles as $role_id => $role): ?>
							<?php $checked = array_key_exists($role_id,$user_roles) ? TRUE : FALSE; ?>
							<?php
							$checkbox_properties = array(
								'name' => 'roles[]',
								'value' => $role_id,
								'checked' => $checked,
							);
							if(!$this->useraccess->HasRole(array('system admin','user admin',))) $checkbox_properties['disabled'] = 'TRUE';
							if(!$this->useraccess->HasRole(array('system admin')) && $role == 'system admin') $checkbox_properties['disabled'] = 'TRUE';
							if(!$this->useraccess->HasRole(array('system admin')) && $role == 'report admin') $checkbox_properties['disabled'] = 'TRUE';
							?>
							<?=form_checkbox($checkbox_properties)?> <?=$role?><br />
						<?php endforeach; ?>
						<?php if ($this->useraccess->HasRole(array('user admin','system admin'))): ?>
							<?=form_submit('submit', 'Save')?>
						<?php endif; ?>
						<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>