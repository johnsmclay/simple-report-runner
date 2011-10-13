<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'User Administration',
)); ?>
						
						<?php
						echo form_open('useradmin/adduser');
						foreach($active_users as $active_user)
						{
							
						}
						?>
<?php $this->load->view('dependencies/footer'); ?>