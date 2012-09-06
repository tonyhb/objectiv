<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6 no-js" lang="en"><![endif]--> 
<!--[if IE 7 ]><html class="ie ie7 no-js" lang="en"><![endif]--> 
<!--[if IE 8 ]><html class="ie ie8 no-js" lang="en"><![endif]--> 
<!--[if IE 9 ]><html class="ie ie9 no-js" lang="en"><![endif]--> 
<!--[if gt IE 9]><!--><html class="no-js non-ie" lang="en"><!--<![endif]--> 
<head>
	<title><?php echo isset($title) ? $title : 'ObjectivWeb'; ?></title>
	<meta charset="utf-8">
	<?php 
		if ( ! isset($meta)) $meta = array();
		foreach ($meta as $name => $content) {
			echo PHP_EOL, "\t", '<meta name="'.$name.'" content="'.$content.'">';
		}
	?>

	<link type="text/css" href="/css/admin.css" rel="stylesheet" media="all" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,800italic,400,700,800' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="app">
		<header><div class='container' id='header-content'></div></header>
		<div class='container'><?php echo isset($body) ? $body : '' ?></div>
	</div>

	<script data-main="/assets/js/config" src="/assets/js/libs/require.min.js"></script>
	<script>
		var Seed = {
			sites: <?php echo json_encode($sites) ?>
		};
	</script>
</body>
</html>
