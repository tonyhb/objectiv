<h1>New layout</h1>

<ul>
<?php foreach($errors as $field => $error): ?>
	<li><?php echo $error ?></li>
<?php endforeach; ?>
</ul>

<form action="" method="post">

	<label for="name">Layout name</label>
	<input type="text" placeholder="Layout name" name="name" />

	<label for="content">Code</label>
	<textarea name="content" style="display: block; width:100%; height: 1000px;"></textarea>

	<input type="hidden" name="token" value="<?php echo App::$user->original('csrf') ?>" />
	<button type="submit">Create layout</button>
</form>
