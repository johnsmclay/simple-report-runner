<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login</title>
</head>
<body>
<h1>Please Log In</h1>
<p><?=$error?></p>
<?=form_open('login/submit','',$hidden)?>
Username: <?=form_input('username', $username)?><br/>
Password: <?=form_password('password')?><br/>
<?=form_submit('submit','Login')?>
<?=form_close()?>
</body>
</html>