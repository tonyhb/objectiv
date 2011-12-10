<h1>Layouts</h1>

<nav>
<a href="<?php echo $base ?>/layouts/new">New layout</a>
</nav>

<h2>Your layouts:</h2>

<ul>
<?php foreach($layouts as $layout): ?>
	<li><a href="<?php echo $base ?>/layouts/edit/<?php echo $layout->_id->{'$id'} ?>"><?php echo $layout->n ?></a></li>
<?php endforeach; ?>
