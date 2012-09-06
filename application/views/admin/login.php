<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" lang="en"><![endif]--> 
<!--[if IE 7 ]><html class="ie ie7 no-js" lang="en"><![endif]--> 
<!--[if IE 8 ]><html class="ie ie8 no-js" lang="en"><![endif]--> 
<!--[if IE 9 ]><html class="ie ie9 no-js" lang="en"><![endif]--> 
<!--[if gt IE 9]><!--><html class="no-js non-ie" lang="en"><!--<![endif]--> 
<head>
	<title><?php echo isset($title) ? $title : 'ObjectivWeb'; ?></title>
	<meta charset="utf-8">
	<?php foreach ($meta as $name => $content) echo '<meta name="'.$name.'" content="'.$content.'">', PHP_EOL, "\t" ?>
	<link type="text/css" rel="stylesheet" media="all" href="/css/screen.css" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,800italic,400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
<div class='container'>
	<section id='sign-in'>
		<h1>Sign in to your account</h1>
		<form action="" method="post">
			<div>
				<input type="email" name="email" placeholder="Email address">
			</div>
			<div>
				<input type="password" name="password" placeholder="Password">
			</div>
			<input type="submit" value="Sign in" class='grey-button'>
		</form>

		<a href='#'>Forgotten your password?</a>
	</section>
</div>
</body>
</html>
