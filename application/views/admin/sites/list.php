<h1>Choose a site</h1>

<ul>
<?php foreach(App::$user->sites as $site): ?>
	<li><a href="/admin/<?php echo $site['id'] ?>"><?php echo $site['name'] ?></a></li>
<?php endforeach; ?>
</ul>
