<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" lang="en"><![endif]--> 
<!--[if IE 7 ]><html class="ie ie7 no-js" lang="en"><![endif]--> 
<!--[if IE 8 ]><html class="ie ie8 no-js" lang="en"><![endif]--> 
<!--[if IE 9 ]><html class="ie ie9 no-js" lang="en"><![endif]--> 
<!--[if gt IE 9]><!--><html class="no-js non-ie" lang="en"><!--<![endif]--> 
<head>
	<title><?php echo $title ?></title>
	<meta charset="utf-8">

	<?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), PHP_EOL, "\t" ?>

	<?php foreach ($meta as $name => $content) echo '<meta name="'.$name.'" content="'.$content.'">', PHP_EOL, "\t" ?>

</head>
<body>
<header class='topbar'>
	<nav class='fill'>
		<div class='container'>
			<ul class='nav' style='height: 40px'>
			<?php if (App::$user AND App_Auth::authorise_user(array('login', 'admin'))): ?>
				<li><a href="<?php echo $base ?>/pages">Pages</a></li>
					<?php if (App_Auth::authorise_user(array('view layouts', 'admin'))): ?>
						<li><a href="<?php echo $base?>/layouts">Layouts</a></li>
					<?php endif; ?>
			<?php endif; ?>
			</ul>
		</div>
	</nav>
</header>

<div class='container' style='padding-top: 40px'>
	<div class='content'>
		<?php echo $body ?>
	</div>
</div>

</body>
</html>
