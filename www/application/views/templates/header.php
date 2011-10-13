<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?=$title?></title>
		<? $this->load->view('dependencies/source_links'); ?>
	</head>
	<body>
		<div id="wrapper">
			<img id="compLogo" src='<?=base_url();?>/assets/images/compLogo.png' title='Company Logo' />
			<header>
				<h1 class="arial center"><?=$header1?></h1>
			</header>
			<div class="clear"></div> <!-- clear floats -->	
			<?php
				$this->load->view('dependencies/navigation_menu');
			?>
			
			<div id="main">
				<div id="formElements">
					<div id="createReports" class="section">
						<h2><?=$header2?></h2>