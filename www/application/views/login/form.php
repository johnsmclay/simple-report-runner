<! DOCTYPE>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>MIL Reports -- Login</title>
		<? $this->load->view('dependencies/source_links');?>
	</head>
	<body>
		<div id="wrapper">
			<div style="width:100%; min-width:100%;">
				<img id="compLogo" src='<?=base_url();?>/assets/images/compLogo.png' title='Company Logo' />
				<header>
					<h1 class="arial center">The Reporting Dashboard</h1>
				</header>
			</div>
			<div class="clear"></div>
			<!-- clear floats -->
			<div id="holder">
				<div id="loginForm">
					<p>
						<?=$error
						?>
					</p>
					<?=form_open('login/submit','',$hidden)
					?>
					<fieldset>
						<legend>
							Please Log In
						</legend>
						<ul>
							<li>
								<label>Username: </label><?=form_input('username', $username);?>
							</li>
							<li>
								<label>Password: </label><?=form_password('password');?>
							</li>
							<li>
								<?
									$submit = array(
											'name' => 'submit',
											'value' => 'Login',
											'class' => 'loginBtn'
									);
									echo form_submit($submit);
								?>
							</li>
						</ul>
					</fieldset>
					<?=form_close();?>
				</div>
			</div>
		</div>
	</body>
</html>