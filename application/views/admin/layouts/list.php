<div class='span16'>
	<div class='page-header'>
		<a class='btn primary pull-right' href='<?php echo $base ?>/layouts/new'>New layout</a>
		<h1>Layouts <small>All layouts sorted by date modified</small></h1>
	</div>
</div>

<?php if (empty($layouts)): ?>
	
	<div class='alert-message block-message info'>
		<a class='close' href='#'>x</a>
		<p>You have no layouts at the moment. To create pages and content on your website you need to create a HTML layout.</p>

		<div class='alert-actions'>
			<a class='btn small' href='<?php echo $base ?>/layouts/new'>Create a layout now</a>
		</div>
	</div>
<?php else: ?>
	<ul class='unstyled'>
	<?php foreach($layouts as $layout): ?>
		<li><a href="<?php echo $base ?>/layouts/edit/<?php echo $layout->_id->{'$id'} ?>"><?php echo $layout->n ?></a></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
