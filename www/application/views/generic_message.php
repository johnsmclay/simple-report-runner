<?php $this->load->view('dependencies/header', array(
		'title' => $message_title,
		'header1' => 'The Reporting Dashboard',
		'header2' => $message_title,
)); ?>

<p><?=$message_body?></a></p><br/>

<p><?=$message_return?></p>

<?php $this->load->view('dependencies/footer');?>