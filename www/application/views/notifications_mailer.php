<?php $this->load->view('dependencies/header', array(
		'title' => 'Mass Email Tool',
		'header1' => 'The Reporting Dashboard',
		'header2' => 'Mass Email Tool',
)); ?>

<?=validation_errors()?>

<?=form_open_multipart('mass_emailer/send',array('accept-charset'=>'UTF-8',))?>

From Name:<br/><?=form_input(array('size'=>50,'name'=>'message_from_name','value'=>set_value('message_from_name', 'Support')))?><br/><br/>
From address:<br/><?=form_input(array('size'=>50,'name'=>'message_from_address','value'=>set_value('message_from_address', 'services@temp.com')))?><br/><br/>
Subject:<br/><?=form_input(array('size'=>100,'name'=>'message_subject','value'=>set_value('message_subject', 'Scheduled System Maintenance Notification')))?><br/><br/>
Body:<br/><textarea rows="20" cols="100" name="message_body" /></textarea><br/>
<input type="radio" name="message_encoding" value="plaintext" checked="checked" /> Plain
<input type="radio" name="message_encoding" value="html" /> HTML<br/><br/>
List of email addresses:<br/><input type="file" name="userfile" size="20" />

<br /><br />

<input type="submit" value="Send" />

</form>
<br /><br />
<?php $this->load->view('dependencies/footer');?>
