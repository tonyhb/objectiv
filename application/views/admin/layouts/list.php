<div class='span16'>
	<div class='page-header'>
		<a class='btn primary pull-right' href='<?php echo $base ?>/layouts/new'>New layout</a>
		<h1>Layouts <small>All layouts sorted by date modified</small></h1>
	</div>
</div>

<h2>Your layouts:</h2>

<ul class='unstyled'>
	<?php foreach($layouts as $layout): ?>
		<li><a href="<?php echo $base ?>/layouts/edit/<?php echo $layout->_id->{'$id'} ?>"><?php echo $layout->n ?></a></li>
	<?php endforeach; ?>
</ul>
