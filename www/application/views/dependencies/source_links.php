		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="<?=base_url();?>assets/javascript/jquery-ui-1.8.15.custom.min.js"></script>
		<script type="text/javascript" src="<?=base_url();?>assets/javascript/jquery.collapse.js"></script>
		<script type="text/javascript" src="<?=base_url();?>assets/javascript/jquery.scrollbarTable-0.1.js"></script>
		<script type="text/javascript" src="<?=base_url();?>assets/javascript/main.js"></script>
		
		<?
		// This will allow you to name a JavaScript file the same as your controller (i.e. controllerName.js)
		// and will automatically load it. for you.
		if (file_exists('assets/javascript/' . $this->router->class . '.js')) {
		?>
		<script type="text/javascript" src="<?=base_url();?>assets/javascript/<?=$this->router->class;?>.js"></script>
		<?}?>
		
		<link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/css/main.css" />
		<? // Same as above, but for CSS files.
		if (file_exists('assets/css/' . $this->router->class . '.css')) {
		?>
		<link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/css/<?=$this->router->class;?>.css" />
		<?}?>
		<link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/css/jquery_ui_smoothness/jquery-ui-1.8.15.custom.css" />
