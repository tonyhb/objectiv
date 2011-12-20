<div class='span16'>
	<div class='page-header'>
		<a class='btn primary pull-right' href='<?php echo $base ?>/css/new'>New stylesheet</a>
		<h1>CSS <small>All stylesheets sorted by date modified</small></h1>
	</div>
</div>

<?php if (empty($css)): ?>
	<div class='alert-message block-message info'>
		<a class='close' href='#'>x</a>
		<p>You have no stylesheets at the moment. To create pages and content on your website you need to create a stylesheet.</p>

		<div class='alert-actions'>
			<a class='btn small' href='<?php echo $base ?>/css/new'>Create a stylesheet now</a>
		</div>
	</div>
<?php else: ?>
	<ul class='unstyled'>
	<?php foreach($css as $sheet): ?>
		<li><h3><a href="<?php echo $base ?>/css/edit/<?php echo $sheet['_id'] ?>"><?php echo $sheet['name'] ?></a></h3></li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
