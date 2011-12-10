<div class='span16'>
	<div class='page-header'>
		<h1>Choose a site <small>from your sites available to manage</small></h1>
	</div>

	<ul class='unstyled'>
		<?php foreach(App::$user->sites as $site): ?>
		<li>
			<h3><a href="/admin/<?php echo $site['id'] ?>"><?php echo $site['name'] ?></a></h3>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
