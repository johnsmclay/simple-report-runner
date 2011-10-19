<?php $this->load->view('dependencies/header',array(
	'title' => 'User Administration',
	'header1' => 'The Reporting Dashboard',
	'header2' => 'Edit Account',
)); ?>
		<p><?=validation_errors()?></p>
		<?=form_open('pglmsweeklies/requestReport')?>
		<?=form_submit('submit', 'Run Report')?>
		<?=form_close()?>
<?php $this->load->view('dependencies/footer'); ?>